<?php
require_once 'koneksi.php';

// Wajib Login Mahasiswa
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa') {
    header('Location: login.php?tab=mahasiswa');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

try {
    $mahasiswa_id = $_SESSION['user_id'];
    $dosen_id = $_SESSION['dosen_id'];
    $nim = $_SESSION['nim'];
    $nama_mahasiswa = $_SESSION['nama_lengkap'];
    $prodi = $_SESSION['prodi'];
    $angkatan = $_SESSION['angkatan'];
    $jenis_kelamin = $_SESSION['jenis_kelamin'];
    $usia = intval($_POST['usia'] ?? $_SESSION['usia']);

    // 2. Ambil Data Indikator Skrining
    $ipk_asli = floatval($_POST['ipk'] ?? 3.5);
    $ipk_ml = ($ipk_asli <= 4.00) ? round($ipk_asli * 2.5, 2) : $ipk_asli;

    $jam_belajar_harian = intval($_POST['jam_belajar_harian'] ?? 4);
    $tekanan_akademik = intval($_POST['tekanan_akademik'] ?? 3);
    $tekanan_pekerjaan = intval($_POST['tekanan_pekerjaan'] ?? 0);
    $kepuasan_belajar = intval($_POST['kepuasan_belajar'] ?? 3);
    $tekanan_keuangan = intval($_POST['tekanan_keuangan'] ?? 3);
    $durasi_tidur = trim($_POST['durasi_tidur'] ?? '7-8 jam');
    $pola_makan = trim($_POST['pola_makan'] ?? 'Sehat');
    $riwayat_keluarga = trim($_POST['riwayat_keluarga'] ?? 'Tidak');
    $pikiran_menyakiti_diri = trim($_POST['pikiran_menyakiti_diri'] ?? 'Tidak');

    // 3. Susun Payload untuk Model Machine Learning
    $ml_input = [
        'jenis_kelamin' => $jenis_kelamin,
        'usia' => $usia,
        'tekanan_akademik' => $tekanan_akademik,
        'tekanan_pekerjaan' => $tekanan_pekerjaan,
        'ipk' => $ipk_ml,
        'kepuasan_belajar' => $kepuasan_belajar,
        'durasi_tidur' => $durasi_tidur,
        'pola_makan' => $pola_makan,
        'pikiran_menyakiti_diri' => $pikiran_menyakiti_diri,
        'jam_belajar_harian' => $jam_belajar_harian,
        'tekanan_keuangan' => $tekanan_keuangan,
        'riwayat_keluarga' => $riwayat_keluarga
    ];

    // 4. Panggil Engine ML
    $ml_response = panggil_model_ml($ml_input);

    if (!isset($ml_response['status']) || $ml_response['status'] !== 'success') {
        throw new Exception($ml_response['message'] ?? 'Gagal memperoleh prediksi dari model ML.');
    }

    $hasil_prediksi = intval($ml_response['hasil_prediksi']);
    $kategori = $ml_response['kategori'];
    $catatan_refleksi = $ml_response['catatan_refleksi'];
    $prob_rendah = floatval($ml_response['chart_data']['percentages'][0] ?? 0);
    $prob_tinggi = floatval($ml_response['chart_data']['percentages'][1] ?? 0);
    $faktor_utama = implode(', ', $ml_response['faktor_utama'] ?? []);

    // 5. MASTER INSERT: Simpan ke `skrining_master`
    $skrining_master_id = null;
    if ($pdo) {
        $kode_sesi = 'SKR-' . date('Y') . '-' . strtoupper(substr(uniqid(), -6));
        $tahun_akademik = '2025/2026 Ganjil';

        $stmtMaster = $pdo->prepare("INSERT INTO `skrining_master` (
            `kode_sesi`, `mahasiswa_id`, `dosen_id`, `tahun_akademik`,
            `ipk_snapshot`, `usia_snapshot`, `jenis_kelamin_snapshot`,
            `hasil_prediksi`, `kategori`, `probabilitas_rendah`, `probabilitas_tinggi`,
            `catatan_refleksi`, `faktor_utama`, `status_konseling`, `tanggal_skrining`
        ) VALUES (
            ?, ?, ?, ?,
            ?, ?, ?,
            ?, ?, ?, ?,
            ?, ?, 'Belum Ditindaklanjuti', NOW()
        )");

        $stmtMaster->execute([
            $kode_sesi, $mahasiswa_id, $dosen_id, $tahun_akademik,
            $ipk_asli, $usia, $jenis_kelamin,
            $hasil_prediksi, $kategori, $prob_rendah, $prob_tinggi,
            $catatan_refleksi, $faktor_utama
        ]);

        $skrining_master_id = $pdo->lastInsertId();

        // 6. DETAIL INSERT: Simpan ke `skrining_detail` untuk 12 parameter
        // Ambil ID pertanyaan dari `pertanyaan_indikator` (key: kode_fitur, val: id)
        $stmtPertanyaan = $pdo->query("SELECT kode_fitur, id FROM `pertanyaan_indikator`");
        $pertanyaan_map = $stmtPertanyaan->fetchAll(PDO::FETCH_KEY_PAIR); // ['jenis_kelamin' => 1, ...]

        // Rincian 12 jawaban beserta interpretasi singkat
        $rincian_jawaban = [
            'jenis_kelamin' => [$jenis_kelamin, ($jenis_kelamin === 'Laki-laki' ? 0 : 1), 'Demografi'],
            'usia' => [(string)$usia, $usia, 'Demografi'],
            'ipk' => [number_format($ipk_asli, 2), $ipk_asli, ($ipk_asli >= 3.5 ? 'Sangat Baik' : ($ipk_asli >= 3.0 ? 'Baik' : 'Perlu Peningkatan'))],
            'tekanan_akademik' => [(string)$tekanan_akademik, $tekanan_akademik, ($tekanan_akademik >= 4 ? 'Sangat Berat' : ($tekanan_akademik == 3 ? 'Sedang' : 'Ringan'))],
            'kepuasan_belajar' => [(string)$kepuasan_belajar, $kepuasan_belajar, ($kepuasan_belajar <= 2 ? 'Kurang Puas' : 'Puas')],
            'jam_belajar_harian' => [(string)$jam_belajar_harian, $jam_belajar_harian, ($jam_belajar_harian >= 7 ? 'Intensitas Tinggi' : 'Wajar/Ideal')],
            'tekanan_keuangan' => [(string)$tekanan_keuangan, $tekanan_keuangan, ($tekanan_keuangan >= 4 ? 'Beban Berat' : ($tekanan_keuangan == 3 ? 'Sedang' : 'Terkendali'))],
            'tekanan_pekerjaan' => [(string)$tekanan_pekerjaan, $tekanan_pekerjaan, ($tekanan_pekerjaan == 0 ? 'Tidak Bekerja' : 'Level ' . $tekanan_pekerjaan)],
            'durasi_tidur' => [$durasi_tidur, ($durasi_tidur === 'Kurang dari 5 jam' ? 4 : 8), ($durasi_tidur === 'Kurang dari 5 jam' ? 'Kurang / Begadang' : 'Cukup')],
            'pola_makan' => [$pola_makan, ($pola_makan === 'Sehat' ? 3 : 1), ($pola_makan === 'Tidak Sehat' ? 'Buruk / Tidak Teratur' : 'Baik')],
            'riwayat_keluarga' => [$riwayat_keluarga, ($riwayat_keluarga === 'Ya' ? 1 : 0), ($riwayat_keluarga === 'Ya' ? 'Ada Riwayat' : 'Tidak Ada')],
            'pikiran_menyakiti_diri' => [$pikiran_menyakiti_diri, ($pikiran_menyakiti_diri === 'Ya' ? 1 : 0), ($pikiran_menyakiti_diri === 'Ya' ? 'Sangat Butuh Perhatian' : 'Aman')]
        ];

        $stmtDetail = $pdo->prepare("INSERT INTO `skrining_detail` (
            `skrining_id`, `pertanyaan_id`, `kode_fitur`, `nilai_raw`, `nilai_numerik`, `interpretasi_singkat`
        ) VALUES (?, ?, ?, ?, ?, ?)");

        foreach ($rincian_jawaban as $kode_f => $val) {
            $pid = $pertanyaan_map[$kode_f] ?? null;
            if ($pid) {
                $stmtDetail->execute([
                    $skrining_master_id,
                    $pid,
                    $kode_f,
                    $val[0],
                    $val[1],
                    $val[2]
                ]);
            }
        }
    }

    if ($skrining_master_id) {
        header("Location: hasil.php?id={$skrining_master_id}");
    } else {
        header("Location: dashboard_mahasiswa.php");
    }
    exit;

} catch (Exception $e) {
    header('Location: index.php?pesan=error&error_msg=' . urlencode($e->getMessage()));
    exit;
}
?>
