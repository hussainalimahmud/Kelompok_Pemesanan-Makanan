<?php
require_once 'koneksi.php';

// Wajibkan Mahasiswa Login terlebih dahulu
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa') {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'dosen') {
        header('Location: dosen/dashboard.php');
        exit;
    }
    header('Location: login.php?tab=mahasiswa&pesan=silakan_login');
    exit;
}

$mhs_nim = $_SESSION['nim'];
$mhs_nama = $_SESSION['nama_lengkap'];
$mhs_prodi = $_SESSION['prodi'];
$mhs_angkatan = $_SESSION['angkatan'];
$mhs_jk = $_SESSION['jenis_kelamin'];
$mhs_usia = $_SESSION['usia'];
$dosen_pa = $_SESSION['nama_dosen_pa'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kuesioner Skrining Kesehatan Mental - MindCare SPK</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<!-- Hero Glass Banner -->
<header class="container mt-4">
  <div class="glass-banner">
    <div class="row align-items-center">
      <div class="col-lg-8">
        <span class="badge badge-glass-indigo px-3 py-1 rounded-pill fw-semibold mb-2">
          <i class="bi bi-cpu me-1"></i> SPK Berbasis Machine Learning
        </span>
        <h2 class="fw-bold mb-2 text-slate-900">Skrining Kesehatan Mental & Beban Studi</h2>
        <p class="text-muted mb-0">
          Selamat datang, <strong><?= htmlspecialchars($mhs_nama) ?></strong>. Kuesioner ini akan diolah oleh model AI dan hasilnya dapat ditinjau oleh Dosen Pembimbing Akademik Anda: <strong><?= htmlspecialchars($dosen_pa) ?></strong>.
        </p>
      </div>
      <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
        <div class="d-inline-flex p-3 rounded-4 gap-3 text-start align-items-center" style="background: rgba(255, 255, 255, 0.7); border: 1px solid rgba(226, 232, 240, 0.7);">
          <div class="avatar-badge fs-4" style="width: 48px; height: 48px;">
            <i class="bi bi-shield-check"></i>
          </div>
          <div>
            <div class="fw-bold text-dark">Data Terlindungi</div>
            <small class="text-muted">Kerahasiaan data bimbingan akademik terjaga.</small>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>

<main class="container mb-5">
  <?php if (isset($_GET['pesan']) && $_GET['pesan'] === 'error'): ?>
    <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4" role="alert">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>
      <?= htmlspecialchars($_GET['error_msg'] ?? 'Terjadi kesalahan saat memproses data. Silakan coba lagi.') ?>
    </div>
  <?php endif; ?>

  <div class="row justify-content-center">
    <div class="col-lg-10">
      
      <div class="card glass-card border-0">
        <div class="card-header py-3">
          <div class="d-flex align-items-center gap-2">
            <span class="avatar-badge">
              <i class="bi bi-clipboard2-pulse"></i>
            </span>
            <h5 class="mb-0 fw-bold text-slate-900">Formulir Kuesioner Mahasiswa</h5>
          </div>
          <small class="text-muted">Isilah setiap pertanyaan berikut secara jujur sesuai kondisi aktual yang Anda alami dalam beberapa waktu terakhir.</small>
        </div>

        <div class="card-body p-4">
          <form action="proses_skrining.php" method="POST" id="formSkrining">
            
            <!-- SECTION 1: Identitas Mahasiswa (Auto-filled) -->
            <div class="mb-4">
              <h6 class="text-primary fw-bold text-uppercase small border-bottom pb-2 mb-3">
                <i class="bi bi-person-badge me-1"></i> 1. Identitas Mahasiswa
              </h6>
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Nomor Induk Mahasiswa (NIM)</label>
                  <input type="text" class="form-control font-monospace fw-bold" name="nim" value="<?= htmlspecialchars($mhs_nim) ?>" readonly style="background: rgba(241, 245, 249, 0.6);">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Nama Lengkap Mahasiswa</label>
                  <input type="text" class="form-control fw-bold" name="nama_mahasiswa" value="<?= htmlspecialchars($mhs_nama) ?>" readonly style="background: rgba(241, 245, 249, 0.6);">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Program Studi</label>
                  <input type="text" class="form-control" name="prodi" value="<?= htmlspecialchars($mhs_prodi) ?>" readonly style="background: rgba(241, 245, 249, 0.6);">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Tahun Angkatan</label>
                  <input type="text" class="form-control" name="angkatan" value="<?= htmlspecialchars($mhs_angkatan) ?>" readonly style="background: rgba(241, 245, 249, 0.6);">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Dosen Pembimbing Akademik</label>
                  <input type="text" class="form-control text-primary fw-semibold" value="<?= htmlspecialchars($dosen_pa) ?>" readonly style="background: rgba(241, 245, 249, 0.6);">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Jenis Kelamin</label>
                  <input type="text" class="form-control" name="jenis_kelamin" value="<?= htmlspecialchars($mhs_jk) ?>" readonly style="background: rgba(241, 245, 249, 0.6);">
                </div>
                <div class="col-md-6">
                  <label for="usia" class="form-label">Usia (Tahun) <span class="text-danger">*</span></label>
                  <input type="number" class="form-control" id="usia" name="usia" min="16" max="50" value="<?= htmlspecialchars($mhs_usia) ?>" required>
                </div>
              </div>
            </div>

            <!-- SECTION 2: Indikator Akademik & Finansial -->
            <div class="mb-4">
              <h6 class="text-primary fw-bold text-uppercase small border-bottom pb-2 mb-3">
                <i class="bi bi-mortarboard me-1"></i> 2. Kondisi Akademik & Finansial
              </h6>
              
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="ipk" class="form-label">Indeks Prestasi Kumulatif (IPK Terakhir) <span class="text-danger">*</span></label>
                  <input type="number" step="0.01" min="0" max="4.00" class="form-control" id="ipk" name="ipk" placeholder="Contoh: 3.50" value="3.50" required>
                  <div class="form-text text-muted">Skala 0.00 – 4.00 (otomatis diselaraskan ke model).</div>
                </div>

                <div class="col-md-6">
                  <label for="jam_belajar_harian" class="form-label">Rata-rata Waktu Belajar Mandiri / Hari <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <input type="number" class="form-control" id="jam_belajar_harian" name="jam_belajar_harian" min="1" max="18" value="4" required>
                    <span class="input-group-text">Jam / Hari</span>
                  </div>
                  <div class="form-text text-muted">Di luar jam kuliah tatap muka di kelas.</div>
                </div>

                <!-- Tekanan Akademik (Skala 1-5) -->
                <div class="col-12 mt-3">
                  <label class="form-label d-flex justify-content-between">
                    <span>Tingkat Tekanan Akademik yang Dirasakan <span class="text-danger">*</span></span>
                    <span class="text-muted small">1 = Sangat Ringan &bull; 5 = Sangat Berat</span>
                  </label>
                  <div class="rating-scale-group">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                      <div class="rating-scale-item">
                        <input type="radio" name="tekanan_akademik" id="ta_<?= $i ?>" value="<?= $i ?>" <?= $i === 3 ? 'checked' : '' ?> required>
                        <label for="ta_<?= $i ?>">
                          <div><?= $i ?></div>
                          <div class="rating-scale-desc">
                            <?= ($i == 1 ? 'Rendah' : ($i == 3 ? 'Sedang' : ($i == 5 ? 'Tinggi' : 'Level ' . $i))) ?>
                          </div>
                        </label>
                      </div>
                    <?php endfor; ?>
                  </div>
                </div>

                <!-- Kepuasan Belajar (Skala 1-5) -->
                <div class="col-12 mt-3">
                  <label class="form-label d-flex justify-content-between">
                    <span>Kepuasan Terhadap Proses & Lingkungan Belajar <span class="text-danger">*</span></span>
                    <span class="text-muted small">1 = Sangat Tidak Puas &bull; 5 = Sangat Puas</span>
                  </label>
                  <div class="rating-scale-group">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                      <div class="rating-scale-item">
                        <input type="radio" name="kepuasan_belajar" id="kb_<?= $i ?>" value="<?= $i ?>" <?= $i === 3 ? 'checked' : '' ?> required>
                        <label for="kb_<?= $i ?>">
                          <div><?= $i ?></div>
                          <div class="rating-scale-desc">
                            <?= ($i == 1 ? 'Tidak Puas' : ($i == 3 ? 'Cukup' : ($i == 5 ? 'Sangat Puas' : 'Level ' . $i))) ?>
                          </div>
                        </label>
                      </div>
                    <?php endfor; ?>
                  </div>
                </div>

                <!-- Tekanan Keuangan (Skala 1-5) -->
                <div class="col-12 mt-3">
                  <label class="form-label d-flex justify-content-between">
                    <span>Beban / Tekanan Finansial & Biaya Hidup Kuliah <span class="text-danger">*</span></span>
                    <span class="text-muted small">1 = Tidak Ada Tekanan &bull; 5 = Sangat Tertekan</span>
                  </label>
                  <div class="rating-scale-group">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                      <div class="rating-scale-item">
                        <input type="radio" name="tekanan_keuangan" id="tk_<?= $i ?>" value="<?= $i ?>" <?= $i === 3 ? 'checked' : '' ?> required>
                        <label for="tk_<?= $i ?>">
                          <div><?= $i ?></div>
                          <div class="rating-scale-desc">
                            <?= ($i == 1 ? 'Aman' : ($i == 3 ? 'Cukup' : ($i == 5 ? 'Kritis' : 'Level ' . $i))) ?>
                          </div>
                        </label>
                      </div>
                    <?php endfor; ?>
                  </div>
                </div>

                <!-- Tekanan Pekerjaan (Skala 0-5) -->
                <div class="col-12 mt-3">
                  <label class="form-label d-flex justify-content-between">
                    <span>Tekanan dari Pekerjaan Sampingan / Freelance / Magang <span class="text-danger">*</span></span>
                    <span class="text-muted small">0 = Tidak Bekerja &bull; 5 = Sangat Melelahkan</span>
                  </label>
                  <div class="rating-scale-group">
                    <div class="rating-scale-item">
                      <input type="radio" name="tekanan_pekerjaan" id="tp_0" value="0" checked required>
                      <label for="tp_0">
                        <div>0</div>
                        <div class="rating-scale-desc">Tidak Kerja</div>
                      </label>
                    </div>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                      <div class="rating-scale-item">
                        <input type="radio" name="tekanan_pekerjaan" id="tp_<?= $i ?>" value="<?= $i ?>">
                        <label for="tp_<?= $i ?>">
                          <div><?= $i ?></div>
                          <div class="rating-scale-desc">
                            <?= ($i == 1 ? 'Ringan' : ($i == 3 ? 'Sedang' : ($i == 5 ? 'Berat' : 'Lvl ' . $i))) ?>
                          </div>
                        </label>
                      </div>
                    <?php endfor; ?>
                  </div>
                </div>

              </div>
            </div>

            <!-- SECTION 3: Gaya Hidup & Kesejahteraan Psikologis -->
            <div class="mb-4">
              <h6 class="text-primary fw-bold text-uppercase small border-bottom pb-2 mb-3">
                <i class="bi bi-heart-pulse me-1"></i> 3. Pola Hidup & Kesejahteraan Psikologis
              </h6>
              
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="durasi_tidur" class="form-label">Rata-rata Durasi Tidur Harian <span class="text-danger">*</span></label>
                  <select class="form-select" id="durasi_tidur" name="durasi_tidur" required>
                    <option value="Kurang dari 5 jam">Kurang dari 5 jam (Sering Begadang)</option>
                    <option value="5-6 jam">5 - 6 jam</option>
                    <option value="7-8 jam" selected>7 - 8 jam (Cukup & Ideal)</option>
                    <option value="Lebih dari 8 jam">Lebih dari 8 jam</option>
                  </select>
                </div>

                <div class="col-md-6">
                  <label for="pola_makan" class="form-label">Kualitas & Keteraturan Pola Makan <span class="text-danger">*</span></label>
                  <select class="form-select" id="pola_makan" name="pola_makan" required>
                    <option value="Sehat" selected>Sehat & Teratur (Gizi Seimbang)</option>
                    <option value="Cukup/Sedang">Cukup / Sedang</option>
                    <option value="Tidak Sehat">Tidak Sehat / Sering Terlewat</option>
                  </select>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Apakah Ada Riwayat Masalah Kesehatan Mental di Keluarga? <span class="text-danger">*</span></label>
                  <div class="d-flex gap-4 pt-1">
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="riwayat_keluarga" id="rkTidak" value="Tidak" checked>
                      <label class="form-check-label" for="rkTidak">Tidak Ada</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="riwayat_keluarga" id="rkYa" value="Ya">
                      <label class="form-check-label" for="rkYa">Ada</label>
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Pernahkah Muncul Pikiran untuk Menyakiti Diri / Putus Asa? <span class="text-danger">*</span></label>
                  <div class="d-flex gap-4 pt-1">
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="pikiran_menyakiti_diri" id="pmdTidak" value="Tidak" checked>
                      <label class="form-check-label text-success fw-semibold" for="pmdTidak">Tidak Pernah</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="pikiran_menyakiti_diri" id="pmdYa" value="Ya">
                      <label class="form-check-label text-danger fw-semibold" for="pmdYa">Pernah / Sering</label>
                    </div>
                  </div>
                </div>

              </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-3 border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
              <div class="text-muted small">
                <i class="bi bi-info-circle me-1 text-primary"></i> Data sesi akan disimpan ke dalam pola Master-Detail dan diolah oleh Random Forest model.
              </div>
              <button type="submit" class="btn btn-primary btn-lg px-4 rounded-pill shadow-sm d-flex align-items-center gap-2">
                <span>Proses Analisis Skrining</span>
                <i class="bi bi-arrow-right-circle-fill"></i>
              </button>
            </div>

          </form>
        </div>
      </div>

    </div>
  </div>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
