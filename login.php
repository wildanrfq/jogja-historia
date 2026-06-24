<?php
session_start();
require_once 'config.php';

// Redirect jika sudah login
if(isset($_SESSION['id_user'])) {
    if($_SESSION['peran'] == 'admin') {
        alihkan('admin/dashboard.php');
    } else {
        alihkan('index.php');
    }
}

$pesan_error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = bersihkan_input($_POST['email']);
    $kata_sandi = $_POST['kata_sandi'];
    
    if(empty($email) || empty($kata_sandi)) {
        $pesan_error = 'Email dan password harus diisi!';
    } else {
        $sql = "SELECT * FROM pengguna WHERE email = ?";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $hasil = $stmt->get_result();
        
        if($hasil->num_rows > 0) {
            $pengguna = $hasil->fetch_assoc();
            
            if(password_verify($kata_sandi, $pengguna['kata_sandi'])) {
                $_SESSION['id_user'] = $pengguna['id'];
                $_SESSION['nama_user'] = $pengguna['nama'];
                $_SESSION['peran'] = $pengguna['peran'];
                
                if($pengguna['peran'] == 'admin') {
                    alihkan('admin/dashboard.php');
                } else {
                    alihkan('index.php');
                }
            } else {
                $pesan_error = 'Email atau password salah!';
            }
        } else {
            $pesan_error = 'Email atau password salah!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Jogja Historia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        body {
            background: linear-gradient(135deg, var(--primary-brown) 0%, var(--secondary-brown) 50%, var(--dark-brown) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .kartu-login {
            background: white;
            border: 4px solid var(--light-brown);
            border-radius: 15px;
            padding: 40px;
            max-width: 450px;
            width: 100%;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            position: relative;
        }
        
        .kartu-login::before,
        .kartu-login::after {
            content: '';
            position: absolute;
            width: 30px;
            height: 30px;
            border: 4px solid var(--gold);
        }
        
        .kartu-login::before {
            top: 15px;
            left: 15px;
            border-right: none;
            border-bottom: none;
        }
        
        .kartu-login::after {
            bottom: 15px;
            right: 15px;
            border-left: none;
            border-top: none;
        }
        
        .header-login {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .ikon-logo {
            font-size: 4rem;
            color: var(--gold);
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="kartu-login">
        <div class="header-login">
            <div class="ikon-logo">
                <i class="bi bi-bank2"></i>
            </div>
            <h2 class="teks-coklat">Jogja Historia</h2>
            <p class="text-muted">Login ke Panel Admin</p>
        </div>

        <?php if($pesan_error): ?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle"></i> <?php echo $pesan_error; ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label teks-coklat">
                    <i class="bi bi-envelope"></i> Email
                </label>
                <input type="email" name="email" class="form-control" required 
                       value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
            </div>

            <div class="mb-3">
                <label class="form-label teks-coklat">
                    <i class="bi bi-lock"></i> Password
                </label>
                <input type="password" name="kata_sandi" class="form-control" required>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="ingat">
                <label class="form-check-label" for="ingat">
                    Ingat saya
                </label>
            </div>

            <button type="submit" class="btn btn-traditional w-100 mb-3">
                <i class="bi bi-box-arrow-in-right"></i> Login
            </button>

            <div class="text-center">
                <a href="index.php" class="text-decoration-none teks-coklat">
                    <i class="bi bi-arrow-left"></i> Kembali ke Beranda
                </a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
mysqli_close($koneksi);
?>