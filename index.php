<?php
session_start();
require_once 'config.php';

// Query untuk mendapatkan tempat populer
$sql = "SELECT * FROM tempat ORDER BY dibuat_pada DESC LIMIT 6";
$stmt = $koneksi->prepare($sql);
$stmt->execute();
$hasil = $stmt->get_result();

$tempat_populer = array();
while($baris = $hasil->fetch_assoc()) {
    $tempat_populer[] = $baris;
}

// Kategori
$kategori_list = array(
    array('nama' => 'Museum', 'ikon' => '🏛️'),
    array('nama' => 'Candi', 'ikon' => '⛩️'),
    array('nama' => 'Keraton', 'ikon' => '👑'),
    array('nama' => 'Benteng', 'ikon' => '🏰'),
    array('nama' => 'Monumen', 'ikon' => '🗿'),
    array('nama' => 'Religious Heritage', 'ikon' => '🕌')
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jogja Historia - Jelajahi Warisan Budaya Yogyakarta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="pola-wayang">
    <?php include 'includes/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <h1>Temukan Warisan Budaya Yogyakarta.</h1>
                    <p class="lead">Jelajahi museum, candi, keraton, dan situs bersejarah yang mempesona di Kota Istimewa.</p>
                    <div class="mt-4">
                        <a href="jelajahi.php" class="btn btn-traditional btn-lg me-3">
                            <i class="bi bi-compass"></i> Mulai Menjelajah
                        </a>
                        <a href="peta.php" class="btn btn-outline-light btn-lg">
                            <i class="bi bi-map"></i> Lihat Peta
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Search Section -->
    <section class="py-4" style="margin-top: -10px; position: relative; z-index: 10;">
        <div class="container">
            <div class="kotak-pencarian d-flex">
                <input type="text" class="form-control" placeholder="Cari museum, candi, atau tempat bersejarah..." id="inputPencarian">
                <button class="btn" onclick="cariTempat()">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>
    </section>

    <!-- Categories -->
    <section class="py-5">
        <div class="container">
            <h2 class="judul-bagian">Kategori Warisan</h2>
            <div class="row g-4">
                <?php foreach($kategori_list as $ktg): ?>
                <div class="col-md-4 col-lg-2">
                    <a href="jelajahi.php?kategori=<?php echo urlencode($ktg['nama']); ?>" class="text-decoration-none">
                        <div class="kartu-kategori">
                            <div class="ikon-kategori"><?php echo $ktg['ikon']; ?></div>
                            <h5 style="color: var(--primary-brown);"><?php echo $ktg['nama']; ?></h5>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Featured Places -->
    <section class="py-5 bg-white">
        <div class="container">
            <h2 class="judul-bagian">Tempat Populer</h2>
            <div class="row g-4">
                <?php if(count($tempat_populer) > 0): ?>
                    <?php foreach($tempat_populer as $tempat): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="kartu-tempat">
                            <?php 
                            $gambar_json = $tempat['gambar'];
                            $gambar_array = json_decode($gambar_json, true);
                            $gambar_url = 'https://via.placeholder.com/400x200?text=No+Image';
                            if($gambar_array && count($gambar_array) > 0) {
                                $gambar_url = $gambar_array[0];
                            }
                            ?>
                            <img src="<?php echo htmlspecialchars($gambar_url); ?>" alt="<?php echo htmlspecialchars($tempat['judul']); ?>">
                            <div class="isi-kartu-tempat">
                                <span class="kategori-tempat"><?php echo htmlspecialchars($tempat['kategori']); ?></span>
                                <h4 style="color: var(--primary-brown);"><?php echo htmlspecialchars($tempat['judul']); ?></h4>
                                <p class="text-muted mb-3">
                                    <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($tempat['alamat']); ?>
                                </p>
                                <p><?php echo substr(htmlspecialchars($tempat['deskripsi_singkat']), 0, 100); ?>...</p>
                                <a href="detail_tempat.php?slug=<?php echo $tempat['slug']; ?>" class="btn btn-traditional btn-sm w-100">
                                    <i class="bi bi-arrow-right"></i> Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p class="lead">Belum ada tempat yang ditambahkan. Silakan cek halaman <a href="instalasi.php">Instalasi Database</a>.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="py-5">
        <div class="container">
            <h2 class="judul-bagian">Fitur Unggulan</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="text-center p-4">
                        <div class="mb-3" style="font-size: 3rem; color: var(--gold);">
                            <i class="bi bi-map-fill"></i>
                        </div>
                        <h4 style="color: var(--primary-brown);">Peta Interaktif</h4>
                        <p>Temukan lokasi heritage dengan mudah menggunakan peta interaktif dengan filter kategori</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-4">
                        <div class="mb-3" style="font-size: 3rem; color: var(--gold);">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <h4 style="color: var(--primary-brown);">Rencanakan Kunjungan</h4>
                        <p>Buat itinerary perjalanan sesuai preferensi dan waktu yang Anda miliki</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-4">
                        <div class="mb-3" style="font-size: 3rem; color: var(--gold);">
                            <i class="bi bi-book"></i>
                        </div>
                        <h4 style="color: var(--primary-brown);">Konten Edukatif</h4>
                        <p>Pelajari sejarah dan budaya melalui artikel, timeline, dan quiz interaktif</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function cariTempat() {
            var query = document.getElementById('inputPencarian').value;
            if(query.trim()) {
                window.location.href = 'jelajahi.php?q=' + encodeURIComponent(query);
            }
        }

        document.getElementById('inputPencarian').addEventListener('keypress', function(e) {
            if(e.key === 'Enter') {
                cariTempat();
            }
        });
    </script>
</body>
</html>
<?php
mysqli_close($koneksi);
?>