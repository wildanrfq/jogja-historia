<?php
session_start();
require_once '../config.php';
require_once '../includes/proteksi_session.php';

// Handle delete
if(isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $sql = "DELETE FROM tempat WHERE id = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if($stmt->execute()) {
        header('Location: kelola_tempat.php?pesan=dihapus');
        exit();
    }
}

// Get all places
$sql = "SELECT * FROM tempat ORDER BY dibuat_pada DESC";
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
    <title>Kelola Tempat - Admin Jogja Historia</title>
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
                    <a href="kelola_tempat.php" class="link-nav-admin active">
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

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 px-4 py-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="teks-coklat">Kelola Tempat</h2>
                        <p class="text-muted">Daftar semua tempat heritage</p>
                    </div>
                    <a href="tambah_tempat.php" class="btn btn-traditional">
                        <i class="bi bi-plus-circle"></i> Tambah Tempat Baru
                    </a>
                </div>

                <?php if(isset($_GET['pesan'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?php 
                    if($_GET['pesan'] == 'ditambah') echo 'Tempat berhasil ditambahkan!';
                    if($_GET['pesan'] == 'diupdate') echo 'Tempat berhasil diupdate!';
                    if($_GET['pesan'] == 'dihapus') echo 'Tempat berhasil dihapus!';
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
                                        <th>Foto</th>
                                        <th>Nama Tempat</th>
                                        <th>Kategori</th>
                                        <th>Alamat</th>
                                        <th>Tanggal Dibuat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(count($daftar_tempat) > 0): ?>
                                        <?php foreach($daftar_tempat as $tempat): ?>
                                        <tr>
                                            <td>
                                                <?php 
                                                $gambar = json_decode($tempat['gambar'], true);
                                                $url_gambar = 'https://via.placeholder.com/80';
                                                if($gambar && count($gambar) > 0) {
                                                    $url_gambar = $gambar[0];
                                                }
                                                ?>
                                                <img src="<?php echo htmlspecialchars($url_gambar); ?>" 
                                                     style="width: 80px; height: 60px; object-fit: cover; border-radius: 5px;">
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($tempat['judul']); ?></strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-coklat">
                                                    <?php echo htmlspecialchars($tempat['kategori']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo substr(htmlspecialchars($tempat['alamat']), 0, 40); ?>...</td>
                                            <td><?php echo date('d M Y', strtotime($tempat['dibuat_pada'])); ?></td>
                                            <td>
                                                <a href="edit_tempat.php?id=<?php echo $tempat['id']; ?>" 
                                                   class="btn btn-sm btn-outline-secondary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="kelola_tempat.php?hapus=<?php echo $tempat['id']; ?>" 
                                                   class="btn btn-sm btn-outline-danger"
                                                   onclick="return confirm('Yakin ingin menghapus tempat ini?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                                <a href="../detail_tempat.php?slug=<?php echo $tempat['slug']; ?>" 
                                                   class="btn btn-sm btn-outline-primary" target="_blank">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                                                <p class="text-muted mt-2">Belum ada tempat yang ditambahkan</p>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($koneksi); ?>