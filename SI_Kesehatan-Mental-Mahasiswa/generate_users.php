<?php
// Generator script untuk membuat Master-Detail Database Schema + 5 Dosen + 100 Mahasiswa

$dosen_data = [
    [
        'id' => 1,
        'nidn' => '0012058001',
        'nama_dosen' => 'Dr. Hendra Kusuma, M.Kom.',
        'username' => 'dosen_hendra',
        'password' => 'Dosen#Hendra81',
        'email' => 'hendra.kusuma@univ.ac.id'
    ],
    [
        'id' => 2,
        'nidn' => '0015088202',
        'nama_dosen' => 'Dr. Sri Wahyuni, M.T.',
        'username' => 'dosen_sri',
        'password' => 'Dosen@Wahyuni92',
        'email' => 'sri.wahyuni@univ.ac.id'
    ],
    [
        'id' => 3,
        'nidn' => '0021038503',
        'nama_dosen' => 'Agus Wibowo, S.Kom., M.Cs.',
        'username' => 'dosen_agus',
        'password' => 'Dosen$Wibowo73',
        'email' => 'agus.wibowo@univ.ac.id'
    ],
    [
        'id' => 4,
        'nidn' => '0008118704',
        'nama_dosen' => 'Dewi Lestari, S.T., M.Kom.',
        'username' => 'dosen_dewi',
        'password' => 'Dosen%Lestari64',
        'email' => 'dewi.lestari@univ.ac.id'
    ],
    [
        'id' => 5,
        'nidn' => '0030047905',
        'nama_dosen' => 'Bambang Nugroho, M.Sc., Ph.D.',
        'username' => 'dosen_bambang',
        'password' => 'Dosen&Nugroho55',
        'email' => 'bambang.nugroho@univ.ac.id'
    ]
];

$nama_depan_l = [
    'Ahmad', 'Budi', 'Dimas', 'Eko', 'Fajar', 'Gilang', 'Hafizh', 'Irfan', 'Joko', 'Kevin',
    'Lucky', 'Mahesa', 'Naufal', 'Oki', 'Pandu', 'Qori', 'Rizky', 'Satria', 'Taufik', 'Umar',
    'Vino', 'Wahyu', 'Xaverius', 'Yusuf', 'Zack', 'Bayu', 'Candra', 'Daffa', 'Erlangga', 'Farhan',
    'Galih', 'Handoko', 'Ilham', 'Julian', 'Kharisma', 'Luthfi', 'Mochamad', 'Nur', 'Panji', 'Raditya',
    'Raka', 'Rangga', 'Rayhan', 'Reza', 'Rio', 'Roni', 'Rudi', 'Ryan', 'Syahrul', 'Zulfa'
];

$nama_depan_p = [
    'Adinda', 'Annisa', 'Bella', 'Citra', 'Dina', 'Elsa', 'Fitri', 'Gita', 'Hana', 'Indah',
    'Jessica', 'Kartika', 'Laras', 'Melati', 'Nabila', 'Olivia', 'Putri', 'Qonita', 'Rani', 'Siti',
    'Tiara', 'Ulfa', 'Vina', 'Wulan', 'Yulia', 'Zahra', 'Alya', 'Berlian', 'Cahya', 'Dwi',
    'Endah', 'Fauziah', 'Gisela', 'Hani', 'Intan', 'Jihan', 'Kirana', 'Lestari', 'Maya', 'Nia',
    'Nadya', 'Nurul', 'Pratiwi', 'Rahmatia', 'Risma', 'Safira', 'Salma', 'Shinta', 'Tari', 'Vivi'
];

$nama_belakang = [
    'Pratama', 'Saputra', 'Kusuma', 'Hidayat', 'Santoso', 'Wijaya', 'Permana', 'Nugraha', 'Setiawan', 'Ramadhan',
    'Wibowo', 'Firmansyah', 'Syahputra', 'Maulana', 'Gunawan', 'Utomo', 'Subagyo', 'Kurniawan', 'Hermawan', 'Pradana',
    'Anggraini', 'Puspita', 'Wulandari', 'Rahmawati', 'Lestari', 'Sari', 'Kusumawati', 'Utami', 'Wardani', 'Handayani',
    'Astuti', 'Pertiwi', 'Novitasari', 'Fitriani', 'Cahyani', 'Indriyani', 'Susanti', 'Damayanti', 'Safitri', 'Maharani',
    'Siregar', 'Nasution', 'Lubis', 'Harahap', 'Ginting', 'Sembiring', 'Pasaribu', 'Hutapea', 'Simanjuntak', 'Sitompul'
];

$kata_sandi_prefix = [
    'Bintang', 'Cahaya', 'Garuda', 'Melati', 'Samudra', 'Mentari', 'Pelangi', 'Angkasa', 'Pustaka', 'Merdeka',
    'Zamrud', 'Permata', 'Ksatria', 'Pesona', 'Lentera', 'Laksana', 'Pintar', 'Cerdas', 'Mandiri', 'Sukses',
    'Semangat', 'Harapan', 'Impian', 'Gemilang', 'Prestasi', 'Bahagia', 'Harmoni', 'Pancasila', 'Wawasan', 'Nusantara'
];

$simbol = ['!', '@', '#', '$', '%', '&', '*'];

$mahasiswa_data = [];
$used_names = [];
$used_passwords = [];

for ($i = 1; $i <= 100; $i++) {
    $nim = sprintf("230101%03d", $i);
    $dosen_id = (int)ceil($i / 20); // 1-20 -> 1, 21-40 -> 2, dst.
    
    $is_laki = ($i % 2 === 1);
    $jk = $is_laki ? 'Laki-laki' : 'Perempuan';
    
    $max_attempts = 100;
    do {
        if ($is_laki) {
            $depan = $nama_depan_l[($i * 7 + rand(0, 49)) % count($nama_depan_l)];
        } else {
            $depan = $nama_depan_p[($i * 5 + rand(0, 49)) % count($nama_depan_p)];
        }
        $belakang = $nama_belakang[($i * 11 + rand(0, 49)) % count($nama_belakang)];
        $full_name = "{$depan} {$belakang}";
        $max_attempts--;
    } while (isset($used_names[$full_name]) && $max_attempts > 0);
    $used_names[$full_name] = true;

    do {
        $p_word = $kata_sandi_prefix[($i + rand(0, count($kata_sandi_prefix)-1)) % count($kata_sandi_prefix)];
        $p_sym = $simbol[$i % count($simbol)];
        $p_num = str_pad(($i * 13 + 7) % 999, 3, '0', STR_PAD_LEFT);
        $pass = "{$p_word}{$p_sym}{$p_num}";
    } while (isset($used_passwords[$pass]));
    $used_passwords[$pass] = true;

    $usia = 19 + (($i % 4));

    $mahasiswa_data[] = [
        'id' => $i,
        'nim' => $nim,
        'nama_mahasiswa' => $full_name,
        'password' => $pass,
        'jenis_kelamin' => $jk,
        'usia' => $usia,
        'prodi_id' => 1,
        'angkatan' => '2023',
        'dosen_id' => $dosen_id
    ];
}

// 12 Fitur Machine Learning
$pertanyaan_data = [
    [1, 1, 'jenis_kelamin', 'Jenis Kelamin Mahasiswa', 'pilihan', '["Laki-laki", "Perempuan"]', 'Karakteristik demografis biologis mahasiswa.', 1.0, 1],
    [2, 1, 'usia', 'Usia Mahasiswa (Tahun)', 'angka', NULL, 'Usia kronologis dalam tahun.', 1.0, 2],
    [3, 1, 'ipk', 'Indeks Prestasi Kumulatif (IPK Terakhir)', 'desimal', NULL, 'Nilai IPK skala 0.00 - 4.00 (dikonversi ke skala model).', 2.0, 3],
    [4, 1, 'tekanan_akademik', 'Tingkat Tekanan Akademik yang Dirasakan', 'skala_1_5', '{"1":"Sangat Ringan","2":"Ringan","3":"Sedang","4":"Berat","5":"Sangat Berat"}', 'Beban tugas, ujian, dan tenggat waktu perkuliahan.', 3.0, 4],
    [5, 1, 'kepuasan_belajar', 'Kepuasan Terhadap Proses & Lingkungan Belajar', 'skala_1_5', '{"1":"Sangat Tidak Puas","2":"Tidak Puas","3":"Cukup Puas","4":"Puas","5":"Sangat Puas"}', 'Evaluasi kenyamanan kurikulum dan metode pengajaran dosen.', 2.0, 5],
    [6, 1, 'jam_belajar_harian', 'Rata-rata Waktu Belajar Mandiri per Hari (Jam)', 'angka', NULL, 'Waktu yang dihabiskan untuk belajar mandiri di luar jam kelas.', 1.5, 6],
    
    [7, 2, 'tekanan_keuangan', 'Beban / Tekanan Finansial & Biaya Hidup Kuliah', 'skala_1_5', '{"1":"Tidak Ada Tekanan","2":"Ringan","3":"Sedang","4":"Berat","5":"Sangat Kritis"}', 'Tekanan terkait UKT, biaya hidup harian, dan tempat tinggal.', 2.5, 7],
    [8, 2, 'tekanan_pekerjaan', 'Tekanan Pekerjaan Sampingan / Freelance / Magang', 'skala_0_5', '{"0":"Tidak Bekerja","1":"Sangat Ringan","2":"Ringan","3":"Sedang","4":"Berat","5":"Sangat Melelahkan"}', 'Beban ganda bagi mahasiswa yang bekerja paruh waktu.', 2.0, 8],

    [9, 3, 'durasi_tidur', 'Rata-rata Durasi Tidur Harian', 'pilihan', '["Kurang dari 5 jam", "5-6 jam", "7-8 jam", "Lebih dari 8 jam"]', 'Pola istirahat tidur pada malam hari.', 2.5, 9],
    [10, 3, 'pola_makan', 'Kualitas & Keteraturan Pola Makan', 'pilihan', '["Sehat", "Cukup/Sedang", "Tidak Sehat"]', 'Keteraturan jam makan dan gizi seimbang harian.', 2.0, 10],
    [11, 3, 'riwayat_keluarga', 'Riwayat Gangguan Mental dalam Keluarga', 'pilihan', '["Tidak", "Ya"]', 'Faktor genetik atau predisposisi riwayat psikiatri keluarga.', 2.0, 11],
    [12, 3, 'pikiran_menyakiti_diri', 'Pernahkah Muncul Pikiran untuk Menyakiti Diri / Putus Asa?', 'pilihan', '["Tidak", "Ya"]', 'Indikator krisis psikologis dan kecenderungan impulsif.', 4.0, 12]
];

// Susun file database.sql
$sql = "-- ========================================================\n";
$sql .= "-- Database: db_spk_mental (Arsitektur Kompleks Master-Detail)\n";
$sql .= "-- Sistem Pendukung Keputusan Skrining Kesehatan Mental Mahasiswa\n";
$sql .= "-- ========================================================\n\n";

$sql .= "CREATE DATABASE IF NOT EXISTS `db_spk_mental` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\n";
$sql .= "USE `db_spk_mental`;\n\n";

$sql .= "SET FOREIGN_KEY_CHECKS = 0;\n";
$sql .= "DROP TABLE IF EXISTS `konseling_riwayat`;\n";
$sql .= "DROP TABLE IF EXISTS `skrining_detail`;\n";
$sql .= "DROP TABLE IF EXISTS `skrining_master`;\n";
$sql .= "DROP TABLE IF EXISTS `skrining`;\n"; // legacy table drop
$sql .= "DROP TABLE IF EXISTS `pertanyaan_indikator`;\n";
$sql .= "DROP TABLE IF EXISTS `kategori_indikator`;\n";
$sql .= "DROP TABLE IF EXISTS `mahasiswa`;\n";
$sql .= "DROP TABLE IF EXISTS `dosen`;\n";
$sql .= "DROP TABLE IF EXISTS `program_studi`;\n";
$sql .= "SET FOREIGN_KEY_CHECKS = 1;\n\n";

// 1. Program Studi
$sql .= "-- --------------------------------------------------------\n";
$sql .= "-- 1. Tabel Master: program_studi\n";
$sql .= "-- --------------------------------------------------------\n";
$sql .= "CREATE TABLE `program_studi` (\n";
$sql .= "  `id` INT AUTO_INCREMENT PRIMARY KEY,\n";
$sql .= "  `kode_prodi` VARCHAR(10) NOT NULL UNIQUE,\n";
$sql .= "  `nama_prodi` VARCHAR(100) NOT NULL,\n";
$sql .= "  `jenjang` VARCHAR(10) NOT NULL DEFAULT 'S1',\n";
$sql .= "  `fakultas` VARCHAR(100) NOT NULL,\n";
$sql .= "  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP\n";
$sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n\n";

$sql .= "INSERT INTO `program_studi` (`id`, `kode_prodi`, `nama_prodi`, `jenjang`, `fakultas`) VALUES\n";
$sql .= "(1, 'SI', 'Sistem Informasi', 'S1', 'Fakultas Ilmu Komputer'),\n";
$sql .= "(2, 'TI', 'Teknik Informatika', 'S1', 'Fakultas Ilmu Komputer'),\n";
$sql .= "(3, 'MI', 'Manajemen Informatika', 'D3', 'Fakultas Ilmu Komputer');\n\n";

// 2. Dosen
$sql .= "-- --------------------------------------------------------\n";
$sql .= "-- 2. Tabel Master: dosen\n";
$sql .= "-- --------------------------------------------------------\n";
$sql .= "CREATE TABLE `dosen` (\n";
$sql .= "  `id` INT AUTO_INCREMENT PRIMARY KEY,\n";
$sql .= "  `nidn` VARCHAR(30) NOT NULL UNIQUE,\n";
$sql .= "  `nama_dosen` VARCHAR(100) NOT NULL,\n";
$sql .= "  `username` VARCHAR(50) NOT NULL UNIQUE,\n";
$sql .= "  `password` VARCHAR(255) NOT NULL,\n";
$sql .= "  `password_plain` VARCHAR(100) NOT NULL,\n";
$sql .= "  `email` VARCHAR(100) DEFAULT NULL,\n";
$sql .= "  `prodi_id` INT NOT NULL DEFAULT 1,\n";
$sql .= "  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,\n";
$sql .= "  FOREIGN KEY (`prodi_id`) REFERENCES `program_studi`(`id`) ON DELETE CASCADE\n";
$sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n\n";

$dosen_vals = [];
foreach ($dosen_data as $d) {
    $h = password_hash($d['password'], PASSWORD_DEFAULT);
    $dosen_vals[] = "({$d['id']}, '{$d['nidn']}', '{$d['nama_dosen']}', '{$d['username']}', '{$h}', '{$d['password']}', '{$d['email']}', 1)";
}
$sql .= "INSERT INTO `dosen` (`id`, `nidn`, `nama_dosen`, `username`, `password`, `password_plain`, `email`, `prodi_id`) VALUES\n" . implode(",\n", $dosen_vals) . ";\n\n";

// 3. Mahasiswa
$sql .= "-- --------------------------------------------------------\n";
$sql .= "-- 3. Tabel Master: mahasiswa (100 Mahasiswa, 1 Dosen = 20 Mahasiswa)\n";
$sql .= "-- --------------------------------------------------------\n";
$sql .= "CREATE TABLE `mahasiswa` (\n";
$sql .= "  `id` INT AUTO_INCREMENT PRIMARY KEY,\n";
$sql .= "  `nim` VARCHAR(20) NOT NULL UNIQUE,\n";
$sql .= "  `nama_mahasiswa` VARCHAR(100) NOT NULL,\n";
$sql .= "  `password` VARCHAR(255) NOT NULL,\n";
$sql .= "  `password_plain` VARCHAR(100) NOT NULL,\n";
$sql .= "  `prodi_id` INT NOT NULL DEFAULT 1,\n";
$sql .= "  `angkatan` VARCHAR(10) NOT NULL DEFAULT '2023',\n";
$sql .= "  `jenis_kelamin` ENUM('Laki-laki', 'Perempuan') NOT NULL,\n";
$sql .= "  `usia` INT NOT NULL DEFAULT 20,\n";
$sql .= "  `dosen_id` INT NOT NULL,\n";
$sql .= "  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,\n";
$sql .= "  FOREIGN KEY (`prodi_id`) REFERENCES `program_studi`(`id`) ON DELETE CASCADE,\n";
$sql .= "  FOREIGN KEY (`dosen_id`) REFERENCES `dosen`(`id`) ON DELETE CASCADE\n";
$sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n\n";

$mhs_vals = [];
foreach ($mahasiswa_data as $m) {
    $h = password_hash($m['password'], PASSWORD_DEFAULT);
    $esc_name = addslashes($m['nama_mahasiswa']);
    $mhs_vals[] = "({$m['id']}, '{$m['nim']}', '{$esc_name}', '{$h}', '{$m['password']}', 1, '{$m['angkatan']}', '{$m['jenis_kelamin']}', {$m['usia']}, {$m['dosen_id']})";
}
$sql .= "INSERT INTO `mahasiswa` (`id`, `nim`, `nama_mahasiswa`, `password`, `password_plain`, `prodi_id`, `angkatan`, `jenis_kelamin`, `usia`, `dosen_id`) VALUES\n" . implode(",\n", $mhs_vals) . ";\n\n";

// 4. Kategori Indikator
$sql .= "-- --------------------------------------------------------\n";
$sql .= "-- 4. Tabel Master: kategori_indikator\n";
$sql .= "-- --------------------------------------------------------\n";
$sql .= "CREATE TABLE `kategori_indikator` (\n";
$sql .= "  `id` INT AUTO_INCREMENT PRIMARY KEY,\n";
$sql .= "  `kode_kategori` VARCHAR(20) NOT NULL UNIQUE,\n";
$sql .= "  `nama_kategori` VARCHAR(100) NOT NULL,\n";
$sql .= "  `deskripsi` TEXT DEFAULT NULL,\n";
$sql .= "  `urutan` INT NOT NULL DEFAULT 1\n";
$sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n\n";

$sql .= "INSERT INTO `kategori_indikator` (`id`, `kode_kategori`, `nama_kategori`, `deskripsi`, `urutan`) VALUES\n";
$sql .= "(1, 'AKADEMIK', 'Kondisi Akademik & Pembelajaran', 'Indikator performa studi, durasi belajar, dan tingkat tekanan kurikulum.', 1),\n";
$sql .= "(2, 'FINANSIAL', 'Kondisi Finansial & Pekerjaan', 'Tingkat tekanan keuangan kuliah dan beban kerja sampingan mahasiswa.', 2),\n";
$sql .= "(3, 'PSIKOLOGIS', 'Gaya Hidup & Kesejahteraan Mental', 'Pola tidur harian, kualitas nutrisi makan, riwayat depresi, dan pemikiran negatif.', 3);\n\n";

// 5. Pertanyaan Indikator
$sql .= "-- --------------------------------------------------------\n";
$sql .= "-- 5. Tabel Master: pertanyaan_indikator (12 Parameter Fitur ML)\n";
$sql .= "-- --------------------------------------------------------\n";
$sql .= "CREATE TABLE `pertanyaan_indikator` (\n";
$sql .= "  `id` INT AUTO_INCREMENT PRIMARY KEY,\n";
$sql .= "  `kategori_id` INT NOT NULL,\n";
$sql .= "  `kode_fitur` VARCHAR(50) NOT NULL UNIQUE,\n";
$sql .= "  `teks_pertanyaan` TEXT NOT NULL,\n";
$sql .= "  `tipe_input` ENUM('skala_1_5', 'skala_0_5', 'pilihan', 'angka', 'desimal') NOT NULL,\n";
$sql .= "  `opsi_pilihan` JSON DEFAULT NULL,\n";
$sql .= "  `keterangan_bantuan` TEXT DEFAULT NULL,\n";
$sql .= "  `bobot_relatif` DECIMAL(5,2) NOT NULL DEFAULT 1.00,\n";
$sql .= "  `urutan` INT NOT NULL DEFAULT 1,\n";
$sql .= "  FOREIGN KEY (`kategori_id`) REFERENCES `kategori_indikator`(`id`) ON DELETE CASCADE\n";
$sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n\n";

$pi_vals = [];
foreach ($pertanyaan_data as $p) {
    $opsi_sql = $p[5] ? "'" . addslashes($p[5]) . "'" : "NULL";
    $pi_vals[] = "({$p[0]}, {$p[1]}, '{$p[2]}', '" . addslashes($p[3]) . "', '{$p[4]}', {$opsi_sql}, '" . addslashes($p[6]) . "', {$p[7]}, {$p[8]})";
}
$sql .= "INSERT INTO `pertanyaan_indikator` (`id`, `kategori_id`, `kode_fitur`, `teks_pertanyaan`, `tipe_input`, `opsi_pilihan`, `keterangan_bantuan`, `bobot_relatif`, `urutan`) VALUES\n" . implode(",\n", $pi_vals) . ";\n\n";

// 6. Skrining Master (Header)
$sql .= "-- --------------------------------------------------------\n";
$sql .= "-- 6. Tabel Master Transaksi: skrining_master\n";
$sql .= "-- --------------------------------------------------------\n";
$sql .= "CREATE TABLE `skrining_master` (\n";
$sql .= "  `id` INT AUTO_INCREMENT PRIMARY KEY,\n";
$sql .= "  `kode_sesi` VARCHAR(30) NOT NULL UNIQUE,\n";
$sql .= "  `mahasiswa_id` INT NOT NULL,\n";
$sql .= "  `dosen_id` INT NOT NULL,\n";
$sql .= "  `tahun_akademik` VARCHAR(20) NOT NULL DEFAULT '2025/2026 Ganjil',\n";
$sql .= "  `ipk_snapshot` DECIMAL(4,2) NOT NULL,\n";
$sql .= "  `usia_snapshot` INT NOT NULL,\n";
$sql .= "  `jenis_kelamin_snapshot` VARCHAR(20) NOT NULL,\n";
$sql .= "  `hasil_prediksi` TINYINT(1) NOT NULL COMMENT '0: Rendah, 1: Tinggi',\n";
$sql .= "  `kategori` VARCHAR(50) NOT NULL,\n";
$sql .= "  `probabilitas_rendah` DECIMAL(5,2) NOT NULL,\n";
$sql .= "  `probabilitas_tinggi` DECIMAL(5,2) NOT NULL,\n";
$sql .= "  `catatan_refleksi` TEXT NOT NULL,\n";
$sql .= "  `faktor_utama` TEXT NOT NULL,\n";
$sql .= "  `status_konseling` ENUM('Belum Ditindaklanjuti', 'Dijadwalkan Konseling', 'Selesai Konseling') NOT NULL DEFAULT 'Belum Ditindaklanjuti',\n";
$sql .= "  `tanggal_skrining` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,\n";
$sql .= "  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,\n";
$sql .= "  FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa`(`id`) ON DELETE CASCADE,\n";
$sql .= "  FOREIGN KEY (`dosen_id`) REFERENCES `dosen`(`id`) ON DELETE CASCADE\n";
$sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n\n";

// 7. Skrining Detail (Child/Detail Table)
$sql .= "-- --------------------------------------------------------\n";
$sql .= "-- 7. Tabel Detail Transaksi: skrining_detail (Implementasi Master-Detail)\n";
$sql .= "-- --------------------------------------------------------\n";
$sql .= "CREATE TABLE `skrining_detail` (\n";
$sql .= "  `id` INT AUTO_INCREMENT PRIMARY KEY,\n";
$sql .= "  `skrining_id` INT NOT NULL,\n";
$sql .= "  `pertanyaan_id` INT NOT NULL,\n";
$sql .= "  `kode_fitur` VARCHAR(50) NOT NULL,\n";
$sql .= "  `nilai_raw` VARCHAR(100) NOT NULL,\n";
$sql .= "  `nilai_numerik` DECIMAL(6,2) NOT NULL DEFAULT 0.00,\n";
$sql .= "  `interpretasi_singkat` VARCHAR(100) DEFAULT NULL,\n";
$sql .= "  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,\n";
$sql .= "  FOREIGN KEY (`skrining_id`) REFERENCES `skrining_master`(`id`) ON DELETE CASCADE,\n";
$sql .= "  FOREIGN KEY (`pertanyaan_id`) REFERENCES `pertanyaan_indikator`(`id`) ON DELETE CASCADE\n";
$sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n\n";

// 8. Konseling Riwayat (Detail Catatan Bimbingan)
$sql .= "-- --------------------------------------------------------\n";
$sql .= "-- 8. Tabel Detail Transaksi: konseling_riwayat (Riwayat Konsultasi Dosen)\n";
$sql .= "-- --------------------------------------------------------\n";
$sql .= "CREATE TABLE `konseling_riwayat` (\n";
$sql .= "  `id` INT AUTO_INCREMENT PRIMARY KEY,\n";
$sql .= "  `skrining_id` INT NOT NULL,\n";
$sql .= "  `dosen_id` INT NOT NULL,\n";
$sql .= "  `mahasiswa_id` INT NOT NULL,\n";
$sql .= "  `tanggal_konseling` VARCHAR(50) DEFAULT NULL,\n";
$sql .= "  `tempat_metode` VARCHAR(100) DEFAULT 'Ruang Dosen PA / Tatap Muka',\n";
$sql .= "  `catatan_bimbingan` TEXT NOT NULL,\n";
$sql .= "  `solusi_tindak_lanjut` TEXT DEFAULT NULL,\n";
$sql .= "  `status_pertemuan` ENUM('Dijadwalkan', 'Selesai', 'Dibatalkan') NOT NULL DEFAULT 'Dijadwalkan',\n";
$sql .= "  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,\n";
$sql .= "  FOREIGN KEY (`skrining_id`) REFERENCES `skrining_master`(`id`) ON DELETE CASCADE,\n";
$sql .= "  FOREIGN KEY (`dosen_id`) REFERENCES `dosen`(`id`) ON DELETE CASCADE,\n";
$sql .= "  FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa`(`id`) ON DELETE CASCADE\n";
$sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n\n";

// Sample initial data for skrining_master, skrining_detail, and konseling_riwayat
$sample_data_master = [
    [
        'id' => 1, 'kode' => 'SKR-2026-0001', 'm_id' => 1, 'd_id' => 1, 'ipk' => 3.75, 'usia' => 20, 'jk' => 'Laki-laki',
        'pred' => 0, 'kat' => 'Tingkat Stress/Risiko Rendah', 'pr' => 93.50, 'pt' => 6.50,
        'pesan' => 'Kondisi psikologis dan pola hidupmu relatif stabil dan sehat. Pertahankan manajemen waktu, pola istirahat, dan lingkungan belajar yang positif!',
        'faktor' => 'Tekanan Akademik (24.71%), Tekanan Keuangan (12.01%), Jam Belajar Harian (8.45%)',
        'status' => 'Belum Ditindaklanjuti',
        'jawaban' => [
            'jenis_kelamin' => ['Laki-laki', 0, 'Normal'],
            'usia' => ['20', 20, 'Normal'],
            'ipk' => ['3.75', 3.75, 'Sangat Baik'],
            'tekanan_akademik' => ['2', 2, 'Ringan'],
            'kepuasan_belajar' => ['4', 4, 'Puas'],
            'jam_belajar_harian' => ['4', 4, 'Ideal'],
            'tekanan_keuangan' => ['2', 2, 'Ringan'],
            'tekanan_pekerjaan' => ['0', 0, 'Tidak Bekerja'],
            'durasi_tidur' => ['7-8 jam', 8, 'Cukup & Ideal'],
            'pola_makan' => ['Sehat', 3, 'Gizi Seimbang'],
            'riwayat_keluarga' => ['Tidak', 0, 'Tidak Ada'],
            'pikiran_menyakiti_diri' => ['Tidak', 0, 'Aman']
        ]
    ],
    [
        'id' => 2, 'kode' => 'SKR-2026-0002', 'm_id' => 2, 'd_id' => 1, 'ipk' => 3.05, 'usia' => 21, 'jk' => 'Perempuan',
        'pred' => 1, 'kat' => 'Tingkat Stress/Risiko Tinggi', 'pr' => 17.80, 'pt' => 82.20,
        'pesan' => 'Hasil indikasi menunjukkan kamu sedang mengalami beban pikiran atau tekanan psikologis yang cukup berat. Disarankan untuk beristirahat sejenak dan berkonsultasi dengan Dosen PA atau konselor kampus.',
        'faktor' => 'Pikiran Menyakiti Diri (35.61%), Tekanan Akademik (24.71%), Tekanan Keuangan (12.01%)',
        'status' => 'Dijadwalkan Konseling',
        'jawaban' => [
            'jenis_kelamin' => ['Perempuan', 1, 'Normal'],
            'usia' => ['21', 21, 'Normal'],
            'ipk' => ['3.05', 3.05, 'Cukup'],
            'tekanan_akademik' => ['5', 5, 'Sangat Berat'],
            'kepuasan_belajar' => ['2', 2, 'Kurang Puas'],
            'jam_belajar_harian' => ['7', 7, 'Intensitas Tinggi'],
            'tekanan_keuangan' => ['4', 4, 'Beban Berat'],
            'tekanan_pekerjaan' => ['2', 2, 'Ringan'],
            'durasi_tidur' => ['Kurang dari 5 jam', 4, 'Kurang / Begadang'],
            'pola_makan' => ['Tidak Sehat', 1, 'Sering Terlewat'],
            'riwayat_keluarga' => ['Ya', 1, 'Ada Riwayat'],
            'pikiran_menyakiti_diri' => ['Ya', 1, 'Sangat Butuh Perhatian']
        ],
        'konseling' => [
            'tgl' => 'Senin, 17 November 2026 pukul 10.00 WIB',
            'tempat' => 'Ruang Dosen PA Gedung B Lantai 2',
            'catatan' => 'Diharapkan hadir konseling tatap muka untuk evaluasi beban SKS semester ini dan manajemen tekanan belajar.',
            'solusi' => 'Disarankan mengambil beban maksimal 18 SKS di semester depan serta mengikuti workshop mindfulness konseling kampus.'
        ]
    ],
    [
        'id' => 3, 'kode' => 'SKR-2026-0003', 'm_id' => 21, 'd_id' => 2, 'ipk' => 3.20, 'usia' => 20, 'jk' => 'Laki-laki',
        'pred' => 1, 'kat' => 'Tingkat Stress/Risiko Tinggi', 'pr' => 32.10, 'pt' => 67.90,
        'pesan' => 'Hasil indikasi menunjukkan kamu sedang mengalami beban pikiran atau tekanan psikologis yang cukup berat. Disarankan untuk beristirahat sejenak dan berkonsultasi dengan Dosen PA atau konselor kampus.',
        'faktor' => 'Tekanan Akademik (24.71%), Tekanan Keuangan (12.01%), Durasi Tidur (10.15%)',
        'status' => 'Belum Ditindaklanjuti',
        'jawaban' => [
            'jenis_kelamin' => ['Laki-laki', 0, 'Normal'],
            'usia' => ['20', 20, 'Normal'],
            'ipk' => ['3.20', 3.20, 'Baik'],
            'tekanan_akademik' => ['4', 4, 'Berat'],
            'kepuasan_belajar' => ['2', 2, 'Kurang Puas'],
            'jam_belajar_harian' => ['5', 5, 'Wajar'],
            'tekanan_keuangan' => ['3', 3, 'Sedang'],
            'tekanan_pekerjaan' => ['1', 1, 'Ringan'],
            'durasi_tidur' => ['5-6 jam', 6, 'Kurang'],
            'pola_makan' => ['Cukup/Sedang', 2, 'Cukup'],
            'riwayat_keluarga' => ['Tidak', 0, 'Tidak Ada'],
            'pikiran_menyakiti_diri' => ['Tidak', 0, 'Aman']
        ]
    ]
];

// Insert Master
$sql .= "INSERT INTO `skrining_master` (`id`, `kode_sesi`, `mahasiswa_id`, `dosen_id`, `tahun_akademik`, `ipk_snapshot`, `usia_snapshot`, `jenis_kelamin_snapshot`, `hasil_prediksi`, `kategori`, `probabilitas_rendah`, `probabilitas_tinggi`, `catatan_refleksi`, `faktor_utama`, `status_konseling`) VALUES\n";
$sm_rows = [];
foreach ($sample_data_master as $sm) {
    $sm_pesan = addslashes($sm['pesan']);
    $sm_faktor = addslashes($sm['faktor']);
    $sm_rows[] = "({$sm['id']}, '{$sm['kode']}', {$sm['m_id']}, {$sm['d_id']}, '2025/2026 Ganjil', {$sm['ipk']}, {$sm['usia']}, '{$sm['jk']}', {$sm['pred']}, '{$sm['kat']}', {$sm['pr']}, {$sm['pt']}, '{$sm_pesan}', '{$sm_faktor}', '{$sm['status']}')";
}
$sql .= implode(",\n", $sm_rows) . ";\n\n";

// Insert Detail
$sql .= "INSERT INTO `skrining_detail` (`skrining_id`, `pertanyaan_id`, `kode_fitur`, `nilai_raw`, `nilai_numerik`, `interpretasi_singkat`) VALUES\n";
$sd_rows = [];
$fitur_to_id = [
    'jenis_kelamin' => 1, 'usia' => 2, 'ipk' => 3, 'tekanan_akademik' => 4,
    'kepuasan_belajar' => 5, 'jam_belajar_harian' => 6, 'tekanan_keuangan' => 7,
    'tekanan_pekerjaan' => 8, 'durasi_tidur' => 9, 'pola_makan' => 10,
    'riwayat_keluarga' => 11, 'pikiran_menyakiti_diri' => 12
];

foreach ($sample_data_master as $sm) {
    foreach ($sm['jawaban'] as $kf => $detail) {
        $pid = $fitur_to_id[$kf];
        $val_raw = addslashes($detail[0]);
        $val_num = $detail[1];
        $interp = addslashes($detail[2]);
        $sd_rows[] = "({$sm['id']}, {$pid}, '{$kf}', '{$val_raw}', {$val_num}, '{$interp}')";
    }
}
$sql .= implode(",\n", $sd_rows) . ";\n\n";

// Insert Konseling Riwayat
$sql .= "INSERT INTO `konseling_riwayat` (`skrining_id`, `dosen_id`, `mahasiswa_id`, `tanggal_konseling`, `tempat_metode`, `catatan_bimbingan`, `solusi_tindak_lanjut`, `status_pertemuan`) VALUES\n";
$kr_rows = [];
foreach ($sample_data_master as $sm) {
    if (isset($sm['konseling'])) {
        $c = $sm['konseling'];
        $kr_rows[] = "({$sm['id']}, {$sm['d_id']}, {$sm['m_id']}, '" . addslashes($c['tgl']) . "', '" . addslashes($c['tempat']) . "', '" . addslashes($c['catatan']) . "', '" . addslashes($c['solusi']) . "', 'Dijadwalkan')";
    }
}
$sql .= implode(",\n", $kr_rows) . ";\n";

file_put_contents(__DIR__ . '/database.sql', $sql);
echo "Berhasil menghasilkan database.sql versi Master-Detail!\n";
?>
