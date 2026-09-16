<?php

namespace App\Models;

use App\Models\Concerns\MencatatPelaku;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pemohon extends Model
{
    use MencatatPelaku, SoftDeletes;

    /** Berkas ditolak sebanyak ini berarti pemohon tidak boleh mengirim ulang. */
    public const BATAS_DITOLAK = 3;

    /**
     * Isian Data Pemohon yang wajib ada sebelum verifikasinya boleh disetujui.
     *
     * Daftarnya sengaja sama persis dengan yang dituntut formulir Data Pemohon
     * di situs publik (`fe-ppid`, `Akun\PengaturanController::simpanDataPemohon`).
     * Kalau keduanya berbeda, panel bisa menyetujui berkas yang menurut situs
     * belum lengkap — dan sebaliknya.
     */
    public const WAJIB_VERIFIKASI = [
        'nama' => 'Nama',
        'nik' => 'NIK (16 digit angka)',
        'jenis_pemohon' => 'Jenis pemohon',
        'pekerjaan' => 'Pekerjaan',
        'alamat' => 'Alamat',
        'nama_lembaga' => 'Nama lembaga',
        'file_ktp' => 'Berkas KTP',
    ];

    /**
     * Jenis pemohon yang berdiri atas nama diri sendiri, jadi tidak perlu
     * menyebut lembaga. Dua nilai karena keduanya sah di basis data: `pribadi`
     * kosakata lama panel, `perorangan` kosakata portal pengguna.
     */
    private const JENIS_PERORANGAN = ['pribadi', 'perorangan'];

    protected $table = 'pemohon';

    protected $fillable = [
        'nik',
        'nama',
        'email',
        'no_hp',
        'alamat',
        'pekerjaan',
        'jenis_pemohon',
        'nama_lembaga',
    ];

    /**
     * NIK dan password tidak pernah ikut response API: NIK data pribadi,
     * password hash tidak ada urusannya dengan panel admin.
     */
    protected $hidden = [
        'password',
        'nik',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'tanggal_verifikasi' => 'datetime',
        'jumlah_ditolak' => 'integer',
    ];

    /**
     * Ditambahkan ke setiap response supaya panel tidak perlu mengulang aturan
     * "sudah tiga kali ditolak" sendiri — satu tempat, satu kebenaran.
     */
    protected $appends = ['verifikasi_diblokir', 'sisa_kesempatan', 'kekurangan_data', 'data_lengkap'];

    public function permohonan(): HasMany
    {
        return $this->hasMany(PermohonanInformasi::class, 'pemohon_id');
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    public function getVerifikasiDiblokirAttribute(): bool
    {
        return (int) $this->jumlah_ditolak >= self::BATAS_DITOLAK;
    }

    public function getSisaKesempatanAttribute(): int
    {
        return max(0, self::BATAS_DITOLAK - (int) $this->jumlah_ditolak);
    }

    /**
     * Isian Data Pemohon yang masih kosong, sebagai label siap tampil.
     *
     * Dipakai dua kali: menahan persetujuan di server, dan memberi tahu petugas
     * di panel apa yang membuat berkasnya belum bisa disetujui. Satu tempat,
     * supaya keduanya tidak pernah menyebut daftar yang berbeda.
     *
     * @return list<string>
     */
    public function getKekuranganDataAttribute(): array
    {
        $jenis = (string) $this->getAttribute('jenis_pemohon');
        $kurang = [];

        foreach (self::WAJIB_VERIFIKASI as $kolom => $label) {
            // Perorangan tidak mewakili siapa pun selain dirinya; menuntut nama
            // lembaga darinya berarti menahan berkas yang sudah lengkap.
            if ($kolom === 'nama_lembaga' && in_array($jenis, self::JENIS_PERORANGAN, true)) {
                continue;
            }

            if (blank($this->getAttribute($kolom))) {
                $kurang[] = $label;
                continue;
            }

            // NIK KTP selalu 16 digit angka. Isian yang lebih pendek bukan
            // "sudah diisi" — tidak ada KTP yang bisa dicocokkan dengannya.
            if ($kolom === 'nik' && preg_match('/^[0-9]{16}$/', (string) $this->getAttribute('nik')) !== 1) {
                $kurang[] = $label;
            }
        }

        return $kurang;
    }

    /** Data Pemohon lengkap — syarat sebuah verifikasi boleh disetujui. */
    public function getDataLengkapAttribute(): bool
    {
        return $this->kekurangan_data === [];
    }
}
