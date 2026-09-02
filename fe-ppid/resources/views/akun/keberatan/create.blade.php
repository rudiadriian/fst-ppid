@extends('layouts.portal')

@section('title', __('Tambah Pengajuan Keberatan') . ' | ' . __('PPID FSTJ'))
@section('portal-judul', __('Tambah Pengajuan Keberatan'))

@section('portal')

    <div class="bg-white dark:bg-[#0B2A1D] rounded-2xl border border-gray-100 dark:border-white/10 p-6 sm:p-8">

        @if ($permohonanSaya->isEmpty())
            <div class="p-5 rounded-2xl bg-amber-50 border border-amber-100 text-sm text-amber-900">
                <p class="font-bold mb-1">{{ __('Belum ada permohonan yang bisa dikeberatani.') }}</p>
                <p>{{ __('Keberatan diajukan atas permohonan yang sudah ditanggapi atau ditolak, atau yang batas waktu tanggapannya sudah lewat.') }}
                    <a href="{{ route('akun.permohonan.create') }}" class="font-bold underline">{{ __('Ajukan permohonan dulu') }}</a>.
                </p>
            </div>
        @else
            {{-- Tujuh dasar Pasal 35 UU KIP, ditulis di muka.
                 Pemohon perlu tahu atas dasar apa keberatan bisa diajukan
                 sebelum ia menyusun kasus posisinya, bukan menemukannya satu per
                 satu di dalam dropdown setelah formulirnya terbuka. --}}
            <div class="mb-6 p-5 rounded-2xl bg-[#F3ECDD] dark:bg-[#082217] border border-gray-100 dark:border-white/10 text-sm text-gray-700 dark:text-gray-200">
                <p class="font-bold mb-2">{{ __('Keberatan dapat diajukan atas dasar berikut') }}</p>
                <ul class="list-disc pl-5 space-y-1">
                    @foreach (\App\Models\KeberatanInformasi::JENIS as $label)
                        <li>{{ __($label) }}</li>
                    @endforeach
                </ul>
            </div>

            {{-- Field lain dikunci sampai permohonan dipilih: keberatan tanpa
                 permohonan induk tidak punya dasar. --}}
            <form method="POST" action="{{ route('akun.keberatan.store') }}" enctype="multipart/form-data" class="space-y-6"
                  x-data="{ dipilih: '{{ old('permohonan_id') }}' }">
                @csrf

                <div>
                    <label for="permohonan_id" class="{{ $fsLabel }}">{{ __('Pilih Permohonan Informasi') }} <span class="text-red-600">*</span></label>
                    <select id="permohonan_id" name="permohonan_id" required x-model="dipilih" class="{{ $fsInput }}">
                        <option value="">{{ __('— Pilih permohonan —') }}</option>
                        @foreach ($permohonanSaya as $p)
                            <option value="{{ $p->id }}" @selected(old('permohonan_id') == $p->id)>
                                {{ $p->kode_permohonan }} — {{ \Illuminate\Support\Str::limit($p->rincian_informasi, 50) }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">{{ __('Isian di bawah aktif setelah permohonan dipilih.') }}</p>
                </div>

                <fieldset class="space-y-6" :disabled="!dipilih" :class="!dipilih ? 'opacity-50' : ''">
                    <div>
                        <label for="jenis_keberatan" class="{{ $fsLabel }}">{{ __('Alasan Keberatan') }} <span class="text-red-600">*</span></label>
                        <select id="jenis_keberatan" name="jenis_keberatan" required class="{{ $fsInput }}">
                            @foreach (\App\Models\KeberatanInformasi::JENIS as $nilai => $label)
                                <option value="{{ $nilai }}" @selected(old('jenis_keberatan') === $nilai)>{{ __($label) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="kasus_posisi" class="{{ $fsLabel }}">{{ __('Kasus Posisi Keberatan') }} <span class="text-red-600">*</span></label>
                        <textarea id="kasus_posisi" name="kasus_posisi" rows="5" required maxlength="2000"
                                  placeholder="{{ __('Uraikan duduk perkaranya dan apa yang Anda harapkan sebagai penyelesaian.') }}"
                                  class="{{ $fsInput }}">{{ old('kasus_posisi') }}</textarea>
                    </div>

                    <div>
                        <label for="lampiran" class="{{ $fsLabel }}">{{ __('Lampiran Dokumen Keberatan') }}</label>
                        <input id="lampiran" name="lampiran[]" type="file" multiple accept=".pdf,.jpg,.jpeg,.png" class="{{ $fsInput }}">
                        <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">{{ __('PDF/JPG/PNG, maksimal 10 MB per berkas. Boleh lebih dari satu.') }}</p>
                    </div>

                    <label class="flex items-start gap-3 text-sm text-gray-600 dark:text-gray-300">
                        <input type="checkbox" name="dikuasakan" value="1" @checked(old('dikuasakan'))
                               class="mt-0.5 rounded border-gray-300 text-[#10462F] focus:ring-[#10462F]">
                        <span>{{ __('Dikuasakan — keberatan ini diajukan melalui kuasa saya.') }}</span>
                    </label>

                    <div class="flex items-center gap-4 pt-2">
                        <button type="submit" class="{{ $fsBtn }}">{{ __('Ajukan Keberatan') }}</button>
                        <a href="{{ route('akun.keberatan.index') }}" class="text-sm font-semibold text-[#10462F] dark:text-[#3E9C6C] hover:underline">{{ __('Batal') }}</a>
                    </div>
                </fieldset>
            </form>
        @endif
    </div>

@endsection
