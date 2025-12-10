@extends('layouts.app')

@section('title', 'Pengajuan Keberatan | PPID FSTJ')

@section('content')

    {{-- HEADER HALAMAN KEBERATAN --}}
    <section class="bg-red-600 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-extrabold text-white">
                Formulir Pengajuan Keberatan
            </h1>
            <p class="mt-3 text-lg text-white">
                Ajukan keberatan jika permohonan informasi publik Anda tidak dipenuhi atau tidak sesuai.
            </p>
        </div>
    </section>

    {{-- KONTEN FORMULIR DENGAN ALPINE.JS --}}
    <section class="py-16 lg:py-24 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div x-data="{
                form: {
                    registration_number: '',
                    objection_reason: 'ditolak',
                    objection_purpose: '',
                    full_name: '',
                    phone: '',
                    email: '',
                    _token: '{{ csrf_token() }}'
                },
                isSubmitting: false,
                isSuccess: false,
                response: {},

                async submitForm() {
                    this.isSubmitting = true;
                    this.isSuccess = false;

                    try {
                        const response = await fetch('{{ route('ppid.objection.submit') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.form)
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.response = data;
                            this.isSuccess = true;
                            // Reset form (kecuali token)
                            this.form = { registration_number: '', objection_reason: 'ditolak', objection_purpose: '', full_name: '', phone: '', email: '', _token: '{{ csrf_token() }}' };
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        } else {
                            alert('Gagal mengirim keberatan. Pastikan Nomor Registrasi Permohonan benar.');
                        }
                    } catch (error) {
                        alert('Terjadi kesalahan jaringan atau server.');
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            }"
            class="bg-white p-8 rounded-xl shadow-lg border border-gray-200">

                {{-- Tampilan Sukses --}}
                <div x-show="isSuccess" x-transition.opacity class="bg-red-50 border-l-4 border-red-600 text-red-800 p-4 mb-6" role="alert">
                    <div class="flex">
                        <div class="py-1"><svg class="fill-current h-6 w-6 text-red-600 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/></svg></div>
                        <div>
                            <p class="font-bold text-lg">Keberatan Berhasil Diajukan!</p>
                            <p class="text-sm">Keberatan Anda telah kami terima pada tanggal <span x-text="response.submission_date"></span>.</p>
                            <p class="font-semibold mt-2">Nomor Registrasi Keberatan Anda:</p>
                            <p class="text-xl text-red-600" x-text="response.objection_number"></p>
                            <p class="text-xs mt-1">Kami akan merespon keberatan Anda dalam waktu paling lambat 30 hari kerja.</p>
                        </div>
                    </div>
                </div>

                {{-- FORMULIR UTAMA --}}
                <form @submit.prevent="submitForm()" x-show="!isSuccess" x-transition>
                    <input type="hidden" name="_token" :value="form._token">

                    <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">Informasi Keberatan</h2>

                    {{-- Nomor Registrasi Permohonan --}}
                    <div class="mb-6">
                        <label for="registration_number" class="block text-sm font-medium text-gray-700">Nomor Registrasi Permohonan Informasi Publik Sebelumnya</label>
                        <input x-model="form.registration_number" type="text" id="registration_number" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-red-600 focus:border-red-600" placeholder="Contoh: PPID-FSTJ/20251010/1234">
                        <p class="text-xs text-gray-500 mt-1">Nomor ini didapatkan saat Anda mengajukan permohonan informasi publik.</p>
                    </div>

                    {{-- Alasan Keberatan --}}
                    <div class="mb-6">
                        <label for="objection_reason" class="block text-sm font-medium text-gray-700">Alasan Pengajuan Keberatan</label>
                        <select x-model="form.objection_reason" id="objection_reason" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-red-600 focus:border-red-600">
                            <option value="ditolak">Permohonan Informasi Ditolak</option>
                            <option value="tidak_disediakan">Informasi Tidak Disediakan</option>
                            <option value="tidak_ditanggapi">Permintaan Tidak Ditanggapi</option>
                            <option value="tidak_sesuai">Informasi yang Diberikan Tidak Sesuai</option>
                            <option value="tidak_dikenai_biaya">Pengenaan Biaya yang Tidak Wajar</option>
                            <option value="melebihi_jangka_waktu">Permintaan Melebihi Jangka Waktu Tanggapan</option>
                        </select>
                    </div>

                    {{-- Tujuan Keberatan --}}
                    <div class="mb-6">
                        <label for="objection_purpose" class="block text-sm font-medium text-gray-700">Penjelasan Detail Keberatan dan Permintaan Anda</label>
                        <textarea x-model="form.objection_purpose" id="objection_purpose" rows="4" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-red-600 focus:border-red-600" placeholder="Jelaskan mengapa Anda mengajukan keberatan dan apa yang Anda harapkan sebagai resolusi."></textarea>
                    </div>

                    <h2 class="text-2xl font-bold text-gray-800 mt-10 mb-6 border-b pb-2">Data Pemohon Keberatan</h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Nama Lengkap --}}
                        <div>
                            <label for="objection_full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                            <input x-model="form.full_name" type="text" id="objection_full_name" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-red-600 focus:border-red-600">
                        </div>

                        {{-- No. Telepon --}}
                        <div>
                            <label for="objection_phone" class="block text-sm font-medium text-gray-700">Nomor Telepon/HP</label>
                            <input x-model="form.phone" type="tel" id="objection_phone" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-red-600 focus:border-red-600">
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="objection_email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input x-model="form.email" type="email" id="objection_email" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-red-600 focus:border-red-600">
                        </div>
                    </div>


                    {{-- Tombol Submit --}}
                    <div class="mt-10 pt-6 border-t flex justify-end">
                        <button type="submit" :disabled="isSubmitting"
                            class="inline-flex justify-center py-3 px-8 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-600 transition duration-300">
                            <span x-show="!isSubmitting">Ajukan Keberatan</span>
                            <span x-show="isSubmitting">Mengirim...</span>
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </section>

@endsection
