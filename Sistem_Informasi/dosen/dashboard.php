<?php
require_once 'auth_check.php';

$dosen_id = $_SESSION['user_id'];
$dosen_nama = $_SESSION['nama_lengkap'];
$dosen_nidn = $_SESSION['nidn'];

$total_bimbingan = 20; // 1 dosen = 20 mahasiswa
$total_skrining = 0;
$total_tinggi = 0;
$total_rendah = 0;
$total_perlu_konseling = 0;
$latest_data = [];

if ($pdo) {
    // 1. Total mahasiswa unik yang sudah mengisi skrining dari 20 bimbingan dosen ini
    $stmtCount = $pdo->prepare("SELECT COUNT(DISTINCT mahasiswa_id) FROM `skrining_master` WHERE `dosen_id` = ?");
    $stmtCount->execute([$dosen_id]);
    $total_skrining = $stmtCount->fetchColumn();

    // 2. Total Risiko Tinggi & Rendah pada skrining terkini mahasiswa bimbingan dosen ini
    $stmtTinggi = $pdo->prepare("SELECT COUNT(*) FROM `skrining_master` WHERE `dosen_id` = ? AND `hasil_prediksi` = 1");
    $stmtTinggi->execute([$dosen_id]);
    $total_tinggi = $stmtTinggi->fetchColumn();

    $stmtRendah = $pdo->prepare("SELECT COUNT(*) FROM `skrining_master` WHERE `dosen_id` = ? AND `hasil_prediksi` = 0");
    $stmtRendah->execute([$dosen_id]);
    $total_rendah = $stmtRendah->fetchColumn();

    // 3. Perlu Konseling (Risiko tinggi & Belum ditindaklanjuti)
    $stmtKonsel = $pdo->prepare("SELECT COUNT(*) FROM `skrining_master` WHERE `dosen_id` = ? AND `hasil_prediksi` = 1 AND `status_konseling` = 'Belum Ditindaklanjuti'");
    $stmtKonsel->execute([$dosen_id]);
    $total_perlu_konseling = $stmtKonsel->fetchColumn();

    // 4. Ambil 5 data skrining_master terbaru dari mahasiswa bimbingan dosen ini
    $stmtRecent = $pdo->prepare("SELECT sm.*, m.nim, m.nama_mahasiswa, m.jenis_kelamin 
                                 FROM `skrining_master` sm 
                                 JOIN `mahasiswa` m ON sm.mahasiswa_id = m.id 
                                 WHERE sm.`dosen_id` = ? 
                                 ORDER BY sm.`created_at` DESC LIMIT 5");
    $stmtRecent->execute([$dosen_id]);
    $latest_data = $stmtRecent->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Monitoring Dosen PA - MindCare SPK</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<?php include 'navbar_dosen.php'; ?>

<main class="container my-4">
  
  <!-- Glass Banner Dosen -->
  <div class="glass-banner">
    <div class="row align-items-center">
      <div class="col-lg-8">
        <span class="badge badge-glass-indigo px-3 py-1 rounded-pill mb-2 fw-semibold">
          <i class="bi bi-shield-check me-1"></i> Panel Dosen Pembimbing Akademik
        </span>
        <h2 class="fw-bold mb-1 text-slate-900">Selamat Datang, <?= htmlspecialchars($dosen_nama) ?>!</h2>
        <p class="text-muted mb-0">NIDN: <strong class="text-dark"><?= htmlspecialchars($dosen_nidn) ?></strong> &bull; Anda membimbing dan mengampu <strong>20 Mahasiswa Perwalian</strong>.</p>
      </div>
      <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
        <a href="data_mahasiswa.php" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm d-inline-flex align-items-center gap-2">
          <i class="bi bi-people-fill"></i>
          <span>Kelola 20 Mahasiswa</span>
        </a>
      </div>
    </div>
  </div>

  <!-- 4 Stat Cards Glass -->
  <div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
      <div class="card glass-card border-0 stat-card h-100 p-2">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="avatar-badge fs-3" style="width: 52px; height: 52px; border-radius: 16px;">
            <i class="bi bi-people"></i>
          </div>
          <div>
            <div class="text-muted small">Total Perwalian</div>
            <h3 class="fw-bold mb-0 text-slate-800">20 <small class="fs-6 fw-normal text-muted">Mhs</small></h3>
          </div>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-lg-3">
      <div class="card glass-card border-0 stat-card h-100 p-2" style="border-left: 4px solid #0ea5e9 !important;">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="d-inline-flex align-items-center justify-content-center" style="width: 52px; height: 52px; border-radius: 16px; background: rgba(14, 165, 233, 0.12); color: #0284c7; font-size: 1.6rem;">
            <i class="bi bi-clipboard2-check"></i>
          </div>
          <div>
            <div class="text-muted small">Sudah Skrining</div>
            <h3 class="fw-bold mb-0" style="color: #0284c7;"><?= $total_skrining ?> <small class="fs-6 fw-normal text-muted">/ 20</small></h3>
          </div>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-lg-3">
      <div class="card glass-card border-0 stat-card h-100 p-2" style="border-left: 4px solid #f43f5e !important;">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="d-inline-flex align-items-center justify-content-center" style="width: 52px; height: 52px; border-radius: 16px; background: rgba(244, 63, 94, 0.12); color: #e11d48; font-size: 1.6rem;">
            <i class="bi bi-exclamation-triangle-fill"></i>
          </div>
          <div>
            <div class="text-muted small">Risiko Tinggi (Stres)</div>
            <h3 class="fw-bold mb-0 text-danger"><?= $total_tinggi ?></h3>
          </div>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-lg-3">
      <div class="card glass-card border-0 stat-card h-100 p-2" style="border-left: 4px solid #f59e0b !important;">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="d-inline-flex align-items-center justify-content-center" style="width: 52px; height: 52px; border-radius: 16px; background: rgba(245, 158, 11, 0.12); color: #d97706; font-size: 1.6rem;">
            <i class="bi bi-chat-heart-fill"></i>
          </div>
          <div>
            <div class="text-muted small">Perlu Dijadwalkan</div>
            <h3 class="fw-bold mb-0 text-warning"><?= $total_perlu_konseling ?></h3>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Charts & Info Row -->
  <div class="row g-4 mb-4">
    <div class="col-lg-5">
      <div class="card glass-card border-0 h-100">
        <div class="card-header py-3">
          <h6 class="mb-0 fw-bold"><i class="bi bi-pie-chart text-primary me-2"></i>Proporsi Risiko Mahasiswa Bimbingan</h6>
        </div>
        <div class="card-body p-4 text-center">
          <?php if (($total_tinggi + $total_rendah) > 0): ?>
            <div style="max-width: 230px; margin: 0 auto;">
              <canvas id="chartProporsi"></canvas>
            </div>
            <div class="row mt-3 text-center">
              <div class="col-6 border-end">
                <span class="text-muted small">Risiko Rendah</span>
                <h5 class="fw-bold text-success mb-0"><?= $total_rendah ?> Mhs</h5>
              </div>
              <div class="col-6">
                <span class="text-muted small">Risiko Tinggi</span>
                <h5 class="fw-bold text-danger mb-0"><?= $total_tinggi ?> Mhs</h5>
              </div>
            </div>
          <?php else: ?>
            <p class="text-muted my-5">Belum ada mahasiswa bimbingan yang mengisi kuesioner.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="col-lg-7">
      <div class="card glass-card border-0 h-100">
        <div class="card-header py-3">
          <h6 class="mb-0 fw-bold"><i class="bi bi-info-circle text-primary me-2"></i>Tugas & Panduan Dosen PA (Master-Detail System)</h6>
        </div>
        <div class="card-body p-4">
          <div class="alert alert-glass mb-3">
            <h6 class="fw-bold mb-1 text-slate-800"><i class="bi bi-lightbulb-fill me-1 text-warning"></i> Implementasi Master-Detail:</h6>
            <p class="mb-0 small text-muted">Sistem sekarang mencatat sesi transaksi secara terstruktur (Master Header `skrining_master` dan 12 butir detail `skrining_detail`), serta log riwayat bimbingan berkala `konseling_riwayat`.</p>
          </div>
          <h6 class="fw-bold small text-uppercase text-muted mb-2">Panduan Penggunaan:</h6>
          <ol class="small text-muted ps-3 mb-0 d-flex flex-column gap-2">
            <li>Buka menu <strong>20 Mahasiswa Perwalian</strong> untuk melihat status lengkap mahasiswa Anda.</li>
            <li>Prioritaskan mahasiswa dengan badge <strong>Risiko Tinggi</strong>.</li>
            <li>Klik tombol <strong>Evaluasi & Konseling</strong> untuk membaca detail 12 indikator dan menjadwalkan konseling tatap muka atau online.</li>
            <li>Catatan bimbingan akan tersimpan di tabel `konseling_riwayat` dan langsung memicu notifikasi pada dashboard mahasiswa bersangkutan.</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Tabel Data Skrining Terbaru -->
  <div class="card glass-card border-0 mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
      <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history text-primary me-2"></i>5 Pengisian Sesi Terkini Mahasiswa Bimbingan</h6>
      <a href="data_mahasiswa.php" class="btn btn-glass btn-sm rounded-pill px-3">
        Kelola 20 Mahasiswa <i class="bi bi-arrow-right"></i>
      </a>
    </div>
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead>
          <tr>
            <th>Kode Sesi</th>
            <th>Waktu</th>
            <th>NIM</th>
            <th>Nama Mahasiswa</th>
            <th>IPK Snapshot</th>
            <th>Hasil SPK AI</th>
            <th>Status Konseling</th>
            <th class="text-end">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($latest_data)): ?>
            <?php foreach ($latest_data as $mhs): ?>
              <tr>
                <td><span class="badge bg-light text-dark font-monospace border"><?= htmlspecialchars($mhs['kode_sesi']) ?></span></td>
                <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($mhs['tanggal_skrining'])) ?></td>
                <td><span class="badge bg-light text-dark border font-monospace"><?= htmlspecialchars($mhs['nim']) ?></span></td>
                <td><strong><?= htmlspecialchars($mhs['nama_mahasiswa']) ?></strong></td>
                <td><?= number_format($mhs['ipk_snapshot'], 2) ?></td>
                <td>
                  <?php if ($mhs['hasil_prediksi'] == 1): ?>
                    <span class="badge badge-glass-rose px-2 py-1">
                      <i class="bi bi-exclamation-octagon-fill me-1"></i> Risiko Tinggi (<?= $mhs['probabilitas_tinggi'] ?>%)
                    </span>
                  <?php else: ?>
                    <span class="badge badge-glass-emerald px-2 py-1">
                      <i class="bi bi-check-circle-fill me-1"></i> Risiko Rendah (<?= $mhs['probabilitas_rendah'] ?>%)
                    </span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($mhs['status_konseling'] === 'Dijadwalkan Konseling'): ?>
                    <span class="badge badge-glass-amber">Dijadwalkan</span>
                  <?php elseif ($mhs['status_konseling'] === 'Selesai Konseling'): ?>
                    <span class="badge badge-glass-emerald">Selesai</span>
                  <?php else: ?>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary border">Belum Ditindaklanjuti</span>
                  <?php endif; ?>
                </td>
                <td class="text-end">
                  <a href="detail_mahasiswa.php?id=<?= $mhs['id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill">
                    <i class="bi bi-eye me-1"></i> Detail
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" class="text-center py-4 text-muted">Belum ada data skrining dari mahasiswa perwalian Anda.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</main>

<footer class="footer-glass mt-auto py-3">
  <div class="container text-center text-muted small">
    MindCare SPK Dosen &copy; <?= date('Y') ?> &bull; Dosen PA: <?= htmlspecialchars($dosen_nama) ?>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php if (($total_tinggi + $total_rendah) > 0): ?>
<script>
  const ctxProporsi = document.getElementById('chartProporsi').getContext('2d');
  new Chart(ctxProporsi, {
    type: 'doughnut',
    data: {
      labels: ['Risiko Rendah', 'Risiko Tinggi'],
      datasets: [{
        data: [<?= $total_rendah ?>, <?= $total_tinggi ?>],
        backgroundColor: ['#10b981', '#f43f5e'],
        borderWidth: 2,
        borderColor: '#ffffff'
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { position: 'bottom' } },
      cutout: '72%'
    }
  });
</script>
<?php endif; ?>

</body>
</html>
