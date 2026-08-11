@extends('layouts.portal')

@section('title', 'Tambah Pengajuan Permohonan | PPID FSTJ')
@section('portal-judul', __('Tambah Pengajuan'))

@section('portal')

    <div class="bg-white dark:bg-[#0B2A1D] rounded-2xl border border-gray-100 dark:border-white/10 p-6 sm:p-8">

        {{-- Data pemohon sengaja tidak ditampilkan di formulir: seluruhnya
             mengikuti akun yang sedang masuk dan sudah diverifikasi. --}}
        <p class="text-sm text-gray-600 dark:text-gray-300 mb-7 leading-relaxed">
            {{ __('Permohonan ini diajukan atas nama') }}
            <span class="font-semibold text-gray-900 dark:text-white">{{ $pemohon->nama }}</span>
            ({{ $pemohon->email }}). {{ __('Data pemohon mengikuti akun Anda, jadi tidak perlu diisi ulang.') }}
        </p>

        <form method="POST" action="{{ route('akun.permohonan.store') }}" class="space-y-6"
              x-data="{ salinan: '{{ old('format_informasi', 'softcopy') }}' }">
            @csrf

            <div>
                <label for="rincian_informasi" class="{{ $fsLabel }}">{{ __('Rincian Informasi') }} <span class="text-red-600">*</span></label>
                <textarea id="rincian_informasi" name="rincian_informasi" rows="4" required maxlength="2000"
                          placeholder="{{ __('Jelaskan secara spesifik informasi yang Anda butuhkan.') }}"
                          class="{{ $fsInput }}">{{ old('rincian_informasi') }}</textarea>
            </div>

            <div>
                <label for="tujuan_penggunaan" class="{{ $fsLabel }}">{{ __('Tujuan Penggunaan Informasi') }} <span class="text-red-600">*</span></label>
                <textarea id="tujuan_penggunaan" name="tujuan_penggunaan" rows="3" required maxlength="2000"
                          placeholder="{{ __('Contoh: bahan penelitian akademik.') }}"
                          class="{{ $fsInput }}">{{ old('tujuan_penggunaan') }}</textarea>
            </div>

            <div>
                <label for="cara_memperoleh" class="{{ $fsLabel }}">{{ __('Cara Memperoleh Informasi') }} <span class="text-red-600">*</span></label>
                <select id="cara_memperoleh" name="cara_memperoleh" required class="{{ $fsInput }}">
                    @foreach (\App\Models\PermohonanInformasi::CARA_MEMPEROLEH as $nilai => $label)
                        <option value="{{ $nilai }}" @selected(old('cara_memperoleh') === $nilai)>{{ __($label) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <span class="{{ $fsLabel }}">{{ __('Salinan Informasi Dibutuhkan') }} <span class="text-red-600">*</span></span>
                <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach (['hardcopy' => __('Salinan Cetak'), 'softcopy' => __('Salinan Digital')] as $nilai => $label)
                        <label class="flex items-center gap-3 p-3.5 bg-gray-50 dark:bg-[#0B2A1D] rounded-xl border border-gray-200 dark:border-white/10 cursor-pointer has-[:checked]:border-[#10462F] has-[:checked]:bg-emerald-50/50">
                            <input type="radio" name="format_informasi" value="{{ $nilai }}" x-model="salinan" required
                                   class="text-[#10462F] focus:ring-[#10462F]">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Pilihan cara mendapatkan salinan mengikuti jenis salinannya:
                 cetak hanya bisa diambil langsung, digital hanya lewat email. --}}
            <div>
                <span class="{{ $fsLabel }}">{{ __('Cara Mendapatkan Salinan Informasi') }} <span class="text-red-600">*</span></span>

                <label x-show="salinan === 'hardcopy'" x-cloak
                       class="mt-2 flex items-center gap-3 p-3.5 bg-gray-50 dark:bg-[#0B2A1D] rounded-xl border border-gray-200 dark:border-white/10 cursor-pointer has-[:checked]:border-[#10462F] has-[:checked]:bg-emerald-50/50">
                    <input type="radio" name="cara_pengiriman" value="ambil_langsung" :required="salinan === 'hardcopy'"
                           :disabled="salinan !== 'hardcopy'" @checked(old('cara_pengiriman') === 'ambil_langsung')
                           class="text-[#10462F] focus:ring-[#10462F]">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Mengambil Langsung') }}</span>
                </label>

                <label x-show="salinan === 'softcopy'"
                       class="mt-2 flex items-center gap-3 p-3.5 bg-gray-50 dark:bg-[#0B2A1D] rounded-xl border border-gray-200 dark:border-white/10 cursor-pointer has-[:checked]:border-[#10462F] has-[:checked]:bg-emerald-50/50">
                    <input type="radio" name="cara_pengiriman" value="email" :required="salinan === 'softcopy'"
                           :disabled="salinan !== 'softcopy'" @checked(old('cara_pengiriman') !== 'ambil_langsung')
                           class="text-[#10462F] focus:ring-[#10462F]">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Salinan Digital (Email)') }}</span>
                </label>
            </div>

            {{-- Ketentuan wajib dibaca dulu: kotak pernyataan baru bisa dicentang
                 setelah pengguna menggulir ketentuan sampai bagian akhir, atau
                 menekan tombol "Saya sudah membaca" (jalur keyboard & pembaca layar). --}}
            <div x-data="{
                sudahBaca: false,
                cekGulir(el) {
                    if (el.scrollTop + el.clientHeight >= el.scrollHeight - 8) { this.sudahBaca = true; }
                }
            }"
            x-init="$nextTick(() => { const k = $refs.ketentuan; if (k.scrollHeight <= k.clientHeight + 8) sudahBaca = true; })"
            class="rounded-2xl border border-gray-200 dark:border-white/10 overflow-hidden">

                <div class="px-5 py-3.5 bg-[#F3ECDD] dark:bg-[#082217] border-b border-gray-200 dark:border-white/10 flex flex-wrap items-center justify-between gap-2">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">{{ __('Ketentuan Layanan Permohonan Informasi Publik') }}</h3>
                    <span class="text-xs font-semibold" :class="sudahBaca ? 'text-[#10462F] dark:text-[#3E9C6C]' : 'text-amber-700'"
                          x-text="sudahBaca ? '{{ __('Sudah dibaca') }}' : '{{ __('Gulir sampai akhir untuk membaca') }}'"></span>
                </div>

                <div x-ref="ketentuan" @scroll="cekGulir($event.target)" tabindex="0"
                     class="max-h-56 overflow-y-auto px-5 py-4 text-sm leading-relaxed text-gray-600 dark:text-gray-300 space-y-3 focus:outline-none focus:ring-2 focus:ring-[#E87317]/40">
                    {!! $ketentuan !!}
                </div>

                <div class="px-5 py-4 border-t border-gray-200 dark:border-white/10 space-y-3">
                    <button type="button" x-show="!sudahBaca" @click="sudahBaca = true"
                            class="text-sm font-semibold text-[#E87317] hover:underline">
                        {{ __('Saya sudah membaca ketentuan di atas') }}
                    </button>

                    <label class="flex items-start gap-3 text-sm" :class="sudahBaca ? 'text-gray-700 dark:text-gray-200' : 'text-gray-400 dark:text-gray-500 cursor-not-allowed'">
                        <input type="checkbox" name="pernyataan_benar" value="1" required
                               :disabled="!sudahBaca" @checked(old('pernyataan_benar'))
                               class="mt-0.5 rounded border-gray-300 text-[#10462F] focus:ring-[#10462F] disabled:cursor-not-allowed">
                        <span>{{ __('Saya menyetujui semua informasi yang saya berikan tentang Data Diri dan Permohonan Informasi ini adalah benar') }}</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center gap-4 pt-2">
                <button type="submit" class="{{ $fsBtn }}">{{ __('Kirim Permohonan') }}</button>
                <a href="{{ route('akun.permohonan.index') }}" class="text-sm font-semibold text-[#10462F] dark:text-[#3E9C6C] hover:underline">{{ __('Batal') }}</a>
            </div>
        </form>
    </div>

@endsection
