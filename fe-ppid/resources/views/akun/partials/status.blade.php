@if (session('status'))
    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-sm text-[#10462F] dark:bg-white/5 dark:border-white/10 dark:text-[#3E9C6C]" role="status">
        {{ session('status') }}
    </div>
@endif

{{-- Peringatan: berhasil, tetapi ada langkah wajib yang belum tuntas
     (mis. akun sudah dibuat namun emailnya belum diverifikasi). --}}
@if (session('peringatan'))
    <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-sm text-amber-900 dark:bg-amber-400/10 dark:border-amber-400/20 dark:text-amber-200" role="alert">
        <div class="flex items-start gap-2.5">
            <svg class="w-5 h-5 flex-shrink-0 mt-px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.398 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div>
                <p class="font-semibold">{{ __('Verifikasi email dulu sebelum masuk') }}</p>
                <p class="mt-1">{{ session('peringatan') }}</p>
                <a href="{{ route('akun.verifikasi.notice') }}" class="mt-2 inline-block font-semibold underline">{{ __('Belum menerima emailnya?') }}</a>
            </div>
        </div>
    </div>
@endif

{{-- Penolakan rem anti-bot (honeypot / jeda pengisian). Ditampilkan sendiri
     karena isian yang bersangkutan memang tidak terlihat pengguna. --}}
@if ($errors->has('perisai'))
    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-100 text-sm text-red-700 dark:bg-red-500/10 dark:border-red-500/20 dark:text-red-300" role="alert">
        {{ $errors->first('perisai') }}
    </div>
@endif

@if ($errors->any() && $errors->count() > 1)
    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-100 text-sm text-red-700 dark:bg-red-500/10 dark:border-red-500/20 dark:text-red-300" role="alert">
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $pesan)
                <li>{{ $pesan }}</li>
            @endforeach
        </ul>
    </div>
@endif
