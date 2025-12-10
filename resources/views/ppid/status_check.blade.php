@extends('layouts.app')

@section('title', 'Cek Status Permohonan | PPID FSTJ')

@section('content')

    {{-- HEADER HALAMAN CEK STATUS --}}
    <section class="bg-gray-700 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-extrabold text-white">
                Cek Status Permohonan Informasi
            </h1>
            <p class="mt-3 text-lg text-gray-300">
                Lacak perkembangan permohonan yang telah Anda ajukan.
            </p>
        </div>
    </section>

    {{-- KONTEN CEK STATUS DENGAN ALPINE.JS --}}
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

                async checkStatus() {
                    this.isSearching = true;
                    this.hasResult = false;
                    this.result = {};

                    try {
                        const response = await fetch('{{ route('ppid.status.check') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.form)
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.result = data;
                            this.hasResult = true;
                        } else {
                            alert('Terjadi kesalahan saat mencari status.');
                        }
                    } catch (error) {
                        alert('Terjadi kesalahan jaringan atau server.');
                    } finally {
                        this.isSearching = false;
                    }
                }
            }"
            class="bg-white p-8 rounded-xl shadow-lg border border-gray-200">

                <h2 class="text-xl font-bold text-gray-800 mb-6 border-b pb-2">Masukkan Nomor Registrasi</h2>

                {{-- FORM PENCARIAN --}}
                <form @submit.prevent="checkStatus()">
                    <input type="hidden" name="_token" :value="form._token">
                    <div class="flex space-x-3">
                        <input x-model="form.registration_number" type="text" required
                            class="flex-grow border border-gray-300 rounded-md shadow-sm p-3 focus:ring-[#008060] focus:border-[#008060]"
                            placeholder="Contoh: PPID-FSTJ/20251010/1234">

                        <button type="submit" :disabled="isSearching"
                            class="inline-flex items-center py-3 px-6 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-gray-700 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-700 transition duration-300">
                            <span x-show="!isSearching">Cek Status</span>
                            <span x-show="isSearching">Mencari...</span>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Masukkan nomor registrasi permohonan Anda (PPID-FSTJ/YYYYMMDD/XXXX).</p>
                </form>

                {{-- HASIL PENCARIAN --}}
                <div x-show="hasResult" x-transition.opacity class="mt-10 pt-6 border-t border-gray-200">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Hasil Status</h3>

                    {{-- Status Card --}}
                    <div :class="{
                        'bg-yellow-50 border-yellow-500 text-yellow-800': result.status === 'PENDING' || result.status === 'DALAM PENELITIAN' || result.status === 'DIPROSES',
                        'bg-green-50 border-green-500 text-green-800': result.status === 'DITERIMA',
                        'bg-red-50 border-red-500 text-red-800': result.status === 'DITOLAK' || result.status === 'TIDAK DITEMUKAN'
                    }" class="border-l-4 p-4 mb-6 rounded-lg shadow-md">
                        <p class="text-sm font-semibold">Status Saat Ini</p>
                        <p class="text-3xl font-extrabold mt-1" x-text="result.status"></p>
                    </div>

                    {{-- Detail Status --}}
                    <dl class="space-y-4 text-gray-700">
                        <div class="flex justify-between border-b pb-2">
                            <dt class="font-medium">Nomor Registrasi:</dt>
                            <dd x-text="result.number" class="font-bold"></dd>
                        </div>
                        <div class="flex justify-between border-b pb-2">
                            <dt class="font-medium">Informasi yang Dimohon:</dt>
                            <dd x-text="result.info_requested" class="text-right"></dd>
                        </div>
                        <div x-show="result.response_date && result.status !== 'DITOLAK'" class="flex justify-between border-b pb-2">
                            <dt class="font-medium">Tanggal Respon / Penyerahan:</dt>
                            <dd x-text="result.response_date" class="text-right text-green-600 font-bold"></dd>
                        </div>
                        <div x-show="result.status === 'DITOLAK'" class="flex justify-between border-b pb-2">
                            <dt class="font-medium text-red-600">Tanggal Respon Penolakan:</dt>
                            <dd x-text="result.response_date" class="text-right text-red-600 font-bold"></dd>
                        </div>
                    </dl>

                    {{-- Tombol Lanjut ke Keberatan --}}
                    <div x-show="result.status === 'DITOLAK' || result.status === 'TIDAK DITEMUKAN'" class="mt-8 text-center">
                        <p class="text-sm text-gray-600 mb-3">Jika Anda tidak puas dengan status ini, Anda berhak mengajukan keberatan:</p>
                        <a href="{{ route('ppid.objection') }}" class="inline-flex items-center text-red-600 border border-red-600 hover:bg-red-50 px-4 py-2 rounded-md font-semibold transition duration-150">
                            Ajukan Keberatan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
