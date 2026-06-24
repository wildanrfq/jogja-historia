<?php
session_start();
require_once '../config.php';
require_once '../includes/proteksi_session.php';

$pesan_error = array();
$data_tempat = null;

// Get place ID
if(!isset($_GET['id'])) {
    header('Location: kelola_tempat.php');
    exit();
}

$id_tempat = intval($_GET['id']);

// Get existing place data
$sql = "SELECT * FROM tempat WHERE id = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $id_tempat);
$stmt->execute();
$hasil = $stmt->get_result();

if($hasil->num_rows == 0) {
    header('Location: kelola_tempat.php?pesan=tidak_ditemukan');
    exit();
}

$data_tempat = $hasil->fetch_assoc();

// Handle form submission
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
    
    // Generate slug if title changed
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

    if(!empty($_POST['gambar']) && is_array($_POST['gambar'])) {
        foreach($_POST['gambar'] as $url) {
            if(!empty(trim($url))) {
                $gambar[] = bersihkan_input($url);
            }
        }
    }

    $gambar_json = json_encode($gambar);

    
    // Validation
    if(empty($judul)) {
        $pesan_error[] = 'Nama tempat harus diisi';
    }
    if(empty($kategori)) {
        $pesan_error[] = 'Kategori harus dipilih';
    }
    
    // Check if slug already exists (except for current place)
    $sql = "SELECT id FROM tempat WHERE slug = ? AND id != ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("si", $slug, $id_tempat);
    $stmt->execute();
    $hasil_cek = $stmt->get_result();
    
    if($hasil_cek->num_rows > 0) {
        $pesan_error[] = 'Nama tempat sudah digunakan, gunakan nama lain';
    }
    
    if(count($pesan_error) == 0) {
        $sql = "UPDATE tempat SET 
                judul = ?, slug = ?, kategori = ?, deskripsi_singkat = ?, 
                deskripsi_lengkap = ?, alamat = ?, latitude = ?, longitude = ?, 
                jam_buka = ?, info_tiket = ?, kontak = ?, website = ?, gambar = ?
                WHERE id = ?";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("ssssssddsssssi", 
            $judul, $slug, $kategori, $deskripsi_singkat, $deskripsi_lengkap, 
            $alamat, $latitude, $longitude, $jam_buka, $tiket_json, 
            $kontak, $website, $gambar_json, $id_tempat
        );
        
        if($stmt->execute()) {
            header('Location: kelola_tempat.php?pesan=diupdate');
            exit();
        } else {
            $pesan_error[] = 'Gagal mengupdate tempat: ' . mysqli_error($koneksi);
        }
    }
}

// Parse JSON data for form
$info_tiket = json_decode($data_tempat['info_tiket'], true);
$gambar = json_decode($data_tempat['gambar'], true);

$daftar_kategori = array('Museum', 'Candi', 'Keraton', 'Benteng', 'Monumen', 'Religious Heritage', 'Water Castle', 'Transportasi');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tempat - Admin Jogja Historia</title>
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
                                <i class="bi bi-pencil-square"></i> Edit Tempat
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
                                           value="<?php echo htmlspecialchars($data_tempat['judul']); ?>">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label teks-coklat">
                                        <strong>Kategori *</strong>
                                    </label>
                                    <select name="kategori" class="form-select" required>
                                        <option value="">Pilih Kategori</option>
                                        <?php foreach($daftar_kategori as $kat): ?>
                                        <option value="<?php echo $kat; ?>" 
                                            <?php echo ($data_tempat['kategori'] == $kat) ? 'selected' : ''; ?>>
                                            <?php echo $kat; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label teks-coklat">
                                        <strong>Deskripsi Singkat</strong>
                                    </label>
                                    <textarea name="deskripsi_singkat" class="form-control" rows="2"><?php echo htmlspecialchars($data_tempat['deskripsi_singkat'] ?? ''); ?></textarea>
                                </div>

                                <div class="col-12">
                                    <label class="form-label teks-coklat">
                                        <strong>Deskripsi Lengkap</strong>
                                    </label>
                                    <textarea name="deskripsi_lengkap" class="form-control" rows="6"><?php echo htmlspecialchars($data_tempat['deskripsi_lengkap'] ?? ''); ?></textarea>
                                </div>

                                <div class="col-md-8">
                                    <label class="form-label teks-coklat">
                                        <strong>Alamat</strong>
                                    </label>
                                    <textarea name="alamat" class="form-control" rows="2"><?php echo htmlspecialchars($data_tempat['alamat'] ?? ''); ?></textarea>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label teks-coklat">
                                        <strong>Latitude</strong>
                                    </label>
                                    <input type="text" name="latitude" class="form-control" placeholder="-7.7956"
                                           value="<?php echo htmlspecialchars($data_tempat['latitude'] ?? ''); ?>">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label teks-coklat">
                                        <strong>Longitude</strong>
                                    </label>
                                    <input type="text" name="longitude" class="form-control" placeholder="110.3695"
                                           value="<?php echo htmlspecialchars($data_tempat['longitude'] ?? ''); ?>">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label teks-coklat">
                                        <strong>Jam Buka</strong>
                                    </label>
                                    <input type="text" name="jam_buka" class="form-control" placeholder="08:00-16:00"
                                           value="<?php echo htmlspecialchars($data_tempat['jam_buka'] ?? ''); ?>">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label teks-coklat">
                                        <strong>Kontak</strong>
                                    </label>
                                    <input type="text" name="kontak" class="form-control" placeholder="+62 274 xxx xxx"
                                           value="<?php echo htmlspecialchars($data_tempat['kontak'] ?? ''); ?>">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label teks-coklat">
                                        <strong>Website</strong>
                                    </label>
                                    <input type="url" name="website" class="form-control" placeholder="https://..."
                                           value="<?php echo htmlspecialchars($data_tempat['website'] ?? ''); ?>">
                                </div>

                                <div class="col-12">
                                    <hr>
                                    <h5 class="teks-coklat">Informasi Tiket</h5>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Harga Dewasa (Rp)</label>
                                    <input type="number" name="tiket_dewasa" class="form-control" placeholder="15000"
                                           value="<?php echo isset($info_tiket['dewasa']) ? $info_tiket['dewasa'] : ''; ?>">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Harga Anak (Rp)</label>
                                    <input type="number" name="tiket_anak" class="form-control" placeholder="7500"
                                           value="<?php echo isset($info_tiket['anak']) ? $info_tiket['anak'] : ''; ?>">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Catatan Tiket</label>
                                    <input type="text" name="catatan_tiket" class="form-control" placeholder="Gratis untuk..."
                                           value="<?php echo isset($info_tiket['catatan']) ? htmlspecialchars($info_tiket['catatan']) : ''; ?>">
                                </div>

                                <div class="col-12">
                                    <hr>
                                    <h5 class="teks-coklat d-flex justify-content-between align-items-center">
                                        Gambar (URL)
                                        <button type="button" id="btnTambahGambar" class="btn btn-success btn-sm">
                                            <i class="bi bi-plus-lg"></i> Tambah Gambar
                                        </button>
                                    </h5>
                                </div>

                                <div id="containerGambar">
                                    <?php 
                                    if(!empty($gambar)) {
                                        foreach($gambar as $index => $url): ?>
                                            <div class="row mb-3 item-gambar">
                                                <div class="col-md-10">
                                                    <input type="url" name="gambar[]" class="form-control" 
                                                        value="<?= htmlspecialchars($url) ?>" placeholder="https://..." required>
                                                </div>
                                                <div class="col-md-2 d-flex align-items-center">
                                                    <button type="button" class="btn btn-danger btn-sm btnHapusGambar">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                                <div class="col-md-12 mt-2">
                                                    <img src="<?= htmlspecialchars($url) ?>" class="img-thumbnail" style="max-height:100px;">
                                                </div>
                                            </div>
                                        <?php endforeach;
                                    } else { ?>
                                        <!-- Default 1 input kosong -->
                                        <div class="row mb-3 item-gambar">
                                            <div class="col-md-10">
                                                <input type="url" name="gambar[]" class="form-control" placeholder="https://...">
                                            </div>
                                            <div class="col-md-2 d-flex align-items-center">
                                                <button type="button" class="btn btn-danger btn-sm btnHapusGambar">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>


                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-traditional btn-lg">
                                        <i class="bi bi-save"></i> Update Tempat
                                    </button>
                                    <a href="kelola_tempat.php" class="btn btn-outline-secondary btn-lg ms-2">
                                        Batal
                                    </a>
                                    <a href="../detail_tempat.php?slug=<?php echo $data_tempat['slug']; ?>" 
                                       class="btn btn-outline-primary btn-lg ms-2" target="_blank">
                                        <i class="bi bi-eye"></i> Preview
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
    <script>
        document.getElementById("btnTambahGambar").addEventListener("click", function() {
            const container = document.getElementById("containerGambar");
            const total = container.querySelectorAll(".item-gambar").length;

            if (total >= 6) {
                alert("Maksimal 6 gambar!");
                return;
            }

            const div = document.createElement("div");
            div.classList.add("row", "mb-3", "item-gambar");

            div.innerHTML = `
                <div class="col-md-10">
                    <input type="url" name="gambar[]" class="form-control" placeholder="https://..." required>
                </div>
                <div class="col-md-2 d-flex align-items-center">
                    <button type="button" class="btn btn-danger btn-sm btnHapusGambar">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `;

            container.appendChild(div);
        });

        // Delegasi event untuk tombol hapus
        document.addEventListener("click", function(e) {
            if (e.target.closest(".btnHapusGambar")) {
                const item = e.target.closest(".item-gambar");
                item.remove();
            }
        });
    </script>

</body>
</html>
<?php mysqli_close($koneksi); ?>