<?php
session_start();
require_once 'config.php';

$slug = isset($_GET['slug']) ? bersihkan_input($_GET['slug']) : '';

if(!$slug) {
    header('Location: jelajahi.php');
    exit();
}

$sql = "SELECT * FROM tempat WHERE slug = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("s", $slug);
$stmt->execute();
$hasil = $stmt->get_result();

if($hasil->num_rows == 0) {
    header('Location: jelajahi.php');
    exit();
}

$tempat = $hasil->fetch_assoc();

// Get related places
$sql = "SELECT * FROM tempat WHERE kategori = ? AND id != ? LIMIT 3";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("si", $tempat['kategori'], $tempat['id']);
$stmt->execute();
$hasil = $stmt->get_result();
$tempat_terkait = array();
while($baris = $hasil->fetch_assoc()) {
    $tempat_terkait[] = $baris;
}

// Get events
$sql = "SELECT * FROM acara WHERE id_tempat = ? AND tanggal_selesai >= CURDATE() ORDER BY tanggal_mulai ASC";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $tempat['id']);
$stmt->execute();
$hasil = $stmt->get_result();
$daftar_acara = array();
while($baris = $hasil->fetch_assoc()) {
    $daftar_acara[] = $baris;
}

$gambar = json_decode($tempat['gambar'], true);
if(!$gambar) $gambar = array();

$info_tiket = json_decode($tempat['info_tiket'], true);
if(!$info_tiket) $info_tiket = array();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($tempat['judul']); ?> - Jogja Historia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        #peta { height: 400px; border-radius: 10px; border: 3px solid var(--light-brown); }
        .gambar-galeri { 
            cursor: pointer; 
            transition: all 0.3s; 
            border: 3px solid var(--light-brown); 
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
        }
        .gambar-galeri:hover { t∏ransform: scale(1.05); border-color: var(--gold); }
    </style>
</head>
<body class="pola-wayang">
    <?php include 'includes/navbar.php'; ?>

    <!-- Header -->
    <div class="page-header" style="background: linear-gradient(rgba(107, 68, 35, 0.7), rgba(62, 39, 35, 0.7)), url('<?php echo isset($gambar[0]) ? $gambar[0] : ''; ?>'); background-size: cover; background-position: center;">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <span class="badge bg-emas text-dark fs-6 mb-3">
                        <?php echo htmlspecialchars($tempat['kategori']); ?>
                    </span>
                    <h1 class="display-3 text-white">
                        <?php echo htmlspecialchars($tempat['judul']); ?>
                    </h1>
                    <p class="lead text-white">
                        <i class="bi bi-geo-alt-fill"></i> 
                        <?php echo htmlspecialchars($tempat['alamat']); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <!-- Left Column -->
                <div class="col-lg-8">
                    <!-- Gallery -->
                    <?php if(count($gambar) > 0): ?>
                    <div class="card kartu-tradisional mb-4">
                        <div class="card-body">
                            <h3 class="teks-coklat mb-3">
                                <i class="bi bi-images"></i> Galeri Foto
                            </h3>
                            <div class="row g-3">
                                <?php foreach($gambar as $url_gambar): ?>
                                <div class="col-md-6">
                                    <img src="<?php echo htmlspecialchars($url_gambar); ?>" 
                                         class="img-fluid gambar-galeri rounded" 
                                         alt="<?php echo htmlspecialchars($tempat['judul']); ?>"
                                         onclick="bukaModal('<?php echo htmlspecialchars($url_gambar); ?>')">
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Description -->
                    <div class="card kartu-tradisional mb-4">
                        <div class="card-body">
                            <h3 class="teks-coklat mb-3">
                                <i class="bi bi-book"></i> Tentang Tempat Ini
                            </h3>
                            <p class="lead text-muted"><?php echo htmlspecialchars($tempat['deskripsi_singkat']); ?></p>
                            <hr class="my-4" style="border-color: var(--light-brown);">
                            <div style="text-align: justify; line-height: 1.8;">
                                <?php echo nl2br(htmlspecialchars($tempat['deskripsi_lengkap'])); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Events -->
                    <?php if(count($daftar_acara) > 0): ?>
                    <div class="card kartu-tradisional mb-4">
                        <div class="card-body">
                            <h3 class="teks-coklat mb-3">
                                <i class="bi bi-calendar-event"></i> Event Mendatang
                            </h3>
                            <?php foreach($daftar_acara as $acara): ?>
                            <div class="border-start border-5 border-emas ps-3 mb-3">
                                <h5 class="teks-coklat"><?php echo htmlspecialchars($acara['judul']); ?></h5>
                                <p class="text-muted mb-1">
                                    <i class="bi bi-calendar"></i> 
                                    <?php echo date('d M Y', strtotime($acara['tanggal_mulai'])); ?>
                                    <?php if($acara['tanggal_selesai'] != $acara['tanggal_mulai']): ?>
                                    - <?php echo date('d M Y', strtotime($acara['tanggal_selesai'])); ?>
                                    <?php endif; ?>
                                </p>
                                <p><?php echo htmlspecialchars($acara['deskripsi']); ?></p>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Map -->
                    <div class="card kartu-tradisional">
                        <div class="card-body">
                            <h3 class="teks-coklat mb-3">
                                <i class="bi bi-map"></i> Lokasi
                            </h3>
                            <div id="peta"></div>
                            <div class="mt-3">
                                <a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo $tempat['latitude']; ?>,<?php echo $tempat['longitude']; ?>" 
                                   target="_blank" class="btn btn-traditional">
                                    <i class="bi bi-compass"></i> Petunjuk Arah
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-lg-4">
                    <!-- Info -->
                    <div class="card kartu-tradisional mb-4">
                        <div class="card-body">
                            <h4 class="teks-coklat mb-3">
                                <i class="bi bi-info-circle"></i> Informasi
                            </h4>
                            
                            <?php if($tempat['jam_buka']): ?>
                            <div class="mb-3">
                                <h6 class="teks-coklat"><i class="bi bi-clock"></i> Jam Buka</h6>
                                <p class="mb-0"><?php echo htmlspecialchars($tempat['jam_buka']); ?></p>
                            </div>
                            <?php endif; ?>

                            <?php if(!empty($info_tiket)): ?>
                            <div class="mb-3">
                                <h6 class="teks-coklat"><i class="bi bi-ticket-perforated"></i> Harga Tiket</h6>
                                <?php foreach($info_tiket as $key => $value): ?>
                                    <?php if($key != 'catatan'): ?>
                                    <p class="mb-1">
                                        <strong><?php echo ucfirst($key); ?>:</strong> 
                                        Rp <?php echo number_format($value, 0, ',', '.'); ?>
                                    </p>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <?php if(isset($info_tiket['catatan'])): ?>
                                <p class="small text-muted mb-0">
                                    <i class="bi bi-info-circle"></i> <?php echo htmlspecialchars($info_tiket['catatan']); ?>
                                </p>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>

                            <?php if($tempat['kontak']): ?>
                            <div class="mb-3">
                                <h6 class="teks-coklat"><i class="bi bi-telephone"></i> Kontak</h6>
                                <p class="mb-0"><?php echo htmlspecialchars($tempat['kontak']); ?></p>
                            </div>
                            <?php endif; ?>

                            <?php if($tempat['website']): ?>
                            <div class="mb-3">
                                <h6 class="teks-coklat"><i class="bi bi-globe"></i> Website</h6>
                                <a href="<?php echo htmlspecialchars($tempat['website']); ?>" target="_blank">
                                    Kunjungi Website
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="card kartu-tradisional mb-4">
                        <div class="card-body">
                            <h6 class="teks-coklat mb-3">Aksi</h6>
                            <div class="d-grid gap-2">
                                <button class="btn btn-traditional" onclick="tambahKeItinerary(<?php echo $tempat['id']; ?>)">
                                    <i class="bi bi-plus-circle"></i> Tambah ke Itinerary
                                </button>
                                <button class="btn btn-outline-secondary" onclick="bagikanTempat()">
                                    <i class="bi bi-share"></i> Bagikan
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Related -->
                    <?php if(count($tempat_terkait) > 0): ?>
                    <div class="card kartu-tradisional">
                        <div class="card-body">
                            <h6 class="teks-coklat mb-3">Tempat Serupa</h6>
                            <?php foreach($tempat_terkait as $terkait): ?>
                            <div class="mb-3 border-bottom pb-3">
                                <?php 
                                $gambar_terkait = json_decode($terkait['gambar'], true);
                                $url_gambar_terkait = isset($gambar_terkait[0]) ? $gambar_terkait[0] : 'https://via.placeholder.com/200';
                                ?>
                                <img src="<?php echo $url_gambar_terkait; ?>" 
                                     class="img-fluid rounded mb-2" 
                                     alt="<?php echo htmlspecialchars($terkait['judul']); ?>">
                                <h6 class="teks-coklat mb-1">
                                    <a href="detail_tempat.php?slug=<?php echo $terkait['slug']; ?>" class="text-decoration-none">
                                        <?php echo htmlspecialchars($terkait['judul']); ?>
                                    </a>
                                </h6>
                                <small class="text-muted">
                                    <?php echo substr(htmlspecialchars($terkait['deskripsi_singkat']), 0, 60); ?>...
                                </small>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal -->
    <div class="modal fade" id="modalGambar" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <button type="button" 
                        class="btn-close position-absolute top-0 end-0 m-3 d-flex justify-content-center align-items-center"
                        data-bs-dismiss="modal"
                        style="z-index: 1050; background: white; opacity: 1; width: 35px; height: 35px; border-radius: 50%;">
                        <i class="bi bi-x-lg" style="color: #333; font-size: 1.5rem;"></i>
                    </button>
                    <img id="gambarModal" src="" class="img-fluid w-100 rounded">
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Map
        const peta = L.map('peta').setView([<?php echo $tempat['latitude']; ?>, <?php echo $tempat['longitude']; ?>], 15);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(peta);
        
        const marker = L.marker([<?php echo $tempat['latitude']; ?>, <?php echo $tempat['longitude']; ?>]).addTo(peta);
        marker.bindPopup("<b><?php echo htmlspecialchars($tempat['judul']); ?></b><br><?php echo htmlspecialchars($tempat['alamat']); ?>").openPopup();

        function bukaModal(src) {
            document.getElementById('gambarModal').src = src;
            new bootstrap.Modal(document.getElementById('modalGambar')).show();
        }

        function tambahKeItinerary(idTempat) {
            let itinerary = JSON.parse(localStorage.getItem('itinerary') || '[]');
            if(!itinerary.includes(idTempat)) {
                itinerary.push(idTempat);
                localStorage.setItem('itinerary', JSON.stringify(itinerary));
                alert('Berhasil ditambahkan ke itinerary!');
            } else {
                alert('Tempat ini sudah ada di itinerary Anda!');
            }
        }

        function bagikanTempat() {
            if (navigator.share) {
                navigator.share({
                    title: '<?php echo htmlspecialchars($tempat['judul']); ?>',
                    text: '<?php echo htmlspecialchars($tempat['deskripsi_singkat']); ?>',
                    url: window.location.href
                });
            } else {
                navigator.clipboard.writeText(window.location.href);
                alert('Link berhasil disalin!');
            }
        }
    </script>
</body>
</html>
<?php mysqli_close($koneksi); ?>