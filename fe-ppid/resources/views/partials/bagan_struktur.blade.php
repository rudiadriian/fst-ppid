{{-- Bagan Struktur Organisasi PPID.
     Variabel: $bagan (hasil StrukturOrganisasi::pohon), $judulBagan (opsional). --}}
@if (!empty($bagan))
    <div class="bg-white dark:bg-[#0B2A1D] rounded-2xl border border-gray-100 dark:border-white/10 p-5 sm:p-8">
        <h2 class="text-xl sm:text-2xl font-bold text-center text-gray-900 dark:text-white mb-8">
            {!! $judulDua($judulBagan ?? __('Bagan Struktur Organisasi PPID'), 1) !!}
        </h2>

        {{-- Bagan bisa lebih lebar dari layar ponsel; gulirnya dikurung di sini
             supaya halaman tidak ikut bergeser ke samping. --}}
        <div class="overflow-x-auto">
            <div class="min-w-max mx-auto flex flex-col items-center gap-8 px-1 pb-2">
                @foreach ($bagan as $cabang)
                    @include('partials.bagan_node', ['cabang' => $cabang])
                @endforeach
            </div>
        </div>
    </div>
@endif
