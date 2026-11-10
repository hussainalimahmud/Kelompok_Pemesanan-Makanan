<?php
require_once 'koneksi.php';

$data = null;
$details = [];
$id = intval($_GET['id'] ?? 0);

if ($id > 0 && $pdo) {
    // 1. Ambil data MASTER
    $stmt = $pdo->prepare("SELECT sm.*, m.nim, m.nama_mahasiswa, p.nama_prodi, m.angkatan, d.nama_dosen, d.nidn 
                           FROM `skrining_master` sm 
                           JOIN `mahasiswa` m ON sm.mahasiswa_id = m.id 
                           JOIN `program_studi` p ON m.prodi_id = p.id
                           JOIN `dosen` d ON sm.dosen_id = d.id 
                           WHERE sm.`id` = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch();

    // 2. Ambil data DETAIL (Implementasi Master-Detail)
    if ($data) {
        $stmtDetail = $pdo->prepare("SELECT sd.*, pi.teks_pertanyaan, ki.nama_kategori 
                                     FROM `skrining_detail` sd 
                                     JOIN `pertanyaan_indikator` pi ON sd.pertanyaan_id = pi.id 
                                     JOIN `kategori_indikator` ki ON pi.kategori_id = ki.id 
                                     WHERE sd.`skrining_id` = ? 
                                     ORDER BY pi.urutan ASC");
        $stmtDetail->execute([$id]);
        $details = $stmtDetail->fetchAll();
    }
}

if (!$data) {
    header('Location: index.php');
    exit;
}

$is_high_risk = ($data['hasil_prediksi'] == 1);
$factors_list = array_map('trim', explode(',', $data['faktor_utama']));

$back_url = 'dashboard_mahasiswa.php';
if (isset($_SESSION['role']) && $_SESSION['role'] === 'dosen') {
    $back_url = 'dosen/data_mahasiswa.php';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lembar Hasil Skrining - MindCare SPK</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    @media print {
      .navbar, .footer, .no-print {
        display: none !important;
      }
      body {
        background: #fff !important;
      }
      .card, .glass-card {
        border: 1px solid #ccc !important;
        box-shadow: none !important;
        background: #fff !important;
      }
    }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<main class="container my-4">
  
  <div class="d-flex justify-content-between align-items-center mb-3 no-print">
    <a href="<?= $back_url ?>" class="btn btn-glass btn-sm rounded-pill px-3">
      <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
    </a>
    <div class="d-flex gap-2">
      <button onclick="window.print()" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm">
        <i class="bi bi-printer me-1"></i> Cetak Lembar Hasil
      </button>
    </div>
  </div>

  <div class="row g-4 justify-content-center">
    
    <!-- Kolom Kiri: Ringkasan Hasil & Visualisasi -->
    <div class="col-lg-5">
      
      <!-- Card Status Prediksi -->
      <div class="card glass-card border-0 mb-4 overflow-hidden">
        <div class="card-body p-4 text-center <?= $is_high_risk ? 'badge-glass-rose' : 'badge-glass-emerald' ?>" style="border-radius: 20px;">
          <div class="mb-3">
            <span class="d-inline-flex align-items-center justify-content-center rounded-circle <?= $is_high_risk ? 'bg-danger text-white' : 'bg-success text-white' ?>" style="width: 68px; height: 68px; font-size: 1.8rem; box-shadow: 0 8px 20px rgba(0,0,0,0.08);">
              <i class="bi <?= $is_high_risk ? 'bi-exclamation-triangle-fill' : 'bi-check2-circle' ?>"></i>
            </span>
          </div>
          <span class="badge <?= $is_high_risk ? 'bg-danger' : 'bg-success' ?> px-3 py-2 rounded-pill fs-6 mb-2">
            <?= htmlspecialchars($data['kategori']) ?>
          </span>
          <h4 class="fw-bold mb-1 text-slate-900">
            <?= $is_high_risk ? 'Perlu Pendampingan Dosen PA' : 'Kondisi Mental Relatif Stabil' ?>
          </h4>
          <p class="text-muted small mb-0">
            Kode Sesi: <code><?= htmlspecialchars($data['kode_sesi']) ?></code> &bull; <?= date('d M Y, H:i', strtotime($data['tanggal_skrining'])) ?> WIB
          </p>
        </div>
      </div>

      <!-- Card Chart Probabilitas -->
      <div class="card glass-card border-0 mb-4">
        <div class="card-header py-3">
          <h6 class="mb-0 fw-bold"><i class="bi bi-pie-chart text-primary me-2"></i>Probabilitas Tingkat Risiko</h6>
        </div>
        <div class="card-body p-4 text-center">
          <div style="max-width: 230px; margin: 0 auto;">
            <canvas id="riskChart"></canvas>
          </div>
          <div class="row mt-3 text-center">
            <div class="col-6 border-end">
              <span class="text-muted small">Risiko Rendah</span>
              <h5 class="fw-bold text-success mb-0"><?= $data['probabilitas_rendah'] ?>%</h5>
            </div>
            <div class="col-6">
              <span class="text-muted small">Risiko Tinggi</span>
              <h5 class="fw-bold text-danger mb-0"><?= $data['probabilitas_tinggi'] ?>%</h5>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Kolom Kanan: Detail Master & Faktor Pemicu -->
    <div class="col-lg-7">
      
      <!-- Card Data Diri Mahasiswa & Dosen PA -->
      <div class="card glass-card border-0 mb-4">
        <div class="card-header py-3">
          <h6 class="mb-0 fw-bold"><i class="bi bi-person-lines-fill text-primary me-2"></i>Identitas Mahasiswa & Pembimbing (Master Header)</h6>
        </div>
        <div class="card-body p-4">
          <div class="row g-3">
            <div class="col-sm-6">
              <small class="text-muted d-block">Nama Lengkap</small>
              <strong class="fs-6 text-slate-800"><?= htmlspecialchars($data['nama_mahasiswa']) ?></strong>
            </div>
            <div class="col-sm-6">
              <small class="text-muted d-block">NIM</small>
              <strong class="fs-6 font-monospace text-slate-800"><?= htmlspecialchars($data['nim']) ?></strong>
            </div>
            <div class="col-sm-6">
              <small class="text-muted d-block">Program Studi & Angkatan</small>
              <span><?= htmlspecialchars($data['nama_prodi']) ?> (<?= htmlspecialchars($data['angkatan']) ?>)</span>
            </div>
            <div class="col-sm-6">
              <small class="text-muted d-block">Dosen Pembimbing Akademik</small>
              <span class="text-primary fw-semibold"><?= htmlspecialchars($data['nama_dosen']) ?></span>
            </div>
          </div>
        </div>
      </div>

      <!-- Card Faktor Utama Pemicu -->
      <div class="card glass-card border-0 mb-4">
        <div class="card-header py-3">
          <h6 class="mb-0 fw-bold"><i class="bi bi-diagram-3 text-primary me-2"></i>3 Faktor Pemicu Utama (Feature Importance)</h6>
        </div>
        <div class="card-body p-4">
          <p class="small text-muted mb-3">Model Machine Learning mengidentifikasi indikator berikut yang paling berkontribusi terhadap kondisi psikologis Anda:</p>
          <div class="d-flex flex-column gap-2">
            <?php foreach ($factors_list as $index => $faktor): ?>
              <div class="d-flex align-items-center justify-content-between p-3 rounded-4 <?= $is_high_risk ? 'badge-glass-rose' : 'badge-glass-indigo' ?>">
                <div class="d-flex align-items-center gap-2">
                  <span class="badge <?= $is_high_risk ? 'bg-danger' : 'bg-primary' ?> rounded-pill">#<?= $index + 1 ?></span>
                  <span class="fw-semibold text-slate-800"><?= htmlspecialchars($faktor) ?></span>
                </div>
                <i class="bi bi-graph-up-arrow <?= $is_high_risk ? 'text-danger' : 'text-primary' ?>"></i>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <!-- Card Catatan Refleksi & Rekomendasi Solusi -->
      <div class="card glass-card border-0 mb-4">
        <div class="card-header py-3">
          <h6 class="mb-0 fw-bold"><i class="bi bi-lightbulb text-warning me-2"></i>Catatan Refleksi & Rekomendasi Solusi</h6>
        </div>
        <div class="card-body p-4">
          <div class="alert <?= $is_high_risk ? 'alert-warning' : 'alert-success' ?> border-0 rounded-4 mb-3">
            <div class="fw-semibold mb-1"><i class="bi bi-info-circle me-1"></i> Catatan Model:</div>
            <p class="mb-0 small"><?= htmlspecialchars($data['catatan_refleksi']) ?></p>
          </div>

          <h6 class="fw-bold small text-uppercase text-muted mb-2">Langkah Tindak Lanjut:</h6>
          <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small">
            <?php if ($is_high_risk): ?>
              <li class="d-flex align-items-start gap-2">
                <i class="bi bi-check2-circle text-danger fs-6"></i>
                <span><strong>Konsultasi Akademik:</strong> Diskusikan beban SKS dan manajemen tugas perkuliahan dengan Dosen Pembimbing Akademik (PA).</span>
              </li>
              <li class="d-flex align-items-start gap-2">
                <i class="bi bi-check2-circle text-danger fs-6"></i>
                <span><strong>Istirahat Cukup:</strong> Hindari begadang berlebihan untuk membantu pemulihan neurotransmitter otak.</span>
              </li>
            <?php else: ?>
              <li class="d-flex align-items-start gap-2">
                <i class="bi bi-check2-circle text-success fs-6"></i>
                <span><strong>Pertahankan Ritme:</strong> Jaga keseimbangan antara waktu kuliah, tugas mandiri, dan relaksasi pribadi.</span>
              </li>
            <?php endif; ?>
          </ul>
        </div>
      </div>

    </div>

  </div>

  <!-- TABEL RINCIAN DETAIL JAWABAN (MASTER-DETAIL VIEW) -->
  <div class="card glass-card border-0 mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
      <h6 class="mb-0 fw-bold"><i class="bi bi-list-check text-primary me-2"></i>Rincian 12 Indikator Jawaban (Detail Transaksi)</h6>
      <span class="badge badge-glass-indigo px-3 py-1 rounded-pill">Tabel skrining_detail</span>
    </div>
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead>
          <tr>
            <th style="width: 5%;">No</th>
            <th style="width: 25%;">Kategori</th>
            <th style="width: 40%;">Pertanyaan Indikator</th>
            <th style="width: 15%;">Nilai Input</th>
            <th style="width: 15%;">Interpretasi</th>
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

</main>

<?php include 'footer.php'; ?>

<script>
  const ctx = document.getElementById('riskChart').getContext('2d');
  new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Risiko Rendah', 'Risiko Tinggi'],
      datasets: [{
        data: [<?= $data['probabilitas_rendah'] ?>, <?= $data['probabilitas_tinggi'] ?>],
        backgroundColor: ['#10b981', '#f43f5e'],
        borderWidth: 2,
        borderColor: '#ffffff'
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'bottom', labels: { font: { size: 12 }, padding: 15 } }
      },
      cutout: '72%'
    }
  });
</script>

</body>
</html>
