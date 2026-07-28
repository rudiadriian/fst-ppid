<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Klien API selalu menerima JSON, tidak pernah halaman HTML error.
        $this->renderable(function (ModelNotFoundException|NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['error' => 'Data tidak ditemukan'], 404);
            }
        });

        $this->renderable(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['error' => 'Tidak terautentikasi'], 401);
            }
        });

        $this->renderable(function (AuthorizationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['error' => 'Akses ditolak'], 403);
            }
        });

        // Pelanggaran constraint DB (mis. foreign key masih dirujuk) dijawab
        // sebagai konflik data, bukan 500 — dan detail SQL tidak ikut bocor.
        $this->renderable(function (QueryException $e, Request $request) {
            if (!$request->is('api/*')) {
                return null;
            }

            // 23001 restrict_violation, 23503 foreign_key_violation,
            // 23505 unique_violation, 23514 check_violation.
            $kodeConstraint = ['23001', '23503', '23505', '23514'];

            if (in_array((string) ($e->errorInfo[0] ?? ''), $kodeConstraint, true)) {
                return response()->json([
                    'error' => 'Operasi ditolak karena melanggar keterkaitan data. Periksa data yang masih merujuk baris ini.',
                ], 409);
            }

            return null;
        });
    }
}
