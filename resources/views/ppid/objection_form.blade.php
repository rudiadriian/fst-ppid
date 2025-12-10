@extends('layouts.app')

@section('title', 'Pengajuan Keberatan | PPID FSTJ')

@section('content')
    <section class="relative bg-gradient-to-r from-emerald-900 to-gray-900 py-16 lg:py-24 overflow-hidden">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 30px 30px;"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
            <h1 class="text-4xl sm:text-5xl font-extrabold text-white leading-tight drop-shadow">
                Formulir Pengajuan Keberatan
            </h1>
            <p class="mt-4 text-lg text-red-200 max-w-3xl mx-auto">
                Ajukan keberatan jika permohonan informasi publik Anda tidak dipenuhi atau tidak sesuai.
            </p>
        </div>
    </section>
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
                        await new Promise(resolve => setTimeout(resolve, 1500));

                        const simulatedData = {
                            success: true,
                            objection_number: 'KEBERATAN-FSTJ-' + Math.floor(Math.random() * 10000),
                            submission_date: new Date().toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'})
                        };
                        const data = simulatedData;

                        if (data.success) {
                            this.response = data;
                            this.isSuccess = true;
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
            class="bg-white p-8 sm:p-10 lg:p-12 rounded-3xl shadow-xl border border-gray-100">
                <div x-show="isSuccess" x-transition.opacity class="bg-red-50 border-4 border-red-600 text-gray-800 p-8 rounded-2xl mb-8 text-center" role="alert">
                    <div class="flex flex-col items-center">
                        <svg class="w-16 h-16 text-red-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.398 16c-.77 1.333.192 3 1.732 3z"></path></svg>

                        <p class="font-extrabold text-2xl text-gray-900">Keberatan Berhasil Diajukan!</p>
                        <p class="text-base mt-2">Keberatan Anda telah kami terima pada tanggal <span class="font-semibold text-gray-900" x-text="response.submission_date"></span>.</p>

                        <div class="mt-6 p-4 bg-white border border-gray-200 rounded-xl">
                            <p class="font-semibold text-sm text-gray-600 uppercase tracking-wider">Nomor Registrasi Keberatan Anda:</p>
                            <p class="text-3xl font-black text-red-600 mt-1" x-text="response.objection_number"></p>
                        </div>

                        <p class="text-xs mt-3 text-gray-500">Kami akan memberikan tanggapan tertulis paling lambat 30 hari kerja setelah diterimanya surat keberatan.</p>

                        <button @click="isSuccess = false; window.scrollTo({ top: 0, behavior: 'smooth' })" class="mt-6 text-sm font-semibold text-red-600 hover:underline">
                            Ajukan Keberatan Lain
                        </button>
                    </div>
                </div>
                <div x-show="!isSuccess" class="p-4 sm:p-6 bg-red-50 border border-red-200 rounded-xl mb-8 flex items-start space-x-4">
                    <svg class="w-6 h-6 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <h3 class="font-bold text-gray-800">Perhatikan Prosedur</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Pengajuan keberatan hanya berlaku untuk permohonan informasi publik yang **sudah pernah diajukan** sebelumnya dan mendapatkan tanggapan yang dianggap tidak memuaskan.
                        </p>
                    </div>
                </div>
                <form @submit.prevent="submitForm()" x-show="!isSuccess" x-transition>
                    <input type="hidden" name="_token" :value="form._token">
                    <div class="p-6 bg-gray-50 rounded-xl border border-gray-200 mb-10">
                        <h2 class="text-xl font-bold text-gray-800 mb-6 border-b pb-2 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Detail Keberatan
                        </h2>
                        <div class="mb-6">
                            <label for="registration_number" class="block text-sm font-medium text-gray-700">Nomor Registrasi Permohonan Informasi Publik Sebelumnya</label>
                            <input x-model="form.registration_number" type="text" id="registration_number" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-3 focus:ring-red-600 focus:border-red-600" placeholder="Contoh: PPID-FSTJ/20251010/1234">
                            <p class="text-xs text-gray-500 mt-1">Nomor ini wajib diisi dan didapatkan saat Anda mengajukan permohonan informasi.</p>
                        </div>
                        <div class="mb-6">
                            <label for="objection_reason" class="block text-sm font-medium text-gray-700">Alasan Pengajuan Keberatan</label>
                            <select x-model="form.objection_reason" id="objection_reason" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-3 focus:ring-red-600 focus:border-red-600">
                                <option value="ditolak">Permohonan Informasi Ditolak</option>
                                <option value="tidak_disediakan">Informasi Tidak Disediakan</option>
                                <option value="tidak_ditanggapi">Permintaan Tidak Ditanggapi</option>
                                <option value="tidak_sesuai">Informasi yang Diberikan Tidak Sesuai</option>
                                <option value="tidak_dikenai_biaya">Pengenaan Biaya yang Tidak Wajar</option>
                                <option value="melebihi_jangka_waktu">Permintaan Melebihi Jangka Waktu Tanggapan</option>
                            </select>
                        </div>
                        <div class="mb-6">
                            <label for="objection_purpose" class="block text-sm font-medium text-gray-700">Penjelasan Detail Keberatan dan Permintaan Anda</label>
                            <textarea x-model="form.objection_purpose" id="objection_purpose" rows="4" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-3 focus:ring-red-600 focus:border-red-600" placeholder="Jelaskan mengapa Anda mengajukan keberatan dan apa yang Anda harapkan sebagai resolusi."></textarea>
                        </div>
                    </div>
                    <div class="p-6 bg-gray-50 rounded-xl border border-gray-200">
                         <h2 class="text-xl font-bold text-gray-800 mb-6 border-b pb-2 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-[#008060]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Data Pemohon (Sesuai Permohonan Awal)
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="objection_full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                <input x-model="form.full_name" type="text" id="objection_full_name" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-3 focus:ring-red-600 focus:border-red-600">
                            </div>
                            <div>
                                <label for="objection_phone" class="block text-sm font-medium text-gray-700">Nomor Telepon/HP</label>
                                <input x-model="form.phone" type="tel" id="objection_phone" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-3 focus:ring-red-600 focus:border-red-600">
                            </div>
                            <div>
                                <label for="objection_email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input x-model="form.email" type="email" id="objection_email" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-3 focus:ring-red-600 focus:border-red-600">
                            </div>
                        </div>
                    </div>
                    <div class="mt-12 pt-6 border-t border-gray-200 flex justify-end">
                        <button type="submit" :disabled="isSubmitting"
                            class="inline-flex items-center justify-center py-3 px-10 border border-transparent shadow-xl text-lg font-bold rounded-full text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-red-600/50 transition duration-300">
                            <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span x-show="!isSubmitting">Ajukan Keberatan</span>
                            <span x-show="isSubmitting">Memproses Keberatan...</span>
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </section>

@endsection
