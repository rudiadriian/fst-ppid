@extends('layouts.app')

@section('title', 'Permohonan Informasi Publik | PPID FSTJ')

@section('content')

    {{-- HERO --}}
    <section class="relative fs-gradient overflow-hidden">
        <div class="absolute inset-0 fs-dot-pattern opacity-40"></div>
        <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8 py-16 lg:py-20 text-center">
            <p class="text-sm font-semibold tracking-widest uppercase text-white/70 mb-4">{{ __('Layanan Informasi') }}</p>
            <h1 class="text-4xl lg:text-5xl font-bold text-white leading-tight">{{ __('Formulir Permohonan Informasi Publik') }}</h1>
            <p class="mt-4 text-lg font-normal text-white/80 max-w-2xl mx-auto leading-relaxed">
                {{ __('Isi formulir ini untuk mengajukan permohonan informasi yang Anda butuhkan secara resmi.') }}
            </p>
        </div>
    </section>

    {{-- KONTEN --}}
    <section class="py-16 lg:py-20 bg-[#F7F9F8] dark:bg-[#0d1310]">
        <div class="max-w-4xl mx-auto px-6 lg:px-8">
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
                            alert('{{ __('Gagal mengirim permohonan. Periksa kembali isian Anda.') }}');
                        }
                    } catch (error) {
                        alert('{{ __('Terjadi kesalahan jaringan atau server.') }}');
                        console.error('Fetch error:', error);
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            }"
            class="bg-white dark:bg-[#121a17] p-6 sm:p-10 rounded-2xl shadow-sm border border-gray-100 dark:border-white/10">

                {{-- Success --}}
                <div x-show="isSuccess" x-transition.opacity class="text-center py-4" role="alert">
                    <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-5">
                        <svg class="w-10 h-10 text-[#008060]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Permohonan Berhasil Diajukan!') }}</p>
                    <p class="text-base text-gray-500 dark:text-gray-400 dark:text-gray-500 mt-2">{{ __('Terima kasih,') }} <span class="font-semibold text-gray-900 dark:text-white" x-text="response.applicant_name"></span>. {{ __('Permohonan Anda telah kami terima.') }}</p>

                    <div class="mt-7 inline-block p-6 fs-gradient rounded-2xl text-center">
                        <p class="text-xs font-semibold text-white/80 uppercase tracking-wider">{{ __('Nomor Registrasi Anda') }}</p>
                        <p class="text-3xl font-extrabold text-white mt-1" x-text="response.registration_number"></p>
                    </div>

                    <p class="text-sm mt-4 text-gray-400 dark:text-gray-500">{{ __('Simpan nomor ini untuk melacak status permohonan pada menu Cek Status Tiket.') }}</p>
                    <button @click="isSuccess = false; window.scrollTo({ top: 0, behavior: 'smooth' })" class="mt-6 text-sm font-semibold text-[#008060] hover:text-[#00664e]">
                        {{ __('Ajukan Permohonan Baru') }}
                    </button>
                </div>

                {{-- Notice --}}
                <div x-show="!isSuccess" class="p-5 bg-amber-50 border border-amber-100 rounded-2xl mb-8 flex items-start gap-4">
                    <span class="w-10 h-10 flex-shrink-0 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.398 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </span>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('Perhatian Penting') }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1 leading-relaxed">{{ __('Mohon isi semua data dengan benar dan lengkap. Nomor identitas, email, dan telepon digunakan sebagai validasi permohonan Anda.') }}</p>
                    </div>
                </div>

                <form @submit.prevent="submitForm()" x-show="!isSuccess" x-transition>
                    <input type="hidden" name="_token" :value="form._token">

                    @php
                        $inputClass = 'mt-1.5 block w-full px-4 py-3 bg-gray-50 border border-gray-200 dark:border-white/10 rounded-xl focus:bg-white dark:bg-[#121a17] focus:border-[#008060] focus:ring-2 focus:ring-[#008060]/15 outline-none transition-all text-base';
                        $labelClass = 'block text-sm font-medium text-gray-700';
                        $radioCardClass = 'flex items-center p-3.5 bg-gray-50 rounded-xl border border-gray-200 dark:border-white/10 hover:border-[#008060] has-[:checked]:border-[#008060] has-[:checked]:bg-emerald-50/50 transition cursor-pointer';
                    @endphp

                    {{-- Data Pemohon --}}
                    <div class="p-6 bg-[#F7F9F8] dark:bg-[#0d1310] rounded-2xl border border-gray-100 dark:border-white/10 mb-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2.5">
                            <span class="w-9 h-9 bg-emerald-50 text-[#008060] rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </span>
                            Data Pemohon
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="full_name" class="{{ $labelClass }}">{{ __('Nama Lengkap') }}</label>
                                <input x-model="form.full_name" type="text" id="full_name" required class="{{ $inputClass }}">
                            </div>
                            <div>
                                <label for="id_number" class="{{ $labelClass }}">{{ __('NIK / Nomor Identitas (KTP/Paspor)') }}</label>
                                <input x-model="form.id_number" type="text" id="id_number" required class="{{ $inputClass }}">
                            </div>
                        </div>
                        <div class="mt-5">
                            <label for="address" class="{{ $labelClass }}">{{ __('Alamat Lengkap') }}</label>
                            <textarea x-model="form.address" id="address" rows="2" required class="{{ $inputClass }}"></textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-5">
                            <div>
                                <label for="phone" class="{{ $labelClass }}">{{ __('Nomor Telepon/HP') }}</label>
                                <input x-model="form.phone" type="tel" id="phone" required class="{{ $inputClass }}">
                            </div>
                            <div>
                                <label for="email" class="{{ $labelClass }}">{{ __('Email') }}</label>
                                <input x-model="form.email" type="email" id="email" required class="{{ $inputClass }}">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-5">
                            <div>
                                <label for="applicant_type" class="{{ $labelClass }}">{{ __('Tipe Pemohon') }}</label>
                                <select x-model="form.applicant_type" id="applicant_type" class="{{ $inputClass }}">
                                    <option value="Pribadi">{{ __('Pribadi') }}</option>
                                    <option value="Instansi">{{ __('Instansi') }}</option>
                                    <option value="Kelompok">{{ __('Kelompok') }}</option>
                                </select>
                            </div>
                            <div x-show="form.applicant_type !== 'Pribadi'" x-transition>
                                <label for="institution_name" class="{{ $labelClass }}">{{ __('Nama Instansi/Lembaga/Kelompok') }}</label>
                                <input x-model="form.institution_name" type="text" id="institution_name" :required="form.applicant_type !== 'Pribadi'" class="{{ $inputClass }}">
                            </div>
                        </div>
                    </div>

                    {{-- Detail Permintaan --}}
                    <div class="p-6 bg-[#F7F9F8] dark:bg-[#0d1310] rounded-2xl border border-gray-100 dark:border-white/10 mb-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2.5">
                            <span class="w-9 h-9 bg-emerald-50 text-[#008060] rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </span>
                            Detail Permintaan
                        </h2>
                        <div>
                            <label for="info_needed" class="{{ $labelClass }}">{{ __('Informasi yang Dibutuhkan') }}</label>
                            <textarea x-model="form.info_needed" id="info_needed" rows="3" required class="{{ $inputClass }}" placeholder="{{ __('Jelaskan secara spesifik informasi yang Anda butuhkan (contoh: Laporan Keuangan Tahun 2023 yang sudah diaudit)') }}"></textarea>
                        </div>
                        <div class="mt-5">
                            <label for="purpose" class="{{ $labelClass }}">{{ __('Tujuan Penggunaan Informasi') }}</label>
                            <textarea x-model="form.purpose" id="purpose" rows="3" required class="{{ $inputClass }}" placeholder="{{ __('Jelaskan tujuan Anda menggunakan informasi ini (contoh: Untuk keperluan penelitian akademik/analisis pasar)') }}"></textarea>
                        </div>
                    </div>

                    {{-- Cara Mendapatkan --}}
                    <div class="p-6 bg-[#F7F9F8] dark:bg-[#0d1310] rounded-2xl border border-gray-100 dark:border-white/10">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2.5">
                            <span class="w-9 h-9 bg-emerald-50 text-[#008060] rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            </span>
                            Cara Mendapatkan Informasi
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">{{ __('Pilih Format Informasi') }}</label>
                                <div class="space-y-3">
                                    <label class="{{ $radioCardClass }}">
                                        <input type="radio" x-model="form.format" value="softcopy" class="text-[#008060] focus:ring-[#008060]">
                                        <span class="ml-3 text-gray-700 dark:text-gray-300 font-medium">{{ __('Softcopy (Digital)') }}</span>
                                    </label>
                                    <label class="{{ $radioCardClass }}">
                                        <input type="radio" x-model="form.format" value="hardcopy" class="text-[#008060] focus:ring-[#008060]">
                                        <span class="ml-3 text-gray-700 dark:text-gray-300 font-medium">{{ __('Hardcopy (Cetak)') }}</span>
                                    </label>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">{{ __('Pilih Cara Mendapatkan') }}</label>
                                <div class="space-y-3">
                                    <label class="{{ $radioCardClass }}">
                                        <input type="radio" x-model="form.way_to_get" value="email" class="text-[#008060] focus:ring-[#008060]">
                                        <span class="ml-3 text-gray-700 dark:text-gray-300 font-medium">{{ __('Melalui Email') }}</span>
                                    </label>
                                    <label class="{{ $radioCardClass }}">
                                        <input type="radio" x-model="form.way_to_get" value="ambil_langsung" class="text-[#008060] focus:ring-[#008060]">
                                        <span class="ml-3 text-gray-700 dark:text-gray-300 font-medium">{{ __('Diambil Langsung di Kantor') }}</span>
                                    </label>
                                    <label class="{{ $radioCardClass }}">
                                        <input type="radio" x-model="form.way_to_get" value="pos" class="text-[#008060] focus:ring-[#008060]">
                                        <span class="ml-3 text-gray-700 dark:text-gray-300 font-medium">{{ __('Melalui Pos (Hardcopy)') }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button type="submit" :disabled="isSubmitting"
                            class="inline-flex items-center justify-center py-3.5 px-10 fs-gradient text-white text-base font-semibold rounded-xl shadow-lg shadow-emerald-900/20 hover:-translate-y-0.5 transition-all duration-300 disabled:opacity-70 disabled:cursor-not-allowed">
                            <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span x-show="!isSubmitting">{{ __('Kirim Permohonan') }}</span>
                            <span x-show="isSubmitting">{{ __('Memproses Permohonan...') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

@endsection
