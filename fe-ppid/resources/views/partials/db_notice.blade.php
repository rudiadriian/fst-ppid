{{-- Ditampilkan bila data gagal diambil dari backend; halaman tetap bisa diakses. --}}
@if (!empty($data['db_offline']))
    <div role="status" class="mb-6 flex items-start gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-500/30 dark:bg-amber-500/10">
        <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
        <p class="text-sm font-normal leading-relaxed text-amber-800 dark:text-amber-200">
            {{ __('Data terbaru sedang tidak dapat dimuat dari sistem. Informasi di halaman ini mungkin belum lengkap. Silakan coba beberapa saat lagi.') }}
        </p>
    </div>
@endif
