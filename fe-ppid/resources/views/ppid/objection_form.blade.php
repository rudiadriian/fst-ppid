@extends('layouts.app')

@section('title', 'Pengajuan Keberatan | PPID FSTJ')

@section('content')

    {{-- HERO --}}
    <section class="relative fs-gradient overflow-hidden">
        <div class="absolute inset-0 fs-dot-pattern opacity-40"></div>
        <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8 py-16 lg:py-20 text-center">
            <p class="text-sm font-semibold tracking-widest uppercase text-white/70 mb-4">{{ __('Layanan Informasi') }}</p>
            <h1 class="text-4xl lg:text-5xl font-bold text-white leading-tight">{{ __('Formulir Pengajuan Keberatan') }}</h1>
            <p class="mt-4 text-lg font-normal text-white/80 max-w-2xl mx-auto leading-relaxed">
                {{ __('Ajukan keberatan jika permohonan informasi publik Anda tidak dipenuhi atau tidak sesuai.') }}
            </p>
        </div>
    </section>

    {{-- KONTEN --}}
    <section class="py-16 lg:py-20 bg-[#F7F9F8] dark:bg-[#0d1310]">
        <div class="max-w-4xl mx-auto px-6 lg:px-8">
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
                            alert('{{ __('Gagal mengirim keberatan. Pastikan Nomor Registrasi Permohonan benar.') }}');
                        }
                    } catch (error) {
                        alert('{{ __('Terjadi kesalahan jaringan atau server.') }}');
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
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Keberatan Berhasil Diajukan!') }}</p>
                    <p class="text-base text-gray-500 dark:text-gray-400 dark:text-gray-500 mt-2">{{ __('Keberatan Anda telah kami terima pada tanggal') }} <span class="font-semibold text-gray-900 dark:text-white" x-text="response.submission_date"></span>.</p>

                    <div class="mt-7 inline-block p-6 fs-gradient rounded-2xl text-center">
                        <p class="text-xs font-semibold text-white/80 uppercase tracking-wider">{{ __('Nomor Registrasi Keberatan Anda') }}</p>
                        <p class="text-3xl font-extrabold text-white mt-1" x-text="response.objection_number"></p>
                    </div>

                    <p class="text-sm mt-4 text-gray-400 dark:text-gray-500">{{ __('Kami akan memberikan tanggapan tertulis paling lambat 30 hari kerja setelah surat keberatan diterima.') }}</p>
                    <button @click="isSuccess = false; window.scrollTo({ top: 0, behavior: 'smooth' })" class="mt-6 text-sm font-semibold text-[#008060] hover:text-[#00664e]">
                        {{ __('Ajukan Keberatan Lain') }}
                    </button>
                </div>

                {{-- Notice --}}
                <div x-show="!isSuccess" class="p-5 bg-amber-50 border border-amber-100 rounded-2xl mb-8 flex items-start gap-4">
                    <span class="w-10 h-10 flex-shrink-0 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </span>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('Perhatikan Prosedur') }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1 leading-relaxed">{{ __('Pengajuan keberatan hanya berlaku untuk permohonan informasi publik yang sudah pernah diajukan sebelumnya dan mendapat tanggapan yang dianggap tidak memuaskan.') }}</p>
                    </div>
                </div>

                <form @submit.prevent="submitForm()" x-show="!isSuccess" x-transition>
                    <input type="hidden" name="_token" :value="form._token">

                    @php
                        $inputClass = 'mt-1.5 block w-full px-4 py-3 bg-gray-50 border border-gray-200 dark:border-white/10 rounded-xl focus:bg-white dark:bg-[#121a17] focus:border-[#008060] focus:ring-2 focus:ring-[#008060]/15 outline-none transition-all text-base';
                        $labelClass = 'block text-sm font-medium text-gray-700';
                    @endphp

                    {{-- Detail Keberatan --}}
                    <div class="p-6 bg-[#F7F9F8] dark:bg-[#0d1310] rounded-2xl border border-gray-100 dark:border-white/10 mb-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2.5">
                            <span class="w-9 h-9 bg-emerald-50 text-[#008060] rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </span>
                            Detail Keberatan
                        </h2>
                        <div class="mb-5">
                            <label for="registration_number" class="{{ $labelClass }}">{{ __('Nomor Registrasi Permohonan Sebelumnya') }}</label>
                            <input x-model="form.registration_number" type="text" id="registration_number" required class="{{ $inputClass }}" placeholder="{{ __('Contoh: PPID-FSTJ/20251010/1234') }}">
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5">{{ __('Nomor ini wajib diisi dan didapat saat Anda mengajukan permohonan informasi.') }}</p>
                        </div>
                        <div class="mb-5">
                            <label for="objection_reason" class="{{ $labelClass }}">{{ __('Alasan Pengajuan Keberatan') }}</label>
                            <select x-model="form.objection_reason" id="objection_reason" required class="{{ $inputClass }}">
                                <option value="ditolak">{{ __('Permohonan Informasi Ditolak') }}</option>
                                <option value="tidak_disediakan">{{ __('Informasi Tidak Disediakan') }}</option>
                                <option value="tidak_ditanggapi">{{ __('Permintaan Tidak Ditanggapi') }}</option>
                                <option value="tidak_sesuai">{{ __('Informasi yang Diberikan Tidak Sesuai') }}</option>
                                <option value="tidak_dikenai_biaya">{{ __('Pengenaan Biaya yang Tidak Wajar') }}</option>
                                <option value="melebihi_jangka_waktu">{{ __('Permintaan Melebihi Jangka Waktu Tanggapan') }}</option>
                            </select>
                        </div>
                        <div>
                            <label for="objection_purpose" class="{{ $labelClass }}">{{ __('Penjelasan Detail Keberatan dan Permintaan Anda') }}</label>
                            <textarea x-model="form.objection_purpose" id="objection_purpose" rows="4" required class="{{ $inputClass }}" placeholder="{{ __('Jelaskan mengapa Anda mengajukan keberatan dan apa yang Anda harapkan sebagai resolusi.') }}"></textarea>
                        </div>
                    </div>

                    {{-- Data Pemohon --}}
                    <div class="p-6 bg-[#F7F9F8] dark:bg-[#0d1310] rounded-2xl border border-gray-100 dark:border-white/10">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2.5">
                            <span class="w-9 h-9 bg-emerald-50 text-[#008060] rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </span>
                            Data Pemohon (Sesuai Permohonan Awal)
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div>
                                <label for="objection_full_name" class="{{ $labelClass }}">{{ __('Nama Lengkap') }}</label>
                                <input x-model="form.full_name" type="text" id="objection_full_name" required class="{{ $inputClass }}">
                            </div>
                            <div>
                                <label for="objection_phone" class="{{ $labelClass }}">{{ __('Nomor Telepon/HP') }}</label>
                                <input x-model="form.phone" type="tel" id="objection_phone" required class="{{ $inputClass }}">
                            </div>
                            <div>
                                <label for="objection_email" class="{{ $labelClass }}">{{ __('Email') }}</label>
                                <input x-model="form.email" type="email" id="objection_email" required class="{{ $inputClass }}">
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button type="submit" :disabled="isSubmitting"
                            class="inline-flex items-center justify-center py-3.5 px-10 fs-gradient text-white text-base font-semibold rounded-xl shadow-lg shadow-emerald-900/20 hover:-translate-y-0.5 transition-all duration-300 disabled:opacity-70 disabled:cursor-not-allowed">
                            <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span x-show="!isSubmitting">{{ __('Ajukan Keberatan') }}</span>
                            <span x-show="isSubmitting">{{ __('Memproses Keberatan...') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

@endsection
