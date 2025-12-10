@extends('layouts.app')

@section('title', 'Permohonan Informasi Publik | PPID FSTJ')

@section('content')
    <section class="relative bg-gradient-to-r from-emerald-900 to-gray-900 py-16 lg:py-24 overflow-hidden">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 30px 30px;"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
            <h1 class="text-4xl sm:text-5xl font-extrabold text-white leading-tight drop-shadow">
                Formulir Permohonan Informasi Publik
            </h1>
            <p class="mt-4 text-lg text-emerald-200 max-w-3xl mx-auto">
                Isi formulir ini untuk mengajukan permohonan informasi yang Anda butuhkan secara resmi.
            </p>
        </div>
    </section>
    <section class="py-16 lg:py-24 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div x-data="{
                form: {
                    full_name: '',
                    id_number: '',
                    address: '',
                    phone: '',
                    email: '',
                    applicant_type: 'Pribadi',
                    institution_name: '',
                    info_needed: '',
                    purpose: '',
                    format: 'softcopy',
                    way_to_get: 'email',
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
                            applicant_name: this.form.full_name,
                            registration_number: 'PPID-FSTJ-' + Math.floor(Math.random() * 10000)
                        };

                        const data = simulatedData;

                        if (data.success) {
                            this.response = data;
                            this.isSuccess = true;
                            this.form = {
                                full_name: '', id_number: '', address: '', phone: '', email: '',
                                applicant_type: 'Pribadi', institution_name: '', info_needed: '',
                                purpose: '', format: 'softcopy', way_to_get: 'email', _token: '{{ csrf_token() }}'
                            };
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        } else {
                            alert('Gagal mengirim permohonan. Periksa kembali isian Anda.');
                        }
                    } catch (error) {
                        alert('Terjadi kesalahan jaringan atau server.');
                        console.error('Fetch error:', error);
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            }"
            class="bg-white p-8 sm:p-10 lg:p-12 rounded-3xl shadow-xl border border-gray-100">
                <div x-show="isSuccess" x-transition.opacity class="bg-emerald-50 border-4 border-[#008060] text-gray-800 p-8 rounded-2xl mb-8 text-center" role="alert">
                    <div class="flex flex-col items-center">
                        <svg class="w-16 h-16 text-[#008060] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>

                        <p class="font-extrabold text-2xl text-gray-900">Permohonan Berhasil Diajukan!</p>
                        <p class="text-base mt-2">Terima kasih, <span class="font-semibold text-gray-900" x-text="response.applicant_name"></span>. Permohonan Anda telah kami terima.</p>

                        <div class="mt-6 p-4 bg-white border border-gray-200 rounded-xl">
                            <p class="font-semibold text-sm text-gray-600 uppercase tracking-wider">Nomor Registrasi Anda:</p>
                            <p class="text-3xl font-black text-[#008060] mt-1" x-text="response.registration_number"></p>
                        </div>

                        <p class="text-xs mt-3 text-gray-500">Simpan nomor ini untuk melacak status permohonan Anda pada menu Cek Status Tiket.</p>

                        <button @click="isSuccess = false; window.scrollTo({ top: 0, behavior: 'smooth' })" class="mt-6 text-sm font-semibold text-[#008060] hover:underline">
                            Ajukan Permohonan Baru
                        </button>
                    </div>
                </div>
                <div x-show="!isSuccess" class="p-4 sm:p-6 bg-yellow-50 border border-yellow-200 rounded-xl mb-8 flex items-start space-x-4">
                    <svg class="w-6 h-6 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.398 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <div>
                        <h3 class="font-bold text-gray-800">Perhatian Penting</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Mohon isi semua data dengan **BENAR** dan **LENGKAP**. Nomor identitas, email, dan telepon akan digunakan sebagai validasi permohonan Anda.
                        </p>
                    </div>
                </div>
                <form @submit.prevent="submitForm()" x-show="!isSuccess" x-transition>
                    <input type="hidden" name="_token" :value="form._token">
                    <div class="p-6 bg-gray-50 rounded-xl border border-gray-200 mb-10">
                        <h2 class="text-xl font-bold text-gray-800 mb-6 border-b pb-2 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-[#008060]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Data Pemohon
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                <input x-model="form.full_name" type="text" id="full_name" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-3 focus:ring-[#008060] focus:border-[#008060]">
                            </div>
                            <div>
                                <label for="id_number" class="block text-sm font-medium text-gray-700">NIK / Nomor Identitas (KTP/Paspor)</label>
                                <input x-model="form.id_number" type="text" id="id_number" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-3 focus:ring-[#008060] focus:border-[#008060]">
                            </div>
                        </div>
                        <div class="mt-6">
                            <label for="address" class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                            <textarea x-model="form.address" id="address" rows="2" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-3 focus:ring-[#008060] focus:border-[#008060]"></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">Nomor Telepon/HP</label>
                                <input x-model="form.phone" type="tel" id="phone" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-3 focus:ring-[#008060] focus:border-[#008060]">
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input x-model="form.email" type="email" id="email" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-3 focus:ring-[#008060] focus:border-[#008060]">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label for="applicant_type" class="block text-sm font-medium text-gray-700">Tipe Pemohon</label>
                                <select x-model="form.applicant_type" id="applicant_type" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-3 focus:ring-[#008060] focus:border-[#008060]">
                                    <option>Pribadi</option>
                                    <option>Instansi</option>
                                    <option>Kelompok</option>
                                </select>
                            </div>
                            <div x-show="form.applicant_type !== 'Pribadi'" x-transition>
                                <label for="institution_name" class="block text-sm font-medium text-gray-700">Nama Instansi/Lembaga/Kelompok</label>
                                <input x-model="form.institution_name" type="text" id="institution_name" :required="form.applicant_type !== 'Pribadi'" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-3 focus:ring-[#008060] focus:border-[#008060]">
                            </div>
                        </div>
                    </div>
                    <div class="p-6 bg-gray-50 rounded-xl border border-gray-200 mb-10">
                        <h2 class="text-xl font-bold text-gray-800 mb-6 border-b pb-2 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Detail Permintaan
                        </h2>
                        <div class="mt-6">
                            <label for="info_needed" class="block text-sm font-medium text-gray-700">Informasi yang Dibutuhkan</label>
                            <textarea x-model="form.info_needed" id="info_needed" rows="3" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-3 focus:ring-[#008060] focus:border-[#008060]" placeholder="Jelaskan secara spesifik informasi yang Anda butuhkan (contoh: Laporan Keuangan Tahun 2023 yang sudah diaudit)"></textarea>
                        </div>
                        <div class="mt-6">
                            <label for="purpose" class="block text-sm font-medium text-gray-700">Tujuan Penggunaan Informasi</label>
                            <textarea x-model="form.purpose" id="purpose" rows="3" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-3 focus:ring-[#008060] focus:border-[#008060]" placeholder="Jelaskan tujuan Anda menggunakan informasi ini (contoh: Untuk keperluan penelitian akademik/analisis pasar)"></textarea>
                        </div>
                    </div>
                    <div class="p-6 bg-gray-50 rounded-xl border border-gray-200">
                        <h2 class="text-xl font-bold text-gray-800 mb-6 border-b pb-2 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            Cara Mendapatkan Informasi
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Format Informasi</label>
                                <div class="space-y-3">
                                    <label class="flex items-center p-3 bg-white rounded-lg border border-gray-300 hover:border-[#008060] transition cursor-pointer">
                                        <input type="radio" x-model="form.format" value="softcopy" class="form-radio text-[#008060] focus:ring-[#008060]">
                                        <span class="ml-3 text-gray-700 font-medium">Softcopy (Digital)</span>
                                    </label>
                                    <label class="flex items-center p-3 bg-white rounded-lg border border-gray-300 hover:border-[#008060] transition cursor-pointer">
                                        <input type="radio" x-model="form.format" value="hardcopy" class="form-radio text-[#008060] focus:ring-[#008060]">
                                        <span class="ml-3 text-gray-700 font-medium">Hardcopy (Cetak)</span>
                                    </label>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Cara Mendapatkan</label>
                                <div class="space-y-3">
                                    <label class="flex items-center p-3 bg-white rounded-lg border border-gray-300 hover:border-[#008060] transition cursor-pointer">
                                        <input type="radio" x-model="form.way_to_get" value="email" class="form-radio text-[#008060] focus:ring-[#008060]">
                                        <span class="ml-3 text-gray-700 font-medium">Melalui Email</span>
                                    </label>
                                    <label class="flex items-center p-3 bg-white rounded-lg border border-gray-300 hover:border-[#008060] transition cursor-pointer">
                                        <input type="radio" x-model="form.way_to_get" value="ambil_langsung" class="form-radio text-[#008060] focus:ring-[#008060]">
                                        <span class="ml-3 text-gray-700 font-medium">Diambil Langsung di Kantor</span>
                                    </label>
                                    <label class="flex items-center p-3 bg-white rounded-lg border border-gray-300 hover:border-[#008060] transition cursor-pointer">
                                        <input type="radio" x-model="form.way_to_get" value="pos" class="form-radio text-[#008060] focus:ring-[#008060]">
                                        <span class="ml-3 text-gray-700 font-medium">Melalui Pos (Hardcopy)</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-12 pt-6 border-t border-gray-200 flex justify-end">
                        <button type="submit" :disabled="isSubmitting"
                            class="inline-flex items-center justify-center py-3 px-10 border border-transparent shadow-xl text-lg font-bold rounded-full text-white bg-[#008060] hover:bg-[#00664e] focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-[#008060]/50 transition duration-300">
                            <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span x-show="!isSubmitting">Kirim Permohonan</span>
                            <span x-show="isSubmitting">Memproses Permohonan...</span>
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </section>

@endsection
