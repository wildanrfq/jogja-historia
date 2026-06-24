<?php
session_start();
require_once '../config.php';
require_once '../includes/proteksi_session.php';

// Dapatkan statistik
$sql = "SELECT COUNT(*) as jumlah FROM tempat";
$stmt = $koneksi->prepare($sql);
$stmt->execute();
$hasil = $stmt->get_result();
$total_tempat = $hasil->fetch_assoc()['jumlah'];

$sql = "SELECT COUNT(*) as jumlah FROM acara";
$stmt = $koneksi->prepare($sql);
$stmt->execute();
$hasil = $stmt->get_result();
$total_acara = $hasil->fetch_assoc()['jumlah'];

$sql = "SELECT COUNT(*) as jumlah FROM rencana_perjalanan";
$stmt = $koneksi->prepare($sql);
$stmt->execute();
$hasil = $stmt->get_result();
$total_rencana = $hasil->fetch_assoc()['jumlah'];

$sql = "SELECT COUNT(*) as jumlah FROM pengguna";
$stmt = $koneksi->prepare($sql);
$stmt->execute();
$hasil = $stmt->get_result();
$total_pengguna = $hasil->fetch_assoc()['jumlah'];

// Dapatkan tempat terbaru
$sql = "SELECT * FROM tempat ORDER BY dibuat_pada DESC LIMIT 5";
$stmt = $koneksi->prepare($sql);
$stmt->execute();
$hasil = $stmt->get_result();
$tempat_terbaru = array();
while($baris = $hasil->fetch_assoc()) {
    $tempat_terbaru[] = $baris;
}

// Dapatkan acara mendatang
$sql = "SELECT a.*, t.judul as judul_tempat FROM acara a 
        LEFT JOIN tempat t ON a.id_tempat = t.id 
        WHERE a.tanggal_mulai >= CURDATE() 
        ORDER BY a.tanggal_mulai ASC LIMIT 5";
$stmt = $koneksi->prepare($sql);
$stmt->execute();
$hasil = $stmt->get_result();
$acara_mendatang = array();
while($baris = $hasil->fetch_assoc()) {
    $acara_mendatang[] = $baris;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Jogja Historia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        .sidebar-admin {
            background: linear-gradient(135deg, var(--primary-brown), var(--secondary-brown));
            min-height: 100vh;
            color: white;
            padding: 20px;
        }
        .link-nav-admin {
            color: var(--cream);
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 5px;
            display: block;
            text-decoration: none;
            transition: all 0.3s;
        }
        .link-nav-admin:hover, .link-nav-admin.active {
            background: rgba(212, 175, 55, 0.3);
            color: white;
            padding-left: 25px;
        }
        .kartu-statistik {
            background: white;
            border: 3px solid var(--light-brown);
            border-left: 6px solid var(--gold);
            border-radius: 10px;
            padding: 25px;
            transition: all 0.3s;
        }
        .kartu-statistik:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(107, 68, 35, 0.2);
        }
        .ikon-statistik {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--gold), var(--deep-gold));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--dark-brown);
        }
    </style>
</head>
<body class="pola-wayang">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar-admin">
                <div class="text-center mb-4">
                    <h4 class="teks-emas">
                        <i class="bi bi-bank2"></i><br>
                        Panel Admin
                    </h4>
                    <hr style="border-color: var(--gold);">
                    <p class="small">
                        <i class="bi bi-person-circle"></i><br>
                        <?php echo htmlspecialchars($_SESSION['nama_user']); ?>
                    </p>
                </div>

                <nav>
                    <a href="dashboard.php" class="link-nav-admin active">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <a href="kelola_tempat.php" class="link-nav-admin">
                        <i class="bi bi-geo-alt"></i> Kelola Tempat
                    </a>
                    <a href="kelola_acara.php" class="link-nav-admin">
                        <i class="bi bi-calendar-event"></i> Kelola Event
                    </a>
                    <hr style="border-color: rgba(255,255,255,0.2);">
                    <a href="../index.php" class="link-nav-admin">
                        <i class="bi bi-house"></i> Lihat Website
                    </a>
                    <a href="../logout.php" class="link-nav-admin">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </nav>
            </div>

            <!-- Konten Utama -->
            <div class="col-md-9 col-lg-10 px-4 py-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="teks-coklat">Dashboard</h2>
                        <p class="text-muted">Selamat datang di panel admin Jogja Historia</p>
                    </div>
                    <div>
                        <span class="text-muted">
                            <i class="bi bi-calendar"></i> 
                            <?php echo date('d F Y'); ?>
                        </span>
                    </div>
                </div>

                <!-- Kartu Statistik -->
                <div class="row g-4 mb-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="kartu-statistik">
                            <div class="d-flex align-items-center">
                                <div class="ikon-statistik me-3">
                                    <i class="bi bi-geo-alt"></i>
                                </div>
                                <div>
                                    <h3 class="teks-coklat mb-0"><?php echo $total_tempat; ?></h3>
                                    <p class="text-muted mb-0 small">Total Tempat</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="kartu-statistik">
                            <div class="d-flex align-items-center">
                                <div class="ikon-statistik me-3">
                                    <i class="bi bi-calendar-event"></i>
                                </div>
                                <div>
                                    <h3 class="teks-coklat mb-0"><?php echo $total_acara; ?></h3>
                                    <p class="text-muted mb-0 small">Total Event</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="kartu-statistik">
                            <div class="d-flex align-items-center">
                                <div class="ikon-statistik me-3">
                                    <i class="bi bi-calendar-check"></i>
                                </div>
                                <div>
                                    <h3 class="teks-coklat mb-0"><?php echo $total_rencana; ?></h3>
                                    <p class="text-muted mb-0 small">Itinerary Dibuat</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="kartu-statistik">
                            <div class="d-flex align-items-center">
                                <div class="ikon-statistik me-3">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div>
                                    <h3 class="teks-coklat mb-0"><?php echo $total_pengguna; ?></h3>
                                    <p class="text-muted mb-0 small">Total User</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tempat Terbaru & Acara Mendatang -->
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="card kartu-tradisional">
                            <div class="card-body">
                                <h4 class="teks-coklat mb-3">
                                    <i class="bi bi-clock-history"></i> Tempat Terbaru
                                </h4>
                                <div class="list-group list-group-flush">
                                    <?php if(count($tempat_terbaru) > 0): ?>
                                        <?php foreach($tempat_terbaru as $tempat): ?>
                                        <div class="list-group-item border-0 border-bottom">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1 teks-coklat">
                                                        <?php echo htmlspecialchars($tempat['judul']); ?>
                                                    </h6>
                                                    <small class="text-muted">
                                                        <?php echo htmlspecialchars($tempat['kategori']); ?> • 
                                                        <?php echo date('d M Y', strtotime($tempat['dibuat_pada'])); ?>
                                                    </small>
                                                </div>
                                                <a href="edit-tempat.php?id=<?php echo $tempat['id']; ?>" 
                                                   class="btn btn-outline-secondary btn-sm">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="text-center py-4">
                                            <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                                            <p class="text-muted mt-2">Belum ada tempat</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="mt-3">
                                    <a href="kelola_tempat.php" class="btn btn-traditional w-100">
                                        <i class="bi bi-eye"></i> Lihat Semua Tempat
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card kartu-tradisional">
                            <div class="card-body">
                                <h4 class="teks-coklat mb-3">
                                    <i class="bi bi-calendar-check"></i> Event Mendatang
                                </h4>
                                <div class="list-group list-group-flush">
                                    <?php if(count($acara_mendatang) > 0): ?>
                                        <?php foreach($acara_mendatang as $acara): ?>
                                        <div class="list-group-item border-0 border-bottom">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1 teks-coklat">
                                                        <?php echo htmlspecialchars($acara['judul']); ?>
                                                    </h6>
                                                    <small class="text-muted">
                                                        <?php echo htmlspecialchars($acara['judul_tempat']); ?> • 
                                                        <?php echo date('d M Y', strtotime($acara['tanggal_mulai'])); ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="text-center py-4">
                                            <i class="bi bi-calendar-x" style="font-size: 3rem; opacity: 0.3;"></i>
                                            <p class="text-muted mt-2">Belum ada event mendatang</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="mt-3">
                                    <a href="kelola_acara.php" class="btn btn-traditional w-100">
                                        <i class="bi bi-plus-circle"></i> Tambah Event Baru
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Aksi Cepat -->
                <div class="row g-4 mt-2">
                    <div class="col-12">
                        <div class="card kartu-tradisional">
                            <div class="card-body">
                                <h4 class="teks-coklat mb-3">
                                    <i class="bi bi-lightning"></i> Aksi Cepat
                                </h4>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <a href="tambah_tempat.php" class="btn btn-outline-secondary w-100 py-3">
                                            <i class="bi bi-plus-circle fs-3 d-block mb-2"></i>
                                            Tambah Tempat
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="kelola_acara.php" class="btn btn-outline-secondary w-100 py-3">
                                            <i class="bi bi-calendar-plus fs-3 d-block mb-2"></i>
                                            Tambah Event
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="../jelajahi.php" class="btn btn-outline-secondary w-100 py-3">
                                            <i class="bi bi-eye fs-3 d-block mb-2"></i>
                                            Preview Website
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="../instalasi.php" class="btn btn-outline-secondary w-100 py-3">
                                            <i class="bi bi-gear fs-3 d-block mb-2"></i>
                                            Instalasi Ulang
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($koneksi); ?>