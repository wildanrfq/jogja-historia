<?php
session_start();
require_once '../config.php';
require_once '../includes/proteksi_session.php';

// Handle delete
if(isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $sql = "DELETE FROM acara WHERE id = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if($stmt->execute()) {
        header('Location: kelola_acara.php?pesan=dihapus');
        exit();
    }
}

// Handle add event
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_acara'])) {
    $id_tempat = intval($_POST['id_tempat']);
    $judul = bersihkan_input($_POST['judul']);
    $tanggal_mulai = bersihkan_input($_POST['tanggal_mulai']);
    $tanggal_selesai = bersihkan_input($_POST['tanggal_selesai']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $link_tiket = bersihkan_input($_POST['link_tiket']);
    
    $sql = "INSERT INTO acara (id_tempat, judul, tanggal_mulai, tanggal_selesai, deskripsi, link_tiket) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("isssss", $id_tempat, $judul, $tanggal_mulai, $tanggal_selesai, $deskripsi, $link_tiket);
    
    if($stmt->execute()) {
        header('Location: kelola_acara.php?pesan=ditambah');
        exit();
    }
}

// Get all events
$sql = "SELECT a.*, t.judul as judul_tempat, t.kategori 
        FROM acara a 
        LEFT JOIN tempat t ON a.id_tempat = t.id 
        ORDER BY a.tanggal_mulai DESC";
$stmt = $koneksi->prepare($sql);
$stmt->execute();
$hasil = $stmt->get_result();
$daftar_acara = array();
while($baris = $hasil->fetch_assoc()) {
    $daftar_acara[] = $baris;
}

// Get all places for dropdown
$sql = "SELECT id, judul, kategori FROM tempat ORDER BY judul";
$stmt = $koneksi->prepare($sql);
$stmt->execute();
$hasil = $stmt->get_result();
$daftar_tempat = array();
while($baris = $hasil->fetch_assoc()) {
    $daftar_tempat[] = $baris;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Event - Admin Jogja Historia</title>
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
                    <a href="dashboard.php" class="link-nav-admin">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <a href="kelola_tempat.php" class="link-nav-admin">
                        <i class="bi bi-geo-alt"></i> Kelola Tempat
                    </a>
                    <a href="kelola_acara.php" class="link-nav-admin active">
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

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 px-4 py-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="teks-coklat">Kelola Event</h2>
                        <p class="text-muted">Daftar semua event yang dijadwalkan</p>
                    </div>
                    <button class="btn btn-traditional" data-bs-toggle="modal" data-bs-target="#tambahAcaraModal">
                        <i class="bi bi-plus-circle"></i> Tambah Event Baru
                    </button>
                </div>

                <?php if(isset($_GET['pesan'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?php 
                    if($_GET['pesan'] == 'ditambah') echo 'Event berhasil ditambahkan!';
                    if($_GET['pesan'] == 'dihapus') echo 'Event berhasil dihapus!';
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <div class="card kartu-tradisional">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nama Event</th>
                                        <th>Lokasi</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Tanggal Selesai</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(count($daftar_acara) > 0): ?>
                                        <?php foreach($daftar_acara as $acara): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($acara['judul']); ?></strong><br>
                                                <small class="text-muted">
                                                    <?php echo substr(htmlspecialchars($acara['deskripsi']), 0, 60); ?>...
                                                </small>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($acara['judul_tempat']); ?><br>
                                                <span class="badge bg-coklat small">
                                                    <?php echo htmlspecialchars($acara['kategori']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d M Y', strtotime($acara['tanggal_mulai'])); ?></td>
                                            <td><?php echo date('d M Y', strtotime($acara['tanggal_selesai'])); ?></td>
                                            <td>
                                                <?php 
                                                $hari_ini = date('Y-m-d');
                                                if($acara['tanggal_selesai'] < $hari_ini) {
                                                    echo '<span class="badge bg-secondary">Selesai</span>';
                                                } elseif($acara['tanggal_mulai'] <= $hari_ini && $acara['tanggal_selesai'] >= $hari_ini) {
                                                    echo '<span class="badge bg-success">Berlangsung</span>';
                                                } else {
                                                    echo '<span class="badge bg-info">Akan Datang</span>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <a href="kelola_acara.php?hapus=<?php echo $acara['id']; ?>" 
                                                   class="btn btn-sm btn-outline-danger"
                                                   onclick="return confirm('Yakin ingin menghapus event ini?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <i class="bi bi-calendar-x" style="font-size: 3rem; opacity: 0.3;"></i>
                                                <p class="text-muted mt-2">Belum ada event yang ditambahkan</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Event -->
    <div class="modal fade" id="tambahAcaraModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: var(--primary-brown); color: white;">
                    <h5 class="modal-title">
                        <i class="bi bi-plus-circle"></i> Tambah Event Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label teks-coklat">
                                    <strong>Nama Event *</strong>
                                </label>
                                <input type="text" name="judul" class="form-control" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label teks-coklat">
                                    <strong>Lokasi/Tempat *</strong>
                                </label>
                                <select name="id_tempat" class="form-select" required>
                                    <option value="">Pilih Tempat</option>
                                    <?php foreach($daftar_tempat as $tempat): ?>
                                    <option value="<?php echo $tempat['id']; ?>">
                                        <?php echo htmlspecialchars($tempat['judul']); ?> (<?php echo $tempat['kategori']; ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label teks-coklat">
                                    <strong>Tanggal Mulai *</strong>
                                </label>
                                <input type="date" name="tanggal_mulai" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label teks-coklat">
                                    <strong>Tanggal Selesai *</strong>
                                </label>
                                <input type="date" name="tanggal_selesai" class="form-control" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label teks-coklat">
                                    <strong>Deskripsi</strong>
                                </label>
                                <textarea name="deskripsi" class="form-control" rows="4"></textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label teks-coklat">
                                    <strong>Link Tiket (opsional)</strong>
                                </label>
                                <input type="url" name="link_tiket" class="form-control" placeholder="https://...">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" name="tambah_acara" class="btn btn-traditional">
                            <i class="bi bi-save"></i> Simpan Event
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($koneksi); ?>