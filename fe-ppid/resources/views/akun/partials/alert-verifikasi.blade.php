{{-- Peringatan status Verifikasi Data Pemohon.
     Dipakai Dashboard dan modul Permohonan Informasi. --}}
@unless ($pemohon->dataTerverifikasi())
    <div class="p-5 rounded-2xl border flex flex-wrap items-start justify-between gap-4
                {{ $pemohon->verifikasiMenunggu() ? 'bg-blue-50 border-blue-100' : 'bg-amber-50 border-amber-100' }}">
        <div class="flex items-start gap-3">
            <span class="w-9 h-9 flex-shrink-0 rounded-xl flex items-center justify-center
                         {{ $pemohon->verifikasiMenunggu() ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.398 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </span>
            <div>
                <p class="text-sm font-bold {{ $pemohon->verifikasiMenunggu() ? 'text-blue-900' : 'text-amber-900' }}">
                    {{ __('Status Verifikasi Data Pemohon') }}: {{ $pemohon->labelStatusVerifikasi() }}
                </p>
                <p class="text-sm mt-1 {{ $pemohon->verifikasiMenunggu() ? 'text-blue-800' : 'text-amber-800' }}">
                    @if ($pemohon->verifikasiMenunggu())
                        {{ __('Berkas Anda sedang diperiksa petugas PPID. Pengajuan permohonan bisa dilakukan setelah data disetujui.') }}
                    @elseif ($pemohon->status_verifikasi === 'ditolak')
                        {{ __('Data Anda ditolak petugas. Perbaiki isian dan berkas KTP, lalu kirim ulang.') }}
                    @else
                        {{ __('Lengkapi Data Pemohon & Berkas dulu. Permohonan Informasi baru bisa diajukan setelah data Anda diverifikasi.') }}
                    @endif
                </p>
            </div>
        </div>

        <a href="{{ route('akun.data-pemohon') }}" class="inline-flex items-center gap-1.5 px-5 py-2.5 fs-gradient-accent text-white text-sm font-bold rounded-xl hover:-translate-y-0.5 transition-transform">
            {{ __('Buka Data Pemohon') }}
        </a>
    </div>
@endunless
