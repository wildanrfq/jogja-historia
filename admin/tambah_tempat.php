<?php
session_start();
require_once '../config.php';
require_once '../includes/proteksi_session.php';

$pesan_error = array();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = bersihkan_input($_POST['judul']);
    $kategori = bersihkan_input($_POST['kategori']);
    $deskripsi_singkat = bersihkan_input($_POST['deskripsi_singkat']);
    $deskripsi_lengkap = mysqli_real_escape_string($koneksi, $_POST['deskripsi_lengkap']);
    $alamat = bersihkan_input($_POST['alamat']);
    $latitude = floatval($_POST['latitude']);
    $longitude = floatval($_POST['longitude']);
    $jam_buka = bersihkan_input($_POST['jam_buka']);
    $kontak = bersihkan_input($_POST['kontak']);
    $website = bersihkan_input($_POST['website']);
    
    // Generate slug
    $slug = buat_slug($judul);
    
    // Ticket info JSON
    $info_tiket = array();
    if(!empty($_POST['tiket_dewasa'])) {
        $info_tiket['dewasa'] = intval($_POST['tiket_dewasa']);
    }
    if(!empty($_POST['tiket_anak'])) {
        $info_tiket['anak'] = intval($_POST['tiket_anak']);
    }
    if(!empty($_POST['catatan_tiket'])) {
        $info_tiket['catatan'] = bersihkan_input($_POST['catatan_tiket']);
    }
    $tiket_json = json_encode($info_tiket);
    
    // Images JSON
    $gambar = array();
    if(!empty($_POST['gambar1'])) $gambar[] = bersihkan_input($_POST['gambar1']);
    if(!empty($_POST['gambar2'])) $gambar[] = bersihkan_input($_POST['gambar2']);
    if(!empty($_POST['gambar3'])) $gambar[] = bersihkan_input($_POST['gambar3']);
    $gambar_json = json_encode($gambar);
    
    // Validation
    if(empty($judul)) {
        $pesan_error[] = 'Nama tempat harus diisi';
    }
    if(empty($kategori)) {
        $pesan_error[] = 'Kategori harus dipilih';
    }
    
    if(count($pesan_error) == 0) {
        $dibuat_oleh = $_SESSION['id_user'];
        
        $sql = "INSERT INTO tempat (judul, slug, kategori, deskripsi_singkat, deskripsi_lengkap, alamat, latitude, longitude, jam_buka, info_tiket, kontak, website, gambar, dibuat_oleh) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("ssssssddsssssi", 
            $judul, $slug, $kategori, $deskripsi_singkat, $deskripsi_lengkap, 
            $alamat, $latitude, $longitude, $jam_buka, $tiket_json, 
            $kontak, $website, $gambar_json, $dibuat_oleh
        );
        
        if($stmt->execute()) {
            header('Location: kelola_tempat.php?pesan=ditambah');
            exit();
        } else {
            $pesan_error[] = 'Gagal menambahkan tempat: ' . mysqli_error($koneksi);
        }
    }
}

$daftar_kategori = array('Museum', 'Candi', 'Keraton', 'Benteng', 'Monumen', 'Religious Heritage', 'Water Castle', 'Transportasi');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Tempat - Admin Jogja Historia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body class="pola-wayang">
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="card kartu-tradisional">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="teks-coklat mb-0">
                                <i class="bi bi-plus-circle"></i> Tambah Tempat Baru
                            </h2>
                            <a href="kelola_tempat.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>

                        <?php if(count($pesan_error) > 0): ?>
                        <div class="alert alert-danger">
                            <strong>Error:</strong>
                            <ul class="mb-0">
                                <?php foreach($pesan_error as $error): ?>
                                <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label teks-coklat">
                                        <strong>Nama Tempat *</strong>
                                    </label>
                                    <input type="text" name="judul" class="form-control" required
                                           value="<?php echo isset($_POST['judul']) ? htmlspecialchars($_POST['judul']) : ''; ?>">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label teks-coklat">
                                        <strong>Kategori *</strong>
                                    </label>
                                    <select name="kategori" class="form-select" required>
                                        <option value="">Pilih Kategori</option>
                                        <?php foreach($daftar_kategori as $kat): ?>
                                        <option value="<?php echo $kat; ?>" 
                                            <?php echo (isset($_POST['kategori']) && $_POST['kategori'] == $kat) ? 'selected' : ''; ?>>
                                            <?php echo $kat; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label teks-coklat">
                                        <strong>Deskripsi Singkat</strong>
                                    </label>
                                    <textarea name="deskripsi_singkat" class="form-control" rows="2"><?php echo isset($_POST['deskripsi_singkat']) ? htmlspecialchars($_POST['deskripsi_singkat']) : ''; ?></textarea>
                                </div>

                                <div class="col-12">
                                    <label class="form-label teks-coklat">
                                        <strong>Deskripsi Lengkap</strong>
                                    </label>
                                    <textarea name="deskripsi_lengkap" class="form-control" rows="6"><?php echo isset($_POST['deskripsi_lengkap']) ? htmlspecialchars($_POST['deskripsi_lengkap']) : ''; ?></textarea>
                                </div>

                                <div class="col-md-8">
                                    <label class="form-label teks-coklat">
                                        <strong>Alamat</strong>
                                    </label>
                                    <textarea name="alamat" class="form-control" rows="2"><?php echo isset($_POST['alamat']) ? htmlspecialchars($_POST['alamat']) : ''; ?></textarea>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label teks-coklat">
                                        <strong>Latitude</strong>
                                    </label>
                                    <input type="text" name="latitude" class="form-control" placeholder="-7.7956"
                                           value="<?php echo isset($_POST['latitude']) ? htmlspecialchars($_POST['latitude']) : ''; ?>">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label teks-coklat">
                                        <strong>Longitude</strong>
                                    </label>
                                    <input type="text" name="longitude" class="form-control" placeholder="110.3695"
                                           value="<?php echo isset($_POST['longitude']) ? htmlspecialchars($_POST['longitude']) : ''; ?>">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label teks-coklat">
                                        <strong>Jam Buka</strong>
                                    </label>
                                    <input type="text" name="jam_buka" class="form-control" placeholder="08:00-16:00"
                                           value="<?php echo isset($_POST['jam_buka']) ? htmlspecialchars($_POST['jam_buka']) : ''; ?>">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label teks-coklat">
                                        <strong>Kontak</strong>
                                    </label>
                                    <input type="text" name="kontak" class="form-control" placeholder="+62 274 xxx xxx"
                                           value="<?php echo isset($_POST['kontak']) ? htmlspecialchars($_POST['kontak']) : ''; ?>">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label teks-coklat">
                                        <strong>Website</strong>
                                    </label>
                                    <input type="url" name="website" class="form-control" placeholder="https://..."
                                           value="<?php echo isset($_POST['website']) ? htmlspecialchars($_POST['website']) : ''; ?>">
                                </div>

                                <div class="col-12">
                                    <hr>
                                    <h5 class="teks-coklat">Informasi Tiket</h5>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Harga Dewasa (Rp)</label>
                                    <input type="number" name="tiket_dewasa" class="form-control" placeholder="15000"
                                           value="<?php echo isset($_POST['tiket_dewasa']) ? htmlspecialchars($_POST['tiket_dewasa']) : ''; ?>">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Harga Anak (Rp)</label>
                                    <input type="number" name="tiket_anak" class="form-control" placeholder="7500"
                                           value="<?php echo isset($_POST['tiket_anak']) ? htmlspecialchars($_POST['tiket_anak']) : ''; ?>">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Catatan Tiket</label>
                                    <input type="text" name="catatan_tiket" class="form-control" placeholder="Gratis untuk..."
                                           value="<?php echo isset($_POST['catatan_tiket']) ? htmlspecialchars($_POST['catatan_tiket']) : ''; ?>">
                                </div>

                                <div class="col-12">
                                    <hr>
                                    <h5 class="teks-coklat">Gambar (URL)</h5>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Gambar 1 (URL)</label>
                                    <input type="url" name="gambar1" class="form-control" placeholder="https://..."
                                           value="<?php echo isset($_POST['gambar1']) ? htmlspecialchars($_POST['gambar1']) : ''; ?>">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Gambar 2 (URL)</label>
                                    <input type="url" name="gambar2" class="form-control" placeholder="https://..."
                                           value="<?php echo isset($_POST['gambar2']) ? htmlspecialchars($_POST['gambar2']) : ''; ?>">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Gambar 3 (URL)</label>
                                    <input type="url" name="gambar3" class="form-control" placeholder="https://..."
                                           value="<?php echo isset($_POST['gambar3']) ? htmlspecialchars($_POST['gambar3']) : ''; ?>">
                                </div>

                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-traditional btn-lg">
                                        <i class="bi bi-save"></i> Simpan Tempat
                                    </button>
                                    <a href="kelola_tempat.php" class="btn btn-outline-secondary btn-lg ms-2">
                                        Batal
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($koneksi); ?>