<?php
require_once 'koneksi.php';

if (!$pdo) {
    die("Koneksi PDO gagal!\n");
}

$stmtDosen = $pdo->query("SELECT * FROM `dosen` ORDER BY `id` ASC");
$dosen_list = $stmtDosen->fetchAll();

$stmtMhs = $pdo->query("SELECT m.*, d.nama_dosen, d.nidn 
                        FROM `mahasiswa` m 
                        JOIN `dosen` d ON m.dosen_id = d.id 
                        ORDER BY m.`id` ASC");
$mhs_list = $stmtMhs->fetchAll();

$md = "# DAFTAR AKUN PENGGUNA (DOSEN & MAHASISWA)\n\n";
$md .= "Sistem Informasi Skrining Kesehatan Mental Mahasiswa (SPK AI)\n";
$md .= "**Total Pengguna:** 5 Akun Dosen Pembimbing Akademik & 100 Akun Mahasiswa\n";
$md .= "**Ketentuan Relasi:** 1 Dosen Pembimbing Akademik mengampu tepat **20 Mahasiswa Perwalian**.\n\n";
$md .= "> **Catatan Keamanan:** Seluruh nama dosen dan mahasiswa berbeda, serta setiap akun memiliki password unik yang berbeda satu sama lain.\n\n";
$md .= "---\n\n";

$md .= "## 👨‍🏫 A. DAFTAR AKUN DOSEN PEMBIMBING AKADEMIK (5 DOSEN)\n\n";
$md .= "| No | NIDN | Nama Dosen | Username Login | Password | Mengampu Mahasiswa |\n";
$md .= "|:---:|:---:|:---|:---|:---|:---|\n";
foreach ($dosen_list as $i => $d) {
    $start_m = ($i * 20) + 1;
    $end_m = ($i + 1) * 20;
    $start_nim = sprintf("230101%03d", $start_m);
    $end_nim = sprintf("230101%03d", $end_m);
    $md .= "| " . ($i + 1) . " | `{$d['nidn']}` | **{$d['nama_dosen']}** | `{$d['username']}` | `{$d['password_plain']}` | No. {$start_m}–{$end_m} (`{$start_nim}` s/d `{$end_nim}`) |\n";
}
$md .= "\n---\n\n";

$md .= "## 🎓 B. DAFTAR AKUN MAHASISWA (100 MAHASISWA)\n\n";
$md .= "Mahasiswa login menggunakan **NIM** dan **Password** masing-masing.\n\n";

// Kelompokkan per dosen
$mhs_by_dosen = [];
foreach ($mhs_list as $m) {
    $mhs_by_dosen[$m['dosen_id']][] = $m;
}

foreach ($dosen_list as $idx => $d) {
    $d_id = $d['id'];
    $m_group = $mhs_by_dosen[$d_id] ?? [];
    
    $md .= "### Kelompok Bimbingan Dosen " . ($idx + 1) . ": {$d['nama_dosen']} (NIDN: `{$d['nidn']}`)\n";
    $md .= "| No | NIM (Username) | Nama Mahasiswa | Jenis Kelamin | Password | Dosen Pembimbing |\n";
    $md .= "|:---:|:---:|:---|:---:|:---|:---|\n";

    foreach ($m_group as $m) {
        $md .= "| {$m['id']} | `{$m['nim']}` | {$m['nama_mahasiswa']} | {$m['jenis_kelamin']} | `{$m['password_plain']}` | {$d['nama_dosen']} |\n";
    }
    $md .= "\n";
}

file_put_contents(__DIR__ . '/DAFTAR_AKUN.md', $md);
echo "Berhasil sinkronisasi DAFTAR_AKUN.md dari Database!\n";
?>
