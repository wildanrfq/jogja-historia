<?php
// Konfigurasi database
$host_db = 'localhost';
$nama_db = 'projekk';
$user_db = 'root';
$pass_db = '';

$pesan_sukses = array();
$pesan_error = array();

try {
    // Koneksi tanpa database
    $koneksi = mysqli_connect($host_db, $user_db, $pass_db);
    
    if(!$koneksi) {
        throw new Exception("Koneksi gagal: " . mysqli_connect_error());
    }
    
    mysqli_set_charset($koneksi, "utf8mb4");
    
    // Buat database
    $sql = "CREATE DATABASE IF NOT EXISTS $nama_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    if(mysqli_query($koneksi, $sql)) {
        $pesan_sukses[] = "✓ Database berhasil dibuat";
    }
    
    // Pilih database
    mysqli_select_db($koneksi, $nama_db);
    
    // Buat tabel pengguna
    $sql = "CREATE TABLE IF NOT EXISTS pengguna (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        kata_sandi VARCHAR(255) NOT NULL,
        peran ENUM('admin', 'user') DEFAULT 'user',
        dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if(mysqli_query($koneksi, $sql)) {
        $pesan_sukses[] = "✓ Tabel pengguna berhasil dibuat";
    }
    
    // Buat tabel tempat
    $sql = "CREATE TABLE IF NOT EXISTS tempat (
        id INT AUTO_INCREMENT PRIMARY KEY,
        judul VARCHAR(255) NOT NULL,
        slug VARCHAR(255) UNIQUE NOT NULL,
        kategori VARCHAR(100) NOT NULL,
        deskripsi_singkat TEXT,
        deskripsi_lengkap TEXT,
        alamat TEXT,
        latitude DECIMAL(10, 8),
        longitude DECIMAL(11, 8),
        jam_buka VARCHAR(255),
        info_tiket JSON,
        info_aksesibilitas JSON,
        kontak VARCHAR(255),
        website VARCHAR(255),
        gambar JSON,
        dibuat_oleh INT,
        dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        diperbarui_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (dibuat_oleh) REFERENCES pengguna(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if(mysqli_query($koneksi, $sql)) {
        $pesan_sukses[] = "✓ Tabel tempat berhasil dibuat";
    }
    
    // Buat tabel acara
    $sql = "CREATE TABLE IF NOT EXISTS acara (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_tempat INT,
        judul VARCHAR(255) NOT NULL,
        tanggal_mulai DATE,
        tanggal_selesai DATE,
        deskripsi TEXT,
        link_tiket VARCHAR(255),
        dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_tempat) REFERENCES tempat(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if(mysqli_query($koneksi, $sql)) {
        $pesan_sukses[] = "✓ Tabel acara berhasil dibuat";
    }
    
    // Buat tabel rencana_perjalanan
    $sql = "CREATE TABLE IF NOT EXISTS rencana_perjalanan (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_pengguna INT,
        judul VARCHAR(255) NOT NULL,
        deskripsi TEXT,
        urutan_tempat JSON,
        durasi_menit INT,
        dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_pengguna) REFERENCES pengguna(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if(mysqli_query($koneksi, $sql)) {
        $pesan_sukses[] = "✓ Tabel rencana_perjalanan berhasil dibuat";
    }
    
    // Buat tabel sumber
    $sql = "CREATE TABLE IF NOT EXISTS sumber (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_tempat INT,
        tipe_sumber VARCHAR(50),
        url TEXT,
        catatan TEXT,
        FOREIGN KEY (id_tempat) REFERENCES tempat(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if(mysqli_query($koneksi, $sql)) {
        $pesan_sukses[] = "✓ Tabel sumber berhasil dibuat";
    }
    
    // Insert admin user
    $email_admin = 'admin@jogjahistoria.id';
    $sql = "SELECT * FROM pengguna WHERE email = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("s", $email_admin);
    $stmt->execute();
    $hasil = $stmt->get_result();
    
    if($hasil->num_rows == 0) {
        $nama_admin = 'Administrator';
        $kata_sandi = password_hash('admin123', PASSWORD_DEFAULT);
        $peran = 'admin';
        
        $sql = "INSERT INTO pengguna (nama, email, kata_sandi, peran) VALUES (?, ?, ?, ?)";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("ssss", $nama_admin, $email_admin, $kata_sandi, $peran);
        
        if($stmt->execute()) {
            $pesan_sukses[] = "✓ User admin berhasil dibuat (email: admin@jogjahistoria.id, password: admin123)";
        }
    }
    
    // Insert sample data tempat
    $data_tempat = array(
        array(
            'judul' => 'Keraton Yogyakarta',
            'slug' => 'keraton-yogyakarta',
            'kategori' => 'Keraton',
            'deskripsi_singkat' => 'Istana resmi Kesultanan Yogyakarta yang masih berfungsi hingga kini sebagai pusat budaya Jawa.',
            'deskripsi_lengkap' => 'Keraton Ngayogyakarta Hadiningrat atau Keraton Yogyakarta adalah istana resmi Kesultanan Ngayogyakarta Hadiningrat yang kini berlokasi di Kota Yogyakarta. Keraton ini didirikan oleh Sultan Hamengkubuwono I pada tahun 1755. Kompleks keraton ini merupakan pusat kebudayaan Jawa yang masih lestari hingga kini.',
            'alamat' => 'Jl. Rotowijayan No.1, Panembahan, Kraton, Kota Yogyakarta',
            'latitude' => -7.8051,
            'longitude' => 110.3644,
            'jam_buka' => '08:00-14:00 (Tutup Jumat)',
            'info_tiket' => '{"dewasa": 15000, "anak": 7500}',
            'gambar' => '["https://images.unsplash.com/photo-1555400038-63f5ba517a47?w=800", "https://images.unsplash.com/photo-1548013146-72479768bada?w=800"]'
        ),
        array(
            'judul' => 'Taman Sari Water Castle',
            'slug' => 'taman-sari',
            'kategori' => 'Water Castle',
            'deskripsi_singkat' => 'Kompleks taman istana yang dulunya menjadi tempat rekreasi Sultan dan keluarganya.',
            'deskripsi_lengkap' => 'Taman Sari atau Taman Sari Water Castle adalah kompleks taman dan kolam yang dibangun pada masa Sultan Hamengkubuwono I sebagai tempat peristirahatan Sultan. Kompleks ini memiliki kolam pemandian, lorong bawah tanah, dan masjid bawah tanah yang unik.',
            'alamat' => 'Jl. Taman, Patehan, Kraton, Kota Yogyakarta',
            'latitude' => -7.8099,
            'longitude' => 110.3596,
            'jam_buka' => '09:00-15:00',
            'info_tiket' => '{"dewasa": 15000, "anak": 8000}',
            'gambar' => '["https://images.unsplash.com/photo-1598963541372-d399dc3e3ff5?w=800"]'
        ),
        array(
            'judul' => 'Museum Benteng Vredeburg',
            'slug' => 'benteng-vredeburg',
            'kategori' => 'Museum',
            'deskripsi_singkat' => 'Benteng peninggalan Belanda yang kini menjadi museum sejarah perjuangan kemerdekaan Indonesia.',
            'deskripsi_lengkap' => 'Benteng Vredeburg dibangun oleh Belanda pada tahun 1760. Kini benteng ini difungsikan sebagai Museum Khusus Perjuangan Nasional yang menyimpan berbagai koleksi tentang sejarah perjuangan Indonesia, khususnya di Yogyakarta.',
            'alamat' => 'Jl. Margo Mulyo No.6, Ngupasan, Gondomanan, Kota Yogyakarta',
            'latitude' => -7.7993,
            'longitude' => 110.3661,
            'jam_buka' => '08:00-16:00 (Tutup Senin)',
            'info_tiket' => '{"dewasa": 10000, "anak": 5000, "pelajar": 5000}',
            'gambar' => '["https://images.unsplash.com/photo-1566127444979-b3d2b6fd1b8d?w=800"]'
        ),
        array(
            'judul' => 'Candi Prambanan',
            'slug' => 'candi-prambanan',
            'kategori' => 'Candi',
            'deskripsi_singkat' => 'Kompleks candi Hindu terbesar di Indonesia yang dibangun pada abad ke-9.',
            'deskripsi_lengkap' => 'Candi Prambanan adalah kompleks candi Hindu yang dibangun pada abad ke-9 Masehi. Candi ini dipersembahkan untuk Trimurti, tiga dewa utama Hindu yaitu Brahma, Wisnu, dan Siwa. Prambanan adalah situs Warisan Dunia UNESCO.',
            'alamat' => 'Jl. Raya Solo - Yogyakarta, Kranggan, Bokoharjo, Prambanan, Sleman',
            'latitude' => -7.7520,
            'longitude' => 110.4915,
            'jam_buka' => '06:00-17:00',
            'info_tiket' => '{"dewasa_domestik": 50000, "anak_domestik": 25000, "asing": 350000}',
            'gambar' => '["https://images.unsplash.com/photo-1588668632166-e0c5b69c3c73?w=800"]'
        ),
        array(
            'judul' => 'Monumen Yogya Kembali',
            'slug' => 'monjali',
            'kategori' => 'Monumen',
            'deskripsi_singkat' => 'Museum sejarah perjuangan kemerdekaan RI yang berbentuk kerucut menjulang.',
            'deskripsi_lengkap' => 'Monumen Yogya Kembali (Monjali) didirikan untuk mengenang dan menghormati perjuangan rakyat Yogyakarta dalam mempertahankan kemerdekaan Indonesia. Museum ini diresmikan pada tahun 1989 dan berbentuk kerucut dengan tinggi 31,8 meter.',
            'alamat' => 'Jl. Ring Road Utara, Jongkang, Sariharjo, Ngaglik, Sleman',
            'latitude' => -7.7503,
            'longitude' => 110.3677,
            'jam_buka' => '08:00-16:00 (Tutup Senin)',
            'info_tiket' => '{"dewasa": 10000, "anak": 5000}',
            'gambar' => '["https://images.unsplash.com/photo-1566737236500-c8ac43014a67?w=800"]'
        )
    );
    
    foreach($data_tempat as $tempat) {
        $sql = "SELECT id FROM tempat WHERE slug = ?";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("s", $tempat['slug']);
        $stmt->execute();
        $hasil = $stmt->get_result();
        
        if($hasil->num_rows == 0) {
            $sql = "INSERT INTO tempat (judul, slug, kategori, deskripsi_singkat, deskripsi_lengkap, alamat, latitude, longitude, jam_buka, info_tiket, gambar, dibuat_oleh) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
            $stmt = $koneksi->prepare($sql);
            $stmt->bind_param("ssssssddsss", 
                $tempat['judul'],
                $tempat['slug'],
                $tempat['kategori'],
                $tempat['deskripsi_singkat'],
                $tempat['deskripsi_lengkap'],
                $tempat['alamat'],
                $tempat['latitude'],
                $tempat['longitude'],
                $tempat['jam_buka'],
                $tempat['info_tiket'],
                $tempat['gambar']
            );
            $stmt->execute();
        }
    }
    
    $pesan_sukses[] = "✓ Data sampel berhasil dimasukkan (5 tempat heritage)";
    
} catch(Exception $e) {
    $pesan_error[] = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalasi Database - Jogja Historia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            padding: 50px;
            background: linear-gradient(135deg, #6B4423 0%, #C4A582 100%);
            min-height: 100vh;
        }
        .kotak-instalasi {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>
    <div class="kotak-instalasi">
        <h1 class="mb-4 text-center">
            <i class="bi bi-gear-fill"></i> Instalasi Database
        </h1>
        
        <?php if(count($pesan_sukses) > 0): ?>
        <div class="alert alert-success">
            <h4><i class="bi bi-check-circle"></i> Instalasi Berhasil!</h4>
            <ul class="mb-0">
                <?php foreach($pesan_sukses as $pesan): ?>
                <li><?php echo $pesan; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        
        <?php if(count($pesan_error) > 0): ?>
        <div class="alert alert-danger">
            <h4><i class="bi bi-x-circle"></i> Error!</h4>
            <ul class="mb-0">
                <?php foreach($pesan_error as $pesan): ?>
                <li><?php echo $pesan; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        
        <?php if(count($pesan_sukses) > 0 && count($pesan_error) == 0): ?>
        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-lg" style="background: #D4AF37; color: #3E2723; border: 2px solid #3E2723; font-weight: bold;">
                <i class="bi bi-house"></i> Kembali ke Beranda
            </a>
            <a href="login.php" class="btn btn-lg" style="background: #6B4423; color: white; font-weight: bold;">
                <i class="bi bi-box-arrow-in-right"></i> Login Admin
            </a>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
<?php
if(isset($koneksi)) {
    mysqli_close($koneksi);
}
?>