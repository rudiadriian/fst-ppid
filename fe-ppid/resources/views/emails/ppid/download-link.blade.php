<x-mail::message>
# Tautan Unduh Laporan PPID

Yth. Bapak/Ibu **{{ $name }}**,

Terima kasih atas permintaan Anda untuk mengunduh **{{ $title }}** melalui PPID Food Station Tjipinang Jaya.

Silakan klik tombol di bawah ini untuk memulai proses unduh dokumen Anda. Tautan ini bersifat terbatas dan hanya berlaku untuk laporan yang Anda minta.

<x-mail::button :url="$url">
Unduh Laporan {{ $title }}
</x-mail::button>

**Detail Permintaan Anda:**
* **Nama Dokumen:** {{ $title }}
* **Tujuan Email:** {{ $name }}
* **Tautan Berlaku hingga:** [Jika Anda memiliki batas waktu, sebutkan di sini]

Jika Anda memiliki pertanyaan lebih lanjut atau membutuhkan bantuan, jangan ragu untuk menghubungi layanan PPID kami.

Hormat kami,

**Tim PPID Food Station Tjipinang Jaya**
[ppid@foodstation.co.id](mailto:ppid@foodstation.co.id)
</x-mail::message>
