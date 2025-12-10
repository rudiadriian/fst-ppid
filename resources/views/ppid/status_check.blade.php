@extends('layouts.app')

@section('title', 'Cek Status Permohonan | PPID FSTJ')

@section('content')
    <section class="relative bg-gradient-to-r from-emerald-900 to-gray-900 py-16 lg:py-24 overflow-hidden">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 30px 30px;"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
            <h1 class="text-4xl sm:text-5xl font-extrabold text-white leading-tight drop-shadow">
                Cek Status Permohonan Informasi
            </h1>
            <p class="mt-4 text-lg text-emerald-200 max-w-3xl mx-auto">
                Lacak perkembangan permohonan yang telah Anda ajukan menggunakan Nomor Registrasi.
            </p>
        </div>
    </section>
    <section class="py-16 lg:py-24 bg-gray-50">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
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
                    try {
                        const response = await fetch('{{ route('ppid.status.check') }}', { ... });
                        const data = await response.json();
                        if (data.success) {
                            this.result = data;
                            this.hasResult = true;
                            this.notFound = false;
                        } else {
                            this.notFound = true;
                            this.hasResult = true;
                        }
                    } catch (error) { ... }
                    ------------------------------------------ */

                    this.isSearching = false;
                }
            }"
            class="bg-white p-8 sm:p-10 lg:p-12 rounded-3xl shadow-xl border border-gray-100">
                <h2 class="text-xl font-bold text-gray-800 mb-6 border-b pb-2">Masukkan Nomor Registrasi</h2>
                <form @submit.prevent="checkStatus()">
                    <input type="hidden" name="_token" :value="form._token">
                    <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
                        <input x-model="form.registration_number" type="text" required
                            class="flex-grow border border-gray-300 rounded-lg shadow-sm p-3 focus:ring-[#008060] focus:border-[#008060] text-lg font-mono uppercase"
                            placeholder="Contoh: PPID-FSTJ/20251010/1234">

                        <button type="submit" :disabled="isSearching"
                            class="inline-flex items-center justify-center py-3 px-6 border border-transparent shadow-md text-base font-bold rounded-lg text-white bg-[#008060] hover:bg-[#00664e] focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-[#008060]/50 transition duration-300">
                            <svg x-show="isSearching" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span x-show="!isSearching">Lacak Status</span>
                            <span x-show="isSearching">Mencari...</span>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Pastikan nomor registrasi yang Anda masukkan benar.</p>
                </form>
                <div x-show="hasResult" x-transition.opacity class="mt-10 pt-6 border-t border-gray-200">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">Status Permohonan</h3>
                    <div :class="{
                        'bg-emerald-50 border-[#008060] text-emerald-800': result.status === 'DITERIMA',
                        'bg-blue-50 border-blue-600 text-blue-800': result.status === 'DALAM PROSES',
                        'bg-red-50 border-red-600 text-red-800': result.status === 'DITOLAK' || result.status === 'DATA TIDAK DITEMUKAN'
                    }" class="border-l-4 p-6 mb-8 rounded-lg shadow-md">
                        <p class="text-sm font-semibold uppercase">Status Permohonan</p>
                        <p class="text-4xl font-extrabold mt-1" x-text="result.status"></p>
                    </div>
                    <dl x-show="!notFound" class="space-y-4 text-gray-700">
                        <div class="flex justify-between border-b pb-2">
                            <dt class="font-medium">Nomor Registrasi:</dt>
                            <dd x-text="result.number" class="font-bold"></dd>
                        </div>
                        <div class="flex justify-between border-b pb-2">
                            <dt class="font-medium">Informasi yang Dimohon:</dt>
                            <dd x-text="result.info_requested" class="text-right max-w-xs"></dd>
                        </div>
                        <div x-show="result.response_date" class="flex justify-between border-b pb-2">
                            <dt class="font-medium">Tanggal Tanggapan/Respon:</dt>
                            <dd x-text="result.response_date" :class="{'text-red-600': result.status === 'DITOLAK', 'text-emerald-600': result.status === 'DITERIMA'}" class="text-right font-bold"></dd>
                        </div>
                    </dl>
                    <div x-show="notFound" class="bg-red-50 p-6 rounded-lg text-red-800 font-medium">
                        <p>Nomor registrasi **<span class="font-bold" x-text="result.number"></span>** tidak ditemukan. Mohon periksa kembali nomor yang Anda masukkan atau hubungi PPID.</p>
                    </div>
                    <div x-show="result.status === 'DITOLAK'" class="mt-10 text-center p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <p class="text-sm text-gray-600 mb-4">Jika Anda tidak puas dengan penolakan ini, Anda berhak mengajukan keberatan:</p>
                        <a href="{{ route('ppid.objection') }}" class="inline-flex items-center text-white bg-red-600 hover:bg-red-700 shadow-md px-6 py-3 rounded-full font-bold transition duration-150">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.398 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Ajukan Keberatan Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
