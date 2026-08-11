@if (session('status'))
    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-sm text-[#10462F] dark:bg-white/5 dark:border-white/10 dark:text-[#3E9C6C]" role="status">
        {{ session('status') }}
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
