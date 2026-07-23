@extends('layouts.app')

@section('title', 'Cek Status Permohonan | PPID FSTJ')

@section('content')

    {{-- HERO --}}
    <section class="relative fs-gradient overflow-hidden">
        <div class="absolute inset-0 fs-dot-pattern opacity-40"></div>
        <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8 py-16 lg:py-20 text-center">
            <p class="text-sm font-semibold tracking-widest uppercase text-white/70 mb-4">{{ __('Layanan Informasi') }}</p>
            <h1 class="text-4xl lg:text-5xl font-bold text-white leading-tight">{{ __('Cek Status Permohonan Informasi') }}</h1>
            <p class="mt-4 text-lg font-normal text-white/80 max-w-2xl mx-auto leading-relaxed">
                {{ __('Lacak perkembangan permohonan yang telah Anda ajukan menggunakan Nomor Registrasi.') }}
            </p>
        </div>
    </section>

    {{-- KONTEN --}}
    <section class="py-16 lg:py-20 bg-[#F7F9F8] dark:bg-[#0d1310]">
        <div class="max-w-3xl mx-auto px-6 lg:px-8">
            <div x-data="{
                form: {
                    registration_number: '',
                    _token: '{{ csrf_token() }}'
                },
                isSearching: false,
                hasResult: false,
                result: {},
                notFound: false,

                async checkStatus() {
                    this.isSearching = true;
                    this.hasResult = false;
                    this.notFound = false;
                    this.result = {};

                    await new Promise(resolve => setTimeout(resolve, 1500));
                    let statusMap = {
                        'PPID-FSTJ/20251010/1234': {status: 'DITERIMA', number: 'PPID-FSTJ/20251010/1234', info_requested: 'Laporan Tahunan 2023', response_date: '15/10/2025'},
                        'PPID-FSTJ/20251101/5678': {status: 'DALAM PROSES', number: 'PPID-FSTJ/20251101/5678', info_requested: 'Data Distribusi Pangan', response_date: 'Perkiraan 20/12/2025'},
                        'PPID-FSTJ/20250920/9012': {status: 'DITOLAK', number: 'PPID-FSTJ/20250920/9012', info_requested: 'Data Rapat Direksi', response_date: '25/09/2025'},
                    };
                    let key = this.form.registration_number.toUpperCase().trim();
                    let data = statusMap[key];

                    if (data) {
                         this.result = data;
                         this.hasResult = true;
                    } else {
                         this.notFound = true;
                         this.hasResult = true;
                         this.result = {status: 'DATA TIDAK DITEMUKAN', number: key};
                    }

                    this.isSearching = false;
                }
            }"
            class="bg-white dark:bg-[#121a17] p-6 sm:p-10 rounded-2xl shadow-sm border border-gray-100 dark:border-white/10">

                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ __('Masukkan Nomor Registrasi') }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500 mb-6">{{ __('Gunakan nomor tiket yang Anda terima saat mengajukan permohonan.') }}</p>

                <form @submit.prevent="checkStatus()">
                    <input type="hidden" name="_token" :value="form._token">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input x-model="form.registration_number" type="text" required
                            class="flex-grow px-4 py-3.5 bg-gray-50 border border-gray-200 dark:border-white/10 rounded-xl focus:bg-white dark:bg-[#121a17] focus:border-[#008060] focus:ring-2 focus:ring-[#008060]/15 outline-none transition-all text-base font-mono uppercase"
                            placeholder="{{ __('Contoh: PPID-FSTJ/20251010/1234') }}">
                        <button type="submit" :disabled="isSearching"
                            class="inline-flex items-center justify-center py-3.5 px-7 fs-gradient text-white text-base font-semibold rounded-xl shadow-lg shadow-emerald-900/20 hover:-translate-y-0.5 transition-all duration-300 disabled:opacity-70">
                            <svg x-show="isSearching" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span x-show="!isSearching">{{ __('Lacak Status') }}</span>
                            <span x-show="isSearching">{{ __('Mencari...') }}</span>
                        </button>
                    </div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-2.5">{{ __('Pastikan nomor registrasi yang Anda masukkan benar.') }}</p>
                </form>

                {{-- Hasil --}}
                <div x-show="hasResult" x-transition.opacity class="mt-10 pt-8 border-t border-gray-100 dark:border-white/10">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-5">{{ __('Status Permohonan') }}</h3>

                    <div :class="{
                        'bg-emerald-50 border-[#008060]': result.status === 'DITERIMA',
                        'bg-blue-50 border-blue-500': result.status === 'DALAM PROSES',
                        'bg-red-50 border-red-500': result.status === 'DITOLAK' || result.status === 'DATA TIDAK DITEMUKAN'
                    }" class="border-l-4 p-6 mb-8 rounded-r-2xl">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Status Permohonan') }}</p>
                        <p class="text-3xl font-extrabold mt-1"
                           :class="{
                                'text-[#008060]': result.status === 'DITERIMA',
                                'text-blue-600': result.status === 'DALAM PROSES',
                                'text-red-600': result.status === 'DITOLAK' || result.status === 'DATA TIDAK DITEMUKAN'
                           }" x-text="result.status"></p>
                    </div>

                    <dl x-show="!notFound" class="space-y-1">
                        <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-white/10">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Nomor Registrasi') }}</dt>
                            <dd x-text="result.number" class="text-sm font-semibold text-gray-900 dark:text-white"></dd>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-white/10">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Informasi yang Dimohon') }}</dt>
                            <dd x-text="result.info_requested" class="text-sm font-semibold text-gray-900 dark:text-white text-right max-w-xs"></dd>
                        </div>
                        <div x-show="result.response_date" class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-white/10">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Tanggal Tanggapan') }}</dt>
                            <dd x-text="result.response_date" class="text-sm font-semibold text-right"
                                :class="{'text-red-600': result.status === 'DITOLAK', 'text-[#008060]': result.status === 'DITERIMA'}"></dd>
                        </div>
                    </dl>

                    <div x-show="notFound" class="p-5 bg-red-50 border border-red-100 rounded-2xl text-sm text-red-700 leading-relaxed">
                        {{ __('Nomor registrasi') }} <span class="font-bold" x-text="result.number"></span> {{ __('tidak ditemukan. Mohon periksa kembali nomor yang Anda masukkan atau hubungi PPID.') }}
                    </div>

                    <div x-show="result.status === 'DITOLAK'" class="mt-8 p-6 bg-[#F7F9F8] dark:bg-[#0d1310] rounded-2xl border border-gray-100 dark:border-white/10 text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">{{ __('Jika Anda tidak puas dengan penolakan ini, Anda berhak mengajukan keberatan:') }}</p>
                        <a href="{{ route('ppid.objection') }}" class="inline-flex items-center gap-2 px-6 py-3 fs-gradient text-white text-sm font-semibold rounded-xl shadow-lg shadow-emerald-900/20 hover:-translate-y-0.5 transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.398 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Ajukan Keberatan Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
