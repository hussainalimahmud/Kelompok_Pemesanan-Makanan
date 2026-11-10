# MindCare SPK — Sistem Skrining Kesehatan Mental Mahasiswa Berbasis Machine Learning

Aplikasi web berbasis **PHP Native** dan **Machine Learning (Python/Scikit-Learn)** untuk mata kuliah Sistem Informasi. Sistem ini berfungsi sebagai **Sistem Pendukung Keputusan (SPK)** dalam mendeteksi tingkat stres dan beban akademik mahasiswa, serta memberikan sarana pemantauan & penjadwalan bimbingan bagi Dosen Pembimbing Akademik (PA).

Sistem ini telah diperbarui menggunakan **Pola Relasional Master-Detail** dan tampilan visual bertema **Modern Glassmorphism** (warna lembut pastel dengan efek frosted glass, bebas warna biru pekat/ngejreng).

---

## 🏛️ Arsitektur Database (Pola Master-Detail)
Struktur database `db_spk_mental` menggunakan pola ternormalisasi dan relasi Master-Detail:

1. **`program_studi` (Master Prodi):** Menyimpan kode, nama prodi, jenjang, dan fakultas.
2. **`dosen` (Master Dosen PA):** 5 Dosen Pembimbing Akademik (NIDN, nama, username, password unik, email, prodi_id).
3. **`mahasiswa` (Master Mahasiswa):** 100 Mahasiswa (NIM, nama, password unik, prodi_id, angkatan, gender, usia, `dosen_id`).
   * **Relasi Perwalian:** Tepat **1 Dosen PA mengampu 20 Mahasiswa Perwalian**.
4. **`kategori_indikator` (Master Kategori Kuesioner):**
   * Akademik & Pembelajaran
   * Finansial & Pekerjaan
   * Gaya Hidup & Kesejahteraan Psikologis
5. **`pertanyaan_indikator` (Master Metadata Indikator):** 12 butir fitur Machine Learning, tipe input, opsi JSON, keterangan, dan bobot.
6. **`skrining_master` (Master Header Sesi Skrining):**
   * Menyimpan header transaksi sesi skrining: `kode_sesi` (misal: `SKR-2026-0001`), `mahasiswa_id`, `dosen_id`, `ipk_snapshot`, hasil klasifikasi AI (0/1), kategori, probabilitas, 3 faktor risiko utama, dan status konseling.
7. **`skrining_detail` (Detail Jawaban Kuesioner):**
   * **Implementasi Child/Detail dari Master:** Menyimpan rincian 12 jawaban per indikator (`skrining_id`, `pertanyaan_id`, `kode_fitur`, `nilai_raw`, `nilai_numerik`, `interpretasi_singkat`).
8. **`konseling_riwayat` (Detail Log Bimbingan Konseling):**
   * Menyimpan riwayat catatan pertemuan/bimbingan dosen PA dan mahasiswa dari waktu ke waktu (`tanggal_konseling`, `tempat_metode`, `catatan_bimbingan`, `solusi_tindak_lanjut`, `status_pertemuan`).

---

## 🎨 Desain Antarmuka: Modern Glassmorphism
* **Canvas Ambient:** Latar belakang mesh gradient lembut (*subtle twilight slate, pale sage, and soft lavender*).
* **Frosted Glass Cards:** Efek kaca buram transparan (`backdrop-filter: blur(16px)` & `rgba(255, 255, 255, 0.65)`).
* **Smooth Tones:** Aksen warna pastel lembut (soft emerald, muted rose/coral, warm amber, dan soft indigo) yang ramah di mata.

---

## 👥 Akun Pengguna & Relasi Perwalian
Kredensial lengkap tercatat di file [**`DAFTAR_AKUN.md`**](DAFTAR_AKUN.md).
* **Dosen 1** (`dosen_hendra` / `Dosen#Hendra81`) $\rightarrow$ Mengampu Mahasiswa `230101001` s/d `230101020`
* **Dosen 2** (`dosen_sri` / `Dosen@Wahyuni92`) $\rightarrow$ Mengampu Mahasiswa `230101021` s/d `230101040`
* **Dosen 3** (`dosen_agus` / `Dosen$Wibowo73`) $\rightarrow$ Mengampu Mahasiswa `230101041` s/d `230101060`
* **Dosen 4** (`dosen_dewi` / `Dosen%Lestari64`) $\rightarrow$ Mengampu Mahasiswa `230101061` s/d `230101080`
* **Dosen 5** (`dosen_bambang` / `Dosen&Nugroho55`) $\rightarrow$ Mengampu Mahasiswa `230101081` s/d `230101100`

---

## 🚀 Cara Menjalankan

1. **Jalankan MySQL:** Pastikan MySQL/Laragon aktif.
2. **Jalankan Server PHP:**
   * Double-click file **[`jalankan.bat`](jalankan.bat)**, atau
   * Jalankan via terminal: `php -S localhost:8000`
3. **Buka di Browser:**
   * URL: **`http://localhost:8000/login.php`**
