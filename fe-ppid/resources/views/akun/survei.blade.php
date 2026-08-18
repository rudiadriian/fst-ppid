@extends('layouts.portal')

@section('title', __('Survei Kepuasan Layanan') . ' | ' . __('PPID FSTJ'))
@section('portal-judul', __('Survei Kepuasan Layanan'))

@section('portal')

    <div class="bg-white dark:bg-[#0B2A1D] rounded-2xl border border-gray-100 dark:border-white/10 p-6 sm:p-8">
        <div class="max-w-2xl">
            <div>
                @php
                    $inputClass = $fsInput;
                    $labelClass = $fsLabel;
                    $btnClass = $fsBtn;
                @endphp

                <p class="text-sm text-gray-600 dark:text-gray-300 mb-6 leading-relaxed">
                    {{ __('Penilaian Anda dipakai untuk memperbaiki mutu layanan informasi publik.') }}
                </p>

                <div class="p-5 bg-[#F3ECDD] dark:bg-[#082217] rounded-2xl border border-gray-100 dark:border-white/10 mb-7">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Nomor Registrasi') }}</p>
                    <p class="text-lg font-extrabold text-gray-900 dark:text-white">{{ $permohonan->kode_permohonan }}</p>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ \Illuminate\Support\Str::limit($permohonan->rincian_informasi, 160) }}</p>
                </div>

                <form method="POST" action="{{ route('akun.survei.store', $permohonan->id) }}" x-data="{ rating: {{ (int) old('rating', 0) }} }" class="space-y-7">
                    @csrf

                    <div>
                        <span class="{{ $labelClass }}">{{ __('Seberapa puas Anda atas layanan informasi ini?') }}</span>

                        {{-- Bintang: tombol radio asli tetap ada supaya bisa dipakai
                             tanpa JavaScript dan terbaca pembaca layar. --}}
                        <div class="mt-3 flex flex-wrap gap-2">
                            @php
                                $skala = [
                                    1 => __('Sangat Tidak Puas'),
                                    2 => __('Tidak Puas'),
                                    3 => __('Cukup'),
                                    4 => __('Puas'),
                                    5 => __('Sangat Puas'),
                                ];
                            @endphp
                            @foreach ($skala as $nilai => $teks)
                                <label class="flex-1 min-w-[110px] cursor-pointer">
                                    <input type="radio" name="rating" value="{{ $nilai }}" x-model.number="rating" class="sr-only peer" required>
                                    <span class="block text-center px-3 py-4 rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-[#0B2A1D] transition-all peer-focus-visible:ring-2 peer-focus-visible:ring-[#E87317]"
                                          :class="rating === {{ $nilai }} ? 'fs-gradient-accent text-white border-transparent' : ''">
                                        <span class="block text-xl font-extrabold">{{ $nilai }}</span>
                                        <span class="block mt-1 text-xs font-semibold">{{ $teks }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        @error('rating')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="komentar" class="{{ $labelClass }}">{{ __('Saran atau Masukan (opsional)') }}</label>
                        <textarea id="komentar" name="komentar" rows="4" maxlength="1000" class="{{ $inputClass }}"
                                  placeholder="{{ __('Ceritakan apa yang sudah baik dan apa yang perlu kami perbaiki.') }}">{{ old('komentar') }}</textarea>
                        @error('komentar')<p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="{{ $btnClass }}">{{ __('Kirim Penilaian') }}</button>
                        <a href="{{ route('akun.dashboard') }}" class="text-sm font-semibold text-[#10462F] dark:text-[#3E9C6C] hover:underline">{{ __('Batal') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
