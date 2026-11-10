<?php
require_once 'auth_check.php';

$dosen_id = $_SESSION['user_id'];
$dosen_nama = $_SESSION['nama_lengkap'];
$id = intval($_GET['id'] ?? 0);
$mhs = null;
$details = [];
$riwayat_konseling = [];

if ($id > 0 && $pdo) {
    // 1. Ambil data MASTER
    $stmt = $pdo->prepare("SELECT sm.*, m.nim, m.nama_mahasiswa, m.jenis_kelamin, m.usia, m.angkatan, p.nama_prodi 
                           FROM `skrining_master` sm 
                           JOIN `mahasiswa` m ON sm.mahasiswa_id = m.id 
                           JOIN `program_studi` p ON m.prodi_id = p.id
                           WHERE sm.`id` = ? AND sm.`dosen_id` = ?");
    $stmt->execute([$id, $dosen_id]);
    $mhs = $stmt->fetch();

    if ($mhs) {
        // 2. Ambil data DETAIL (12 Jawaban)
        $stmtDetail = $pdo->prepare("SELECT sd.*, pi.teks_pertanyaan, ki.nama_kategori 
                                     FROM `skrining_detail` sd 
                                     JOIN `pertanyaan_indikator` pi ON sd.pertanyaan_id = pi.id 
                                     JOIN `kategori_indikator` ki ON pi.kategori_id = ki.id 
                                     WHERE sd.`skrining_id` = ? 
                                     ORDER BY pi.urutan ASC");
        $stmtDetail->execute([$id]);
        $details = $stmtDetail->fetchAll();

        // 3. Ambil log riwayat konseling dari tabel `konseling_riwayat`
        $stmtKonsel = $pdo->prepare("SELECT * FROM `konseling_riwayat` WHERE `skrining_id` = ? ORDER BY `created_at` DESC");
        $stmtKonsel->execute([$id]);
        $riwayat_konseling = $stmtKonsel->fetchAll();
    }
}

if (!$mhs) {
    header('Location: data_mahasiswa.php');
    exit;
}

// Handle Form Update Status & Catatan Konseling oleh Dosen
$pesan_update = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_konseling']) && $pdo) {
    $status_konseling = trim($_POST['status_konseling'] ?? 'Belum Ditindaklanjuti');
    $tanggal_konseling = trim($_POST['tanggal_konseling'] ?? '');
    $tempat_metode = trim($_POST['tempat_metode'] ?? 'Ruang Dosen PA / Tatap Muka');
    $catatan_bimbingan = trim($_POST['catatan_bimbingan'] ?? '');
    $solusi_tindak_lanjut = trim($_POST['solusi_tindak_lanjut'] ?? '');

    // Update status di Master
    $stmtUpdateMaster = $pdo->prepare("UPDATE `skrining_master` SET `status_konseling` = ? WHERE `id` = ? AND `dosen_id` = ?");
    $stmtUpdateMaster->execute([$status_konseling, $id, $dosen_id]);

    // Insert log baru ke `konseling_riwayat`
    if (!empty($catatan_bimbingan)) {
        $status_pertemuan = ($status_konseling === 'Selesai Konseling') ? 'Selesai' : 'Dijadwalkan';
        $stmtInsertLog = $pdo->prepare("INSERT INTO `konseling_riwayat` (
            `skrining_id`, `dosen_id`, `mahasiswa_id`, `tanggal_konseling`, `tempat_metode`, `catatan_bimbingan`, `solusi_tindak_lanjut`, `status_pertemuan`
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmtInsertLog->execute([
            $id, $dosen_id, $mhs['mahasiswa_id'], $tanggal_konseling, $tempat_metode, $catatan_bimbingan, $solusi_tindak_lanjut, $status_pertemuan
        ]);
    }

    $mhs['status_konseling'] = $status_konseling;
    
    // Refresh log konseling
    $stmtKonsel = $pdo->prepare("SELECT * FROM `konseling_riwayat` WHERE `skrining_id` = ? ORDER BY `created_at` DESC");
    $stmtKonsel->execute([$id]);
    $riwayat_konseling = $stmtKonsel->fetchAll();

    $pesan_update = 'Catatan bimbingan dan jadwal konseling berhasil disimpan! Notifikasi otomatis terkirim ke dashboard mahasiswa.';
}

$is_high_risk = ($mhs['hasil_prediksi'] == 1);
$factors = array_map('trim', explode(',', $mhs['faktor_utama']));
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Evaluasi Sesi Skrining - MindCare SPK</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<?php include 'navbar_dosen.php'; ?>

<main class="container my-4">
  
  <div class="d-flex justify-content-between align-items-center mb-3">
    <a href="data_mahasiswa.php" class="btn btn-glass btn-sm rounded-pill px-3">
      <i class="bi bi-arrow-left me-1"></i> Kembali ke 20 Mahasiswa Perwalian
    </a>
    <span class="text-muted small">Kode Sesi: <code><?= htmlspecialchars($mhs['kode_sesi']) ?></code> &bull; <?= date('d M Y, H:i', strtotime($mhs['tanggal_skrining'])) ?> WIB</span>
  </div>

  <?php if ($pesan_update): ?>
    <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($pesan_update) ?>
    </div>
  <?php endif; ?>

  <div class="row g-4">
    
    <!-- Kolom Kiri: Profil & Form Tindak Lanjut Konseling -->
    <div class="col-lg-4">
      
      <!-- Card Status Prediksi Glass -->
      <div class="card glass-card border-0 mb-4 overflow-hidden text-center">
        <div class="card-body p-4 <?= $is_high_risk ? 'badge-glass-rose' : 'badge-glass-emerald' ?>" style="border-radius: 20px;">
          <span class="badge <?= $is_high_risk ? 'bg-danger' : 'bg-success' ?> px-3 py-2 rounded-pill fs-6 mb-2">
            <?= htmlspecialchars($mhs['kategori']) ?>
          </span>
          <h4 class="fw-bold mb-1 text-slate-900">
            <?= $is_high_risk ? 'Tingkat Stres Tinggi' : 'Kondisi Relatif Stabil' ?>
          </h4>
          <p class="small text-muted mb-0">Probabilitas Stres: <strong><?= $mhs['probabilitas_tinggi'] ?>%</strong></p>
        </div>
      </div>

      <!-- Card Profil Mahasiswa -->
      <div class="card glass-card border-0 mb-4">
        <div class="card-header py-3">
          <h6 class="mb-0 fw-bold"><i class="bi bi-person-circle text-primary me-2"></i>Profil Mahasiswa</h6>
        </div>
        <div class="card-body p-3">
          <ul class="list-group list-group-flush" style="--bs-list-group-bg: transparent;">
            <li class="list-group-item d-flex justify-content-between px-0">
              <span class="text-muted">Nama</span>
              <strong class="text-slate-800"><?= htmlspecialchars($mhs['nama_mahasiswa']) ?></strong>
            </li>
            <li class="list-group-item d-flex justify-content-between px-0">
              <span class="text-muted">NIM</span>
              <span class="font-monospace fw-semibold"><?= htmlspecialchars($mhs['nim']) ?></span>
            </li>
            <li class="list-group-item d-flex justify-content-between px-0">
              <span class="text-muted">Program Studi</span>
              <span><?= htmlspecialchars($mhs['nama_prodi']) ?></span>
            </li>
            <li class="list-group-item d-flex justify-content-between px-0">
              <span class="text-muted">Angkatan</span>
              <span><?= htmlspecialchars($mhs['angkatan']) ?></span>
            </li>
            <li class="list-group-item d-flex justify-content-between px-0">
              <span class="text-muted">Gender / Usia</span>
              <span><?= htmlspecialchars($mhs['jenis_kelamin']) ?> / <?= $mhs['usia'] ?> th</span>
            </li>
            <li class="list-group-item d-flex justify-content-between px-0">
              <span class="text-muted">IPK Snapshot</span>
              <span class="badge badge-glass-indigo fs-6"><?= number_format($mhs['ipk_snapshot'], 2) ?></span>
            </li>
          </ul>
        </div>
      </div>

      <!-- Card Tindak Lanjut Konseling oleh Dosen PA (Master-Detail Riwayat) -->
      <div class="card glass-card border-0 mb-4">
        <div class="card-header py-3">
          <h6 class="mb-0 fw-bold"><i class="bi bi-calendar2-plus text-primary me-2"></i>Jadwal Konseling & Bimbingan PA</h6>
        </div>
        <div class="card-body p-4">
          <form action="detail_mahasiswa.php?id=<?= $mhs['id'] ?>" method="POST">
            <div class="mb-3">
              <label for="status_konseling" class="form-label">Status Tindak Lanjut Sesi</label>
              <select name="status_konseling" id="status_konseling" class="form-select" required>
                <option value="Belum Ditindaklanjuti" <?= $mhs['status_konseling'] === 'Belum Ditindaklanjuti' ? 'selected' : '' ?>>Belum Ditindaklanjuti</option>
                <option value="Dijadwalkan Konseling" <?= $mhs['status_konseling'] === 'Dijadwalkan Konseling' ? 'selected' : '' ?>>🔔 Dijadwalkan Konseling (Kirim Notif)</option>
                <option value="Selesai Konseling" <?= $mhs['status_konseling'] === 'Selesai Konseling' ? 'selected' : '' ?>>✅ Selesai Konseling</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="tanggal_konseling" class="form-label">Waktu / Jadwal Pertemuan</label>
              <input type="text" class="form-control" name="tanggal_konseling" id="tanggal_konseling" placeholder="Contoh: Senin, 17 November 2026 jam 10.00 WIB">
            </div>
            <div class="mb-3">
              <label for="tempat_metode" class="form-label">Tempat / Metode Bimbingan</label>
              <input type="text" class="form-control" name="tempat_metode" id="tempat_metode" value="Ruang Dosen PA Gedung B / Tatap Muka">
            </div>
            <div class="mb-3">
              <label for="catatan_bimbingan" class="form-label">Catatan Bimbingan / Pesan untuk Mahasiswa <span class="text-danger">*</span></label>
              <textarea name="catatan_bimbingan" id="catatan_bimbingan" rows="3" class="form-control" placeholder="Tuliskan pesan instruksi/arahan konseling yang akan muncul di notifikasi mahasiswa..." required></textarea>
            </div>
            <div class="mb-3">
              <label for="solusi_tindak_lanjut" class="form-label">Rekomendasi / Solusi Akademik</label>
              <textarea name="solusi_tindak_lanjut" id="solusi_tindak_lanjut" rows="2" class="form-control" placeholder="Contoh: Penyesuaian batas maksimal 18 SKS, rujukan konseling psikologi kampus..."></textarea>
            </div>
            <button type="submit" name="simpan_konseling" class="btn btn-primary w-100 rounded-pill shadow-sm">
              <i class="bi bi-send me-1"></i> Simpan Catatan & Kirim Notif
            </button>
          </form>
        </div>
      </div>

    </div>

    <!-- Kolom Kanan: Rincian 12 Jawaban Kuesioner (Detail Table) & Riwayat Log -->
    <div class="col-lg-8">
      
      <!-- Card Analisis AI & Pemicu -->
      <div class="card glass-card border-0 mb-4">
        <div class="card-header py-3">
          <h6 class="mb-0 fw-bold"><i class="bi bi-cpu text-primary me-2"></i>Analisis Model SPK (Random Forest)</h6>
        </div>
        <div class="card-body p-4">
          <div class="row align-items-center mb-3">
            <div class="col-md-4 text-center">
              <div style="max-width: 160px; margin: 0 auto;">
                <canvas id="miniRiskChart"></canvas>
              </div>
            </div>
            <div class="col-md-8">
              <h6 class="fw-bold mb-2 text-slate-800">3 Faktor Pemicu Risiko Teratas:</h6>
              <div class="d-flex flex-column gap-2">
                <?php foreach ($factors as $idx => $f): ?>
                  <div class="p-2 px-3 rounded-3 <?= $is_high_risk ? 'badge-glass-rose' : 'badge-glass-indigo' ?> small d-flex justify-content-between align-items-center">
                    <span><strong>#<?= $idx + 1 ?></strong> <?= htmlspecialchars($f) ?></span>
                    <i class="bi bi-arrow-up-right"></i>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>

          <div class="p-3 rounded-4 small" style="background: rgba(241, 245, 249, 0.6); border: 1px solid rgba(226, 232, 240, 0.6);">
            <strong class="text-slate-800">Catatan Refleksi Sistem:</strong>
            <p class="mb-0 mt-1 text-muted"><?= htmlspecialchars($mhs['catatan_refleksi']) ?></p>
          </div>
        </div>
      </div>

      <!-- Card Rincian Jawaban 12 Indikator (Tabel skrining_detail) -->
      <div class="card glass-card border-0 mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
          <h6 class="mb-0 fw-bold"><i class="bi bi-card-checklist text-primary me-2"></i>Rincian 12 Indikator Jawaban (Tabel skrining_detail)</h6>
          <span class="badge badge-glass-indigo px-3 py-1 rounded-pill">Master-Detail</span>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table align-middle mb-0">
              <thead>
                <tr>
                  <th style="width: 5%;">No</th>
                  <th style="width: 25%;">Kategori</th>
                  <th style="width: 45%;">Pertanyaan Indikator</th>
                  <th style="width: 12%;">Jawaban</th>
                  <th style="width: 13%;">Interpretasi</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($details)): ?>
                  <?php foreach ($details as $idx => $d): ?>
                    <tr>
                      <td><?= $idx + 1 ?></td>
                      <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($d['nama_kategori']) ?></span></td>
                      <td><?= htmlspecialchars($d['teks_pertanyaan']) ?></td>
                      <td><strong><?= htmlspecialchars($d['nilai_raw']) ?></strong></td>
                      <td>
                        <span class="badge badge-glass-indigo"><?= htmlspecialchars($d['interpretasi_singkat'] ?: '-') ?></span>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Card Riwayat Log Bimbingan Konseling (Tabel konseling_riwayat) -->
      <div class="card glass-card border-0 mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
          <h6 class="mb-0 fw-bold"><i class="bi bi-journal-text text-primary me-2"></i>Log Riwayat Konseling Dosen PA (Tabel konseling_riwayat)</h6>
          <span class="badge badge-glass-indigo px-3 py-1 rounded-pill">Total Log: <?= count($riwayat_konseling) ?></span>
        </div>
        <div class="card-body p-3">
          <?php if (!empty($riwayat_konseling)): ?>
            <div class="timeline d-flex flex-column gap-3">
              <?php foreach ($riwayat_konseling as $log): ?>
                <div class="p-3 rounded-4" style="background: rgba(255, 255, 255, 0.6); border: 1px solid rgba(226, 232, 240, 0.7);">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge <?= ($log['status_pertemuan'] === 'Selesai' ? 'badge-glass-emerald' : 'badge-glass-amber') ?> rounded-pill px-3 py-1">
                      <?= htmlspecialchars($log['status_pertemuan']) ?>
                    </span>
                    <small class="text-muted"><i class="bi bi-clock me-1"></i> <?= date('d M Y H:i', strtotime($log['created_at'])) ?></small>
                  </div>
                  <div class="small mb-1">
                    <strong>Jadwal & Tempat:</strong> <?= htmlspecialchars($log['tanggal_konseling'] ?: '-') ?> (<?= htmlspecialchars($log['tempat_metode'] ?: '-') ?>)
                  </div>
                  <div class="p-2 rounded-3 small mb-2" style="background: rgba(241, 245, 249, 0.5);">
                    <strong>Catatan Dosen PA:</strong><br>
                    <?= nl2br(htmlspecialchars($log['catatan_bimbingan'])) ?>
                  </div>
                  <?php if (!empty($log['solusi_tindak_lanjut'])): ?>
                    <div class="small text-muted">
                      <strong>Rekomendasi Tindak Lanjut:</strong> <?= htmlspecialchars($log['solusi_tindak_lanjut']) ?>
                    </div>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="text-muted small text-center my-3">Belum ada catatan log bimbingan konseling untuk sesi skrining ini.</p>
          <?php endif; ?>
        </div>
      </div>

    </div>

  </div>
</main>

<footer class="footer-glass mt-auto py-3">
  <div class="container text-center text-muted small">
    MindCare SPK Dosen &copy; <?= date('Y') ?> &bull; Dosen PA: <?= htmlspecialchars($dosen_nama) ?>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const miniCtx = document.getElementById('miniRiskChart').getContext('2d');
  new Chart(miniCtx, {
    type: 'doughnut',
    data: {
      labels: ['Rendah', 'Tinggi'],
      datasets: [{
        data: [<?= $mhs['probabilitas_rendah'] ?>, <?= $mhs['probabilitas_tinggi'] ?>],
        backgroundColor: ['#10b981', '#f43f5e'],
        borderWidth: 2,
        borderColor: '#ffffff'
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      cutout: '72%'
    }
  });
</script>
</body>
</html>
