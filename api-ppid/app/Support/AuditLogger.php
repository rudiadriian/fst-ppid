<?php

namespace App\Support;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

/**
 * Penulis jejak audit ke tabel `audit_log`.
 * Kegagalan pencatatan tidak boleh menggagalkan request utama.
 */
class AuditLogger
{
    public static function record(
        ?int $userId,
        string $action,
        ?string $modelType = null,
        ?int $modelId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
    ): void {
        try {
            AuditLog::create([
                'user_id' => $userId,
                'action' => $action,
                'model_type' => $modelType,
                'model_id' => $modelId,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => Request::ip(),
                'user_agent' => substr((string) Request::userAgent(), 0, 255),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Gagal menulis audit_log: '.$e->getMessage(), [
                'action' => $action,
                'user_id' => $userId,
            ]);
        }
    }
}
