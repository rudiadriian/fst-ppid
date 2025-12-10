@extends('layouts.app')

@section('title', 'Permohonan Informasi Publik | PPID FSTJ')

@section('content')

    {{-- HEADER HALAMAN --}}
    <section class="bg-[#008060] py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-extrabold text-white">
                Formulir Permohonan Informasi Publik
            </h1>
            <p class="mt-3 text-lg text-[#e0f2f1]">
                Isi formulir ini untuk mengajukan permohonan informasi yang Anda butuhkan secara resmi.
            </p>
        </div>
    </section>

    {{-- KONTEN FORMULIR DENGAN ALPINE.JS --}}
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
                response: {}, // Untuk menyimpan data sukses dari server

                async submitForm() {
                    this.isSubmitting = true;
                    this.isSuccess = false;

                    try {
                        const response = await fetch('{{ route('ppid.request.submit') }}', {
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
                            // Reset form setelah berhasil
                            this.form = {
                                full_name: '', id_number: '', address: '', phone: '', email: '',
                                applicant_type: 'Pribadi', institution_name: '', info_needed: '',
                                purpose: '', format: 'softcopy', way_to_get: 'email', _token: '{{ csrf_token() }}'
                            };
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        } else {
                            // Tangani error validasi atau server
                            alert('Gagal mengirim permohonan. Periksa kembali isian Anda.');
                            console.error('Submission error:', data);
                        }
                    } catch (error) {
                        alert('Terjadi kesalahan jaringan atau server.');
                        console.error('Fetch error:', error);
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            }"
            class="bg-white p-8 rounded-xl shadow-lg border border-gray-200">

                {{-- Tampilan Sukses --}}
                <div x-show="isSuccess" x-transition.opacity class="bg-green-50 border-l-4 border-[#008060] text-green-700 p-4 mb-6" role="alert">
                    <div class="flex">
                        <div class="py-1"><svg class="fill-current h-6 w-6 text-[#008060] mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/></svg></div>
                        <div>
                            <p class="font-bold text-lg">Permohonan Berhasil Diajukan!</p>
                            <p class="text-sm">Terima kasih, <span x-text="response.applicant_name"></span>. Permohonan Anda telah kami terima.</p>
                            <p class="font-semibold mt-2">Nomor Registrasi Anda:</p>
                            <p class="text-xl text-[#008060]" x-text="response.registration_number"></p>
                            <p class="text-xs mt-1">Simpan nomor ini untuk melacak status permohonan Anda.</p>
                        </div>
                    </div>
                </div>

                {{-- FORMULIR UTAMA --}}
                <form @submit.prevent="submitForm()" x-show="!isSuccess" x-transition>
                    <input type="hidden" name="_token" :value="form._token">

                    <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">Data Pemohon</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Nama Lengkap --}}
                        <div>
                            <label for="full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                            <input x-model="form.full_name" type="text" id="full_name" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-[#008060] focus:border-[#008060]">
                        </div>

                        {{-- NIK / No. Identitas --}}
                        <div>
                            <label for="id_number" class="block text-sm font-medium text-gray-700">NIK / Nomor Identitas Lainnya (KTP/Paspor)</label>
                            <input x-model="form.id_number" type="text" id="id_number" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-[#008060] focus:border-[#008060]">
                        </div>
                    </div>

                    {{-- Alamat --}}
                    <div class="mt-6">
                        <label for="address" class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                        <textarea x-model="form.address" id="address" rows="2" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-[#008060] focus:border-[#008060]"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        {{-- No. Telepon --}}
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Nomor Telepon/HP</label>
                            <input x-model="form.phone" type="tel" id="phone" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-[#008060] focus:border-[#008060]">
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input x-model="form.email" type="email" id="email" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-[#008060] focus:border-[#008060]">
                        </div>
                    </div>

                    {{-- Tipe Pemohon & Nama Instansi --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                         <div>
                            <label for="applicant_type" class="block text-sm font-medium text-gray-700">Tipe Pemohon</label>
                            <select x-model="form.applicant_type" id="applicant_type" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-[#008060] focus:border-[#008060]">
                                <option>Pribadi</option>
                                <option>Instansi</option>
                                <option>Kelompok</option>
                            </select>
                        </div>
                        <div x-show="form.applicant_type !== 'Pribadi'" x-transition>
                            <label for="institution_name" class="block text-sm font-medium text-gray-700">Nama Instansi/Lembaga/Kelompok</label>
                            <input x-model="form.institution_name" type="text" id="institution_name" :required="form.applicant_type !== 'Pribadi'" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-[#008060] focus:border-[#008060]">
                        </div>
                    </div>

                    <h2 class="text-2xl font-bold text-gray-800 mt-10 mb-6 border-b pb-2">Detail Permintaan</h2>

                    {{-- Informasi yang dibutuhkan --}}
                    <div class="mt-6">
                        <label for="info_needed" class="block text-sm font-medium text-gray-700">Informasi yang Dibutuhkan</label>
                        <textarea x-model="form.info_needed" id="info_needed" rows="3" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-[#008060] focus:border-[#008060]" placeholder="Jelaskan secara spesifik informasi yang Anda butuhkan (contoh: Laporan Keuangan Tahun 2023 yang sudah diaudit)"></textarea>
                    </div>

                    {{-- Tujuan Penggunaan --}}
                    <div class="mt-6">
                        <label for="purpose" class="block text-sm font-medium text-gray-700">Tujuan Penggunaan Informasi</label>
                        <textarea x-model="form.purpose" id="purpose" rows="3" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-[#008060] focus:border-[#008060]" placeholder="Jelaskan tujuan Anda menggunakan informasi ini (contoh: Untuk keperluan penelitian akademik/analisis pasar)"></textarea>
                    </div>

                    <h2 class="text-2xl font-bold text-gray-800 mt-10 mb-6 border-b pb-2">Cara Mendapatkan Informasi</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Format --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Format Informasi</label>
                            <div class="mt-2 space-y-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" x-model="form.format" value="softcopy" class="form-radio text-[#008060] focus:ring-[#008060]">
                                    <span class="ml-2 text-gray-700">Softcopy (Digital)</span>
                                </label>
                                <label class="inline-flex items-center ml-6">
                                    <input type="radio" x-model="form.format" value="hardcopy" class="form-radio text-[#008060] focus:ring-[#008060]">
                                    <span class="ml-2 text-gray-700">Hardcopy (Cetak)</span>
                                </label>
                            </div>
                        </div>

                        {{-- Cara Mendapatkan --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cara Mendapatkan</label>
                            <div class="mt-2 space-y-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" x-model="form.way_to_get" value="ambil_langsung" class="form-radio text-[#008060] focus:ring-[#008060]">
                                    <span class="ml-2 text-gray-700">Diambil Langsung</span>
                                </label>
                                <label class="inline-flex items-center ml-6">
                                    <input type="radio" x-model="form.way_to_get" value="email" class="form-radio text-[#008060] focus:ring-[#008060]">
                                    <span class="ml-2 text-gray-700">Melalui Email</span>
                                </label>
                                <label class="inline-flex items-center ml-6">
                                    <input type="radio" x-model="form.way_to_get" value="pos" class="form-radio text-[#008060] focus:ring-[#008060]">
                                    <span class="ml-2 text-gray-700">Melalui Pos</span>
                                </label>
                            </div>
                        </div>
                    </div>


                    {{-- Tombol Submit --}}
                    <div class="mt-10 pt-6 border-t flex justify-end">
                        <button type="submit" :disabled="isSubmitting"
                            class="inline-flex justify-center py-3 px-8 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-[#008060] hover:bg-[#00664e] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#008060] transition duration-300">
                            <span x-show="!isSubmitting">Kirim Permohonan</span>
                            <span x-show="isSubmitting">Mengirim...</span>
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </section>

@endsection
