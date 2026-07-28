<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\AuditLogger;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Basis CRUD untuk seluruh modul CMS.
 *
 * Semua turunan hanya mendeklarasikan konfigurasi (model, kolom yang boleh
 * dicari/diurutkan/difilter, aturan validasi). Query string dari klien tidak
 * pernah dipakai langsung sebagai nama kolom — selalu dicocokkan dulu ke
 * daftar putih di kelas turunan, supaya tidak ada jalur injeksi lewat
 * parameter `sort`/`filter`.
 */
abstract class CrudController extends Controller
{
    /** FQCN model Eloquent yang dikelola. */
    protected string $model;

    /** Slug modul di tabel `modul_sistem`, dipakai untuk audit log. */
    protected string $modulSlug;

    /** Kolom teks yang ikut dicari lewat parameter `search`. */
    protected array $searchable = [];

    /** Kolom yang boleh dipakai sebagai pengurutan. */
    protected array $sortable = ['id'];

    /** Pengurutan bawaan; awalan `-` berarti menurun. */
    protected string $defaultSort = '-id';

    /** Relasi yang di-eager load pada daftar. */
    protected array $withList = [];

    /** Relasi yang di-eager load pada detail. */
    protected array $withDetail = [];

    /**
     * Kolom yang boleh difilter: nama kolom => tipe.
     * Tipe: exact, boolean, date, date_range, tahun.
     */
    protected array $filterable = [];

    /** Kolom sumber slug otomatis (mis. 'judul'); null = tidak ada slug. */
    protected ?string $slugFrom = null;

    /** Kolom slug tujuan. */
    protected string $slugColumn = 'slug';

    /** Kolom yang tidak boleh muncul di audit log (kredensial/token). */
    protected array $auditHidden = ['password', 'token_unduhan', 'remember_token'];

    /** Batas maksimum baris per halaman. */
    protected int $maxPerPage = 100;

    /**
     * Aturan validasi.
     *
     * @param  string  $mode  'create' atau 'update'
     */
    abstract protected function rules(string $mode, ?Model $record): array;

    // ---------------------------------------------------------------------
    // Endpoint
    // ---------------------------------------------------------------------

    public function index(Request $request): JsonResponse
    {
        $query = $this->baseQuery()->with($this->withList);

        $this->applySearch($query, (string) $request->query('search', ''));
        $this->applyFilters($query, $request);
        $this->applySort($query, (string) $request->query('sort', $this->defaultSort));

        $perPage = min(max((int) $request->query('per_page', 15), 1), $this->maxPerPage);
        $page = max((int) $request->query('page', 1), 1);

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $paginator->items(),
            'meta' => [
                'total' => $paginator->total(),
                'page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $record = $this->baseQuery()->with($this->withDetail ?: $this->withList)->findOrFail($id);

        return response()->json(['data' => $record]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->rules('create', null));

        $record = DB::transaction(function () use ($data, $request) {
            /** @var Model $record */
            $record = new $this->model();
            $payload = $this->beforeSave($this->withSlug($data, null), $request, null);

            $record->fill($payload);
            $record->save();

            $this->afterSave($record, $request, 'create');

            return $record;
        });

        AuditLogger::record(
            Auth::guard('api')->id(),
            'create',
            $this->model,
            $record->getKey(),
            null,
            $this->scrub($record->getAttributes())
        );

        return response()->json([
            'data' => $record->fresh($this->withDetail ?: $this->withList),
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        /** @var Model $record */
        $record = $this->baseQuery()->findOrFail($id);
        $sebelum = $this->scrub($record->getAttributes());

        $data = $request->validate($this->rules('update', $record));

        DB::transaction(function () use (&$record, $data, $request) {
            $payload = $this->beforeSave($this->withSlug($data, $record), $request, $record);

            $record->fill($payload);
            $record->save();

            $this->afterSave($record, $request, 'update');
        });

        AuditLogger::record(
            Auth::guard('api')->id(),
            'update',
            $this->model,
            $record->getKey(),
            $sebelum,
            $this->scrub($record->getAttributes())
        );

        return response()->json([
            'data' => $record->fresh($this->withDetail ?: $this->withList),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        /** @var Model $record */
        $record = $this->baseQuery()->findOrFail($id);
        $sebelum = $this->scrub($record->getAttributes());

        $this->beforeDelete($record);

        $record->delete();

        AuditLogger::record(
            Auth::guard('api')->id(),
            'delete',
            $this->model,
            $record->getKey(),
            $sebelum,
            null
        );

        return response()->json(['message' => 'Data dihapus']);
    }

    /**
     * Hapus banyak baris sekaligus (dipakai aksi massal di tabel admin).
     */
    public function destroyMany(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'max:200'],
            'ids.*' => ['integer'],
        ]);

        $terhapus = 0;

        DB::transaction(function () use ($data, &$terhapus) {
            foreach ($this->baseQuery()->whereIn('id', $data['ids'])->get() as $record) {
                $sebelum = $this->scrub($record->getAttributes());

                $this->beforeDelete($record);
                $record->delete();
                $terhapus++;

                AuditLogger::record(
                    Auth::guard('api')->id(),
                    'delete',
                    $this->model,
                    $record->getKey(),
                    $sebelum,
                    null
                );
            }
        });

        return response()->json(['message' => "{$terhapus} data dihapus", 'deleted' => $terhapus]);
    }

    // ---------------------------------------------------------------------
    // Kait untuk turunan
    // ---------------------------------------------------------------------

    /**
     * Ubah payload sebelum disimpan (mis. isi kolom pelaku perubahan).
     */
    protected function beforeSave(array $data, Request $request, ?Model $record): array
    {
        return $data;
    }

    protected function afterSave(Model $record, Request $request, string $mode): void
    {
    }

    /**
     * Kesempatan menolak penghapusan (mis. data masih dirujuk baris lain).
     */
    protected function beforeDelete(Model $record): void
    {
    }

    protected function baseQuery(): Builder
    {
        return $this->model::query();
    }

    // ---------------------------------------------------------------------
    // Internal
    // ---------------------------------------------------------------------

    protected function applySearch(Builder $query, string $search): void
    {
        $search = trim($search);

        if ($search === '' || $this->searchable === []) {
            return;
        }

        $query->where(function (Builder $q) use ($search) {
            foreach ($this->searchable as $kolom) {
                // ILIKE = pencarian tanpa peduli huruf besar/kecil di PostgreSQL.
                $q->orWhere($kolom, 'ilike', '%'.$search.'%');
            }
        });
    }

    protected function applyFilters(Builder $query, Request $request): void
    {
        foreach ($this->filterable as $kolom => $tipe) {
            $nilai = $request->query($kolom);

            if ($nilai === null || $nilai === '') {
                continue;
            }

            match ($tipe) {
                'boolean' => $query->where($kolom, filter_var($nilai, FILTER_VALIDATE_BOOLEAN)),
                'date' => $query->whereDate($kolom, $nilai),
                'date_from' => $query->whereDate($kolom, '>=', $nilai),
                'date_to' => $query->whereDate($kolom, '<=', $nilai),
                default => $query->where($kolom, $nilai),
            };
        }

        // Rentang tanggal umum untuk semua modul yang punya created_at.
        if ($request->filled('created_from')) {
            $query->whereDate('created_at', '>=', $request->query('created_from'));
        }

        if ($request->filled('created_to')) {
            $query->whereDate('created_at', '<=', $request->query('created_to'));
        }
    }

    protected function applySort(Builder $query, string $sort): void
    {
        $sort = $sort !== '' ? $sort : $this->defaultSort;
        $arah = str_starts_with($sort, '-') ? 'desc' : 'asc';
        $kolom = ltrim($sort, '-');

        if (!in_array($kolom, $this->sortable, true)) {
            $kolom = ltrim($this->defaultSort, '-');
            $arah = str_starts_with($this->defaultSort, '-') ? 'desc' : 'asc';
        }

        $query->orderBy($kolom, $arah);
    }

    /**
     * Isi slug dan pastikan unik dengan sufiks angka.
     *
     * Saat update, slug hanya berubah jika editor mengisinya sendiri. Slug
     * tidak ikut berubah ketika judul diedit, supaya tautan yang sudah
     * tersebar di situs publik tidak mati.
     */
    protected function withSlug(array $data, ?Model $record): array
    {
        if ($this->slugFrom === null) {
            return $data;
        }

        $slugManual = trim((string) ($data[$this->slugColumn] ?? ''));

        if ($slugManual !== '') {
            $data[$this->slugColumn] = $this->slugUnik(Str::slug($slugManual), $record);

            return $data;
        }

        if ($record !== null) {
            unset($data[$this->slugColumn]); // pertahankan slug lama

            return $data;
        }

        $sumber = $data[$this->slugFrom] ?? null;

        if (!$sumber) {
            return $data;
        }

        $data[$this->slugColumn] = $this->slugUnik(Str::slug($sumber), null);

        return $data;
    }

    protected function slugUnik(string $dasar, ?Model $record): string
    {
        $dasar = $dasar !== '' ? $dasar : 'item';
        $kandidat = $dasar;
        $n = 2;

        while ($this->slugTerpakai($kandidat, $record)) {
            $kandidat = $dasar.'-'.$n;
            $n++;
        }

        return $kandidat;
    }

    protected function slugTerpakai(string $slug, ?Model $record): bool
    {
        $query = $this->model::query()->where($this->slugColumn, $slug);

        if (method_exists($this->model, 'bootSoftDeletes')) {
            $query->withTrashed();
        }

        if ($record !== null) {
            $query->whereKeyNot($record->getKey());
        }

        return $query->exists();
    }

    /**
     * Buang kolom sensitif sebelum masuk audit_log.
     */
    protected function scrub(array $attributes): array
    {
        foreach ($this->auditHidden as $kolom) {
            unset($attributes[$kolom]);
        }

        return $attributes;
    }
}
