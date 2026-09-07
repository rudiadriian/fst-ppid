#!/usr/bin/env bash
#
# Pasang kunci reCAPTCHA ke .env milik api-ppid di server.
#
#   deploy/pasang-env-recaptcha.sh <direktori-api-ppid>
#
# Dipanggil deploy:api sebelum `config:cache`. Ini satu-satunya bagian pipeline
# yang menyentuh .env, dan hanya baris yang disebut di bawah — berkasnya tidak
# pernah ditimpa, sehingga nilai lain yang hidup di server tetap utuh.
#
# Dibuat setelah kunci yang harus dipasang tangan terlewat berkali-kali. Akibat
# terlewatnya tidak terlihat di mana pun: pipeline tetap hijau, panel tetap
# terbuka, dan yang gagal adalah setiap orang yang mencoba masuk.
#
# Variabel CI yang kosong TIDAK mengosongkan nilai yang sedang berjalan. Satu
# variabel yang lupa dibuat seharusnya tidak mematikan produksi, jadi bila
# nilainya tidak ada, .env dibiarkan apa adanya dan skrip mencetak alasannya —
# itu sekaligus cara membuktikan variabelnya sampai ke runner atau tidak.

set -euo pipefail

akar="${1:-.}"
berkas="$akar/.env"

# Perintah untuk membaca dan menyunting berkas milik www-data. Di runner lewat
# sudo; saat diuji di mesin sendiri, setel PASANG_ENV_TANPA_SUDO=1.
if [ "${PASANG_ENV_TANPA_SUDO:-}" = "1" ]; then
	sebagai_www() { "$@"; }
else
	sebagai_www() { sudo -u www-data "$@"; }
fi

if ! sebagai_www test -f "$berkas"; then
	echo "TIDAK ADA: $berkas — .env harus sudah ada di server sebelum deploy." >&2
	exit 1
fi

# Setel satu baris `KUNCI=nilai`: diganti di tempatnya bila kuncinya sudah ada,
# ditambahkan di akhir bila belum.
#
# Kunci dan nilainya sampai ke awk lewat lingkungan (ENVIRON), bukan lewat `-v`
# maupun disisipkan ke dalam pola seperti pada `sed s|…|…|`. Keduanya menafsirkan
# ulang isinya: sed memperlakukan `&` dan pembatasnya sebagai tanda baca, dan
# `-v` masih memproses escape backslash sehingga `\e` diam-diam menjadi `e`.
# ENVIRON menyerahkan nilainya apa adanya. Kunci reCAPTCHA memang hanya huruf,
# angka, `-` dan `_`, tetapi cara yang aman hanya selama nilainya kebetulan
# jinak bukanlah cara yang aman.
#
# Hasilnya dituang balik lewat `tee`, bukan `mv`: berkas aslinya tetap berkas
# yang sama, jadi kepemilikan dan izin milik www-data tidak berubah.
setel() {
	local kunci="$1"
	local nilai="$2"
	local sementara

	sementara="$(mktemp)"

	sebagai_www cat "$berkas" | PASANG_KUNCI="$kunci" PASANG_NILAI="$nilai" awk '
		BEGIN { k = ENVIRON["PASANG_KUNCI"]; v = ENVIRON["PASANG_NILAI"] }
		index($0, k "=") == 1 { print k "=" v; ada = 1; next }
		{ print }
		END { if (!ada) print k "=" v }
	' >"$sementara"

	sebagai_www tee "$berkas" <"$sementara" >/dev/null
	rm -f "$sementara"

	echo "${kunci} dipasang."
}

if [ -z "${PPID_RECAPTCHA_SECRET_KEY:-}" ]; then
	echo "PPID_RECAPTCHA_SECRET_KEY tidak tersedia di CI — .env server dibiarkan apa adanya."
	echo "Bila login ditolak dengan 'Verifikasi keamanan belum dikonfigurasi di server',"
	echo "variabelnya belum dibuat, atau ditandai Protected sementara branch ini tidak protected."
	exit 0
fi

setel PPID_RECAPTCHA_AKTIF true
setel PPID_RECAPTCHA_SECRET_KEY "$PPID_RECAPTCHA_SECRET_KEY"
