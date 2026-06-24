<?php
$halaman_sekarang = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="bi bi-bank2"></i> Jogja Historia
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?php if($halaman_sekarang == 'index.php') echo 'active'; ?>" 
                       href="index.php">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if($halaman_sekarang == 'jelajahi.php') echo 'active'; ?>" 
                       href="jelajahi.php">Jelajahi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if($halaman_sekarang == 'peta.php') echo 'active'; ?>" 
                       href="peta.php">Peta</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if($halaman_sekarang == 'rencana_perjalanan.php') echo 'active'; ?>" 
                       href="rencana_perjalanan.php">Itinerary</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if($halaman_sekarang == 'acara.php') echo 'active'; ?>" 
                       href="acara.php">Event</a>
                </li>
                <?php if(isset($_SESSION['id_user']) && $_SESSION['peran'] == 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="admin/dashboard.php">
                        <i class="bi bi-gear"></i> Admin
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">
                        <i class="bi bi-person"></i> Login
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>