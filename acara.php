<?php
session_start();
require_once 'config.php';

// Get upcoming events
$sql = "SELECT a.*, t.judul as judul_tempat, t.slug as slug_tempat, t.kategori, t.gambar
        FROM acara a
        LEFT JOIN tempat t ON a.id_tempat = t.id
        WHERE a.tanggal_selesai >= CURDATE()
        ORDER BY a.tanggal_mulai ASC";
$stmt = $koneksi->prepare($sql);
$stmt->execute();
$hasil = $stmt->get_result();
$daftar_acara = array();
while($baris = $hasil->fetch_assoc()) {
    $daftar_acara[] = $baris;
}

// Group by month
$acara_per_bulan = array();
foreach($daftar_acara as $acara) {
    $bulan = date('F Y', strtotime($acara['tanggal_mulai']));
    if(!isset($acara_per_bulan[$bulan])) {
        $acara_per_bulan[$bulan] = array();
    }
    $acara_per_bulan[$bulan][] = $acara;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event & Kalender - Jogja Historia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .kartu-acara {
            background: white;
            border: 3px solid var(--light-brown);
            border-left: 6px solid var(--gold);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        
        .kartu-acara:hover {
            box-shadow: 0 8px 25px rgba(107, 68, 35, 0.2);
            transform: translateX(5px);
        }
        
        .badge-tanggal {
            background: linear-gradient(135deg, var(--gold), var(--deep-gold));
            color: var(--dark-brown);
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            font-weight: bold;
        }
        
        .header-bulan {
            background: linear-gradient(135deg, var(--primary-brown), var(--secondary-brown));
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 6px solid var(--gold);
        }
    </style>
</head>
<body class="pola-wayang">
    <?php include 'includes/navbar.php'; ?>

    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12 mt-5">
                    <h1>Kunjungi Acara Yang Akan Dilaksanakan.</h1>
                    <p class="lead">Jangan lewatkan acara menarik di tempat-tempat bersejarah Yogyakarta.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <?php if(count($daftar_acara) > 0): ?>
                <?php foreach($acara_per_bulan as $bulan => $acara_bulan): ?>
                <div class="header-bulan">
                    <h3 class="mb-0">
                        <i class="bi bi-calendar-month"></i> <?php echo $bulan; ?>
                    </h3>
                </div>

                <div class="row g-4 mb-5">
                    <?php foreach($acara_bulan as $acara): ?>
                    <div class="col-12">
                        <div class="kartu-acara">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    <div class="badge-tanggal">
                                        <div style="font-size: 2rem;">
                                            <?php echo date('d', strtotime($acara['tanggal_mulai'])); ?>
                                        </div>
                                        <div><?php echo strtoupper(date('M', strtotime($acara['tanggal_mulai']))); ?></div>
                                    </div>
                                </div>
                                
                                <div class="col-md-7">
                                    <span class="badge bg-coklat mb-2">
                                        <?php echo htmlspecialchars($acara['kategori']); ?>
                                    </span>
                                    <h4 class="teks-coklat mb-2">
                                        <?php echo htmlspecialchars($acara['judul']); ?>
                                    </h4>
                                    <p class="text-muted mb-2">
                                        <i class="bi bi-geo-alt-fill"></i> 
                                        <strong><?php echo htmlspecialchars($acara['judul_tempat']); ?></strong>
                                    </p>
                                    <p class="mb-2">
                                        <?php echo htmlspecialchars($acara['deskripsi']); ?>
                                    </p>
                                    <p class="text-muted mb-0">
                                        <i class="bi bi-calendar"></i> 
                                        <?php 
                                        $mulai = date('d M Y', strtotime($acara['tanggal_mulai']));
                                        $selesai = date('d M Y', strtotime($acara['tanggal_selesai']));
                                        echo $mulai;
                                        if($mulai != $selesai) {
                                            echo ' - ' . $selesai;
                                        }
                                        ?>
                                    </p>
                                </div>
                                
                                <div class="col-md-3 text-end">
                                    <a href="detail_tempat.php?slug=<?php echo $acara['slug_tempat']; ?>" 
                                       class="btn btn-traditional mb-2 w-100">
                                        <i class="bi bi-info-circle"></i> Info Lokasi
                                    </a>
                                    <?php if($acara['link_tiket']): ?>
                                    <a href="<?php echo htmlspecialchars($acara['link_tiket']); ?>" 
                                       target="_blank" class="btn btn-outline-secondary w-100">
                                        <i class="bi bi-ticket-perforated"></i> Beli Tiket
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <div style="font-size: 5rem; opacity: 0.3;">📅</div>
                    <h4 class="text-muted mt-3">Belum ada event yang dijadwalkan</h4>
                    <p>Silakan cek kembali nanti untuk informasi event terbaru</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Featured Banner -->
    <section class="py-5 bg-white">
        <div class="container">
            <h2 class="judul-bagian">Temukan Lebih Banyak Event</h2>
            <div class="text-center">
                <p class="lead mb-4">
                    Ikuti kami di media sosial untuk mendapatkan update event terbaru
                </p>
                <div>
                    <a href="#" class="btn btn-traditional me-2">
                        <i class="bi bi-instagram"></i> Instagram
                    </a>
                    <a href="#" class="btn btn-traditional me-2">
                        <i class="bi bi-facebook"></i> Facebook
                    </a>
                    <a href="#" class="btn btn-traditional">
                        <i class="bi bi-envelope"></i> Newsletter
                    </a>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($koneksi); ?>