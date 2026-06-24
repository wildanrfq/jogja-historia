<?php
session_start();
require_once 'config.php';

// Get search and filter parameters
$pencarian = isset($_GET['q']) ? bersihkan_input($_GET['q']) : '';
$kategori = isset($_GET['kategori']) ? bersihkan_input($_GET['kategori']) : '';
$urutan = isset($_GET['urutan']) ? bersihkan_input($_GET['urutan']) : 'terbaru';

// Build query
$sql = "SELECT * FROM tempat WHERE 1=1";
$params = array();
$types = "";

if($pencarian) {
    $sql .= " AND (judul LIKE ? OR deskripsi_singkat LIKE ? OR alamat LIKE ?)";
    $kata_kunci = "%$pencarian%";
    $params[] = $kata_kunci;
    $params[] = $kata_kunci;
    $params[] = $kata_kunci;
    $types .= "sss";
}

if($kategori) {
    $sql .= " AND kategori = ?";
    $params[] = $kategori;
    $types .= "s";
}

// Sorting
switch($urutan) {
    case 'nama':
        $sql .= " ORDER BY judul ASC";
        break;
    case 'kategori':
        $sql .= " ORDER BY kategori ASC, judul ASC";
        break;
    default:
        $sql .= " ORDER BY dibuat_pada DESC";
}

$stmt = $koneksi->prepare($sql);
if(count($params) > 0) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$hasil = $stmt->get_result();

$daftar_tempat = array();
while($baris = $hasil->fetch_assoc()) {
    $daftar_tempat[] = $baris;
}

// Get all categories
$sql = "SELECT DISTINCT kategori FROM tempat ORDER BY kategori";
$stmt = $koneksi->prepare($sql);
$stmt->execute();
$hasil = $stmt->get_result();
$daftar_kategori = array();
while($baris = $hasil->fetch_assoc()) {
    $daftar_kategori[] = $baris['kategori'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jelajahi Tempat - Jogja Historia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="pola-wayang">
    <?php include 'includes/navbar.php'; ?>

    <!-- Page Header -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12 mt-5">
                    <h1>Jelajahi Tempat Bersejarah.</h1>
                    <p class="lead">Temukan museum, candi, keraton, dan warisan budaya Yogyakarta</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Filter Section -->
    <section class="py-4 border-bottom">
        <div class="container">
            <form method="GET" action="jelajahi.php" class="row g-3">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-coklat text-white">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control" name="q" 
                               placeholder="Cari tempat..." 
                               value="<?php echo htmlspecialchars($pencarian); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="kategori">
                        <option value="">Semua Kategori</option>
                        <?php foreach($daftar_kategori as $kat): ?>
                        <option value="<?php echo htmlspecialchars($kat); ?>" 
                                <?php echo $kategori == $kat ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($kat); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="urutan">
                        <option value="terbaru" <?php echo $urutan == 'terbaru' ? 'selected' : ''; ?>>Terbaru</option>
                        <option value="nama" <?php echo $urutan == 'nama' ? 'selected' : ''; ?>>Nama A-Z</option>
                        <option value="kategori" <?php echo $urutan == 'kategori' ? 'selected' : ''; ?>>Kategori</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-traditional w-100">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- Results -->
    <section class="py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="teks-coklat">
                    <?php if($pencarian || $kategori): ?>
                        Hasil Pencarian (<?php echo count($daftar_tempat); ?> tempat)
                    <?php else: ?>
                        Semua Tempat (<?php echo count($daftar_tempat); ?> tempat)
                    <?php endif; ?>
                </h4>
                <a href="jelajahi.php" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-circle"></i> Reset Filter
                </a>
            </div>

            <?php if(count($daftar_tempat) > 0): ?>
            <div class="row g-4">
                <?php foreach($daftar_tempat as $tempat): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="kartu-tempat">
                        <?php 
                        $gambar = json_decode($tempat['gambar'], true);
                        $url_gambar = 'https://via.placeholder.com/400x200?text=No+Image';
                        if($gambar && count($gambar) > 0) {
                            $url_gambar = $gambar[0];
                        }
                        ?>
                        <img src="<?php echo htmlspecialchars($url_gambar); ?>" 
                             alt="<?php echo htmlspecialchars($tempat['judul']); ?>">
                        <div class="isi-kartu-tempat">
                            <span class="kategori-tempat">
                                <?php echo htmlspecialchars($tempat['kategori']); ?>
                            </span>
                            <h4 class="teks-coklat mt-2">
                                <?php echo htmlspecialchars($tempat['judul']); ?>
                            </h4>
                            <p class="text-muted mb-2">
                                <i class="bi bi-geo-alt-fill"></i> 
                                <?php echo htmlspecialchars($tempat['alamat']); ?>
                            </p>
                            <?php if($tempat['jam_buka']): ?>
                            <p class="text-muted mb-2">
                                <i class="bi bi-clock"></i> 
                                <?php echo htmlspecialchars($tempat['jam_buka']); ?>
                            </p>
                            <?php endif; ?>
                            <p class="small">
                                <?php echo substr(htmlspecialchars($tempat['deskripsi_singkat']), 0, 100); ?>...
                            </p>
                            <div class="d-flex gap-2 mt-3">
                                <a href="detail_tempat.php?slug=<?php echo $tempat['slug']; ?>" 
                                   class="btn btn-traditional btn-sm flex-grow-1">
                                    <i class="bi bi-info-circle"></i> Detail
                                </a>
                                <a href="peta.php?tempat=<?php echo $tempat['id']; ?>" 
                                   class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-map"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <div style="font-size: 5rem; opacity: 0.3;">🔍</div>
                <h4 class="text-muted">Tidak ada tempat yang ditemukan</h4>
                <p>Coba ubah kata kunci atau filter pencarian Anda</p>
                <a href="jelajahi.php" class="btn btn-traditional mt-3">
                    Lihat Semua Tempat
                </a>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($koneksi); ?>