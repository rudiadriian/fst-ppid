<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\CrudController;
use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

/**
 * Audit log hanya bisa dibaca. Tidak ada jalur tulis/hapus dari API supaya
 * jejak audit tetap sah sebagai bukti.
 */
class AuditLogController extends CrudController
{
    protected string $model = AuditLog::class;

    protected string $modulSlug = 'audit-log';

    protected array $searchable = ['action', 'model_type', 'ip_address'];

    protected array $sortable = ['id', 'action', 'model_type', 'created_at'];

    protected string $defaultSort = '-created_at';

    protected array $withList = ['user:id,name,email'];

    protected array $filterable = [
        'action' => 'exact',
        'model_type' => 'exact',
        'user_id' => 'exact',
    ];

    protected function rules(string $mode, ?Model $record): array
    {
        return [];
    }

    public function store(\Illuminate\Http\Request $request): JsonResponse
    {
        return response()->json(['error' => 'Audit log tidak dapat diubah'], 405);
    }

    public function update(\Illuminate\Http\Request $request, int $id): JsonResponse
    {
        return response()->json(['error' => 'Audit log tidak dapat diubah'], 405);
    }

    public function destroy(int $id): JsonResponse
    {
        return response()->json(['error' => 'Audit log tidak dapat dihapus'], 405);
    }
}
