{{-- Satu cabang bagan struktur organisasi (dipanggil rekursif).
     Variabel: $cabang = ['node' => StrukturOrganisasi, 'alur' => [...], 'samping' => [...]] --}}
@php
    $node = $cabang['node'];
    $poin = $node->daftarPoin();
    $samping = $cabang['samping'] ?? [];

    // Anak alur digambar di bawah kotak; khusus tipe grup anaknya dibungkus bingkai.
    $anak = array_values($node->tipe() === 'grup' ? [] : ($cabang['alur'] ?? []));
    $jumlahAnak = count($anak);

    $garis = 'text-[#10462F] dark:text-[#3E9C6C]';
    $putus = 'border-[#10462F]/70 dark:border-[#3E9C6C]/60';
@endphp

<div class="flex flex-col items-center">

    {{-- Pembungkus kotak selebar kotaknya sendiri: kotak samping ditempel di luar
         alur (absolute) supaya tidak menggeser kotak ini ke kiri dan tidak
         menambah tinggi baris — batang turun tetap menempel di sisi bawah kotak. --}}
    <div class="relative">

        @if ($node->tipe() === 'grup')
            {{-- Bingkai berjudul; anak-anaknya berjajar di dalamnya. --}}
            <div class="rounded-3xl border-2 {{ $putus }} px-5 py-6 sm:px-8">
                <p class="text-center text-lg sm:text-xl font-bold text-gray-900 dark:text-white mb-5">{{ $node->teks('jabatan') }}</p>

                <div class="flex justify-center items-start gap-5">
                    @foreach ($cabang['alur'] as $isiGrup)
                        @include('partials.bagan_node', ['cabang' => $isiGrup])
                    @endforeach
                </div>
            </div>
        @else
            {{-- Kotak biasa: kepala hijau (jabatan) + badan abu (nama / butir). --}}
            <div class="w-[15rem] sm:w-[17rem] rounded-xl overflow-hidden shadow-sm">
                <p class="px-4 py-2.5 fs-gradient text-white text-sm font-bold text-center leading-snug">{{ $node->teks('jabatan') }}</p>

                <div class="px-4 py-4 bg-gray-200 dark:bg-white/10 text-sm text-gray-700 dark:text-gray-200 text-center leading-relaxed">
                    @if ($poin)
                        <ul class="list-disc list-outside pl-5 space-y-1 text-left">
                            @foreach ($poin as $isi)
                                <li>{{ $isi }}</li>
                            @endforeach
                        </ul>
                    @else
                        {{ $node->nama }}
                    @endif

                    @if ($node->teks('deskripsi'))
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ $node->teks('deskripsi') }}</p>
                    @endif
                </div>
            </div>
        @endif

        {{-- Kotak samping: garis putus-putus mendatar dari sisi kanan kotak ini ke
             sisi kiri kotak samping, sejajar tinggi kepala kotak. --}}
        @if (!empty($samping))
            <div class="absolute top-0 left-full flex items-start">
                <span class="mt-5 w-8 sm:w-12 border-t-2 border-dashed {{ $putus }}" aria-hidden="true"></span>

                <div class="flex flex-col gap-5">
                    @foreach ($samping as $cabangSamping)
                        @include('partials.bagan_node', ['cabang' => $cabangSamping])
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    @if ($jumlahAnak === 1)
        {{-- Satu anak: batang turun langsung dari sisi bawah kotak ke ujung panah,
             tanpa jarak, supaya garis menyatu dengan kedua kotak. --}}
        <span class="flex flex-col items-center {{ $garis }}" aria-hidden="true">
            <span class="w-0.5 h-7 bg-current"></span>
            <svg class="block w-4 h-4" viewBox="0 0 16 16" fill="currentColor"><path d="M8 16 1 4h14z"/></svg>
        </span>

        @include('partials.bagan_node', ['cabang' => $anak[0]])
    @elseif ($jumlahAnak > 1)
        {{-- Banyak anak: batang turun ke garis mendatar, lalu satu batang + panah
             untuk tiap anak. Garis mendatar berhenti di titik tengah anak paling
             kiri dan paling kanan, tidak menjulur keluar bagan. --}}
        <span class="w-0.5 h-7 bg-current {{ $garis }}" aria-hidden="true"></span>

        <div class="flex items-start justify-center">
            @foreach ($anak as $i => $cabangAnak)
                <div class="flex flex-col items-center px-3">
                    <div class="relative w-full h-10 {{ $garis }}" aria-hidden="true">
                        <span class="absolute top-0 h-0.5 bg-current
                            @if ($i === 0) left-1/2 right-0
                            @elseif ($i === $jumlahAnak - 1) left-0 right-1/2
                            @else left-0 right-0 @endif"></span>

                        <span class="absolute top-0 left-1/2 -translate-x-1/2 w-0.5 h-6 bg-current"></span>

                        <svg class="block absolute top-6 left-1/2 -translate-x-1/2 w-4 h-4" viewBox="0 0 16 16" fill="currentColor"><path d="M8 16 1 4h14z"/></svg>
                    </div>

                    @include('partials.bagan_node', ['cabang' => $cabangAnak])
                </div>
            @endforeach
        </div>
    @endif
</div>
