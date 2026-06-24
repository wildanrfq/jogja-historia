<?php
session_start();
require_once 'config.php';

// Get all places
$sql = "SELECT * FROM tempat WHERE latitude IS NOT NULL AND longitude IS NOT NULL ORDER BY judul";
$stmt = $koneksi->prepare($sql);
$stmt->execute();
$hasil = $stmt->get_result();
$daftar_tempat = array();
while($baris = $hasil->fetch_assoc()) {
    $daftar_tempat[] = $baris;
}

// Get categories
$sql = "SELECT DISTINCT kategori FROM tempat ORDER BY kategori";
$stmt = $koneksi->prepare($sql);
$stmt->execute();
$hasil = $stmt->get_result();
$daftar_kategori = array();
while($baris = $hasil->fetch_assoc()) {
    $daftar_kategori[] = $baris['kategori'];
}

$tempat_highlight = isset($_GET['tempat']) ? intval($_GET['tempat']) : null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Interaktif - Jogja Historia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        #peta {
            height: calc(100vh - 200px);
            min-height: 500px;
            border: 4px solid var(--light-brown);
            border-radius: 10px;
        }
        
        .sidebar-peta {
            background: white;
            border: 3px solid var(--light-brown);
            border-radius: 10px;
            padding: 20px;
            height: calc(100vh - 200px);
            overflow-y: auto;
        }
        
        .item-tempat {
            padding: 15px;
            border-bottom: 2px solid var(--cream);
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .item-tempat:hover {
            background-color: rgba(212, 175, 55, 0.1);
            border-left: 4px solid var(--gold);
        }
        
        .item-tempat.active {
            background-color: rgba(212, 175, 55, 0.2);
            border-left: 4px solid var(--gold);
        }
        
        .tombol-filter {
            margin: 5px;
        }
    </style>
</head>
<body class="pola-wayang">
    <?php include 'includes/navbar.php'; ?>

    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12 mt-5">
                    <h1>Coba Peta Interaktif.</h1>
                    <p class="lead">Temukan lokasi semua situs warisan budaya Yogyakarta di peta.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-4">
        <div class="container-fluid">
            <div class="row g-4">
                <!-- Sidebar -->
                <div class="col-lg-3">
                    <div class="sidebar-peta">
                        <h4 class="teks-coklat mb-3">
                            <i class="bi bi-funnel"></i> Filter Kategori
                        </h4>
                        <div class="mb-4">
                            <button class="btn btn-traditional btn-sm tombol-filter active" data-kategori="semua">
                                Semua
                            </button>
                            <?php foreach($daftar_kategori as $kat): ?>
                            <button class="btn btn-outline-secondary btn-sm tombol-filter" 
                                    data-kategori="<?php echo htmlspecialchars($kat); ?>">
                                <?php echo htmlspecialchars($kat); ?>
                            </button>
                            <?php endforeach; ?>
                        </div>

                        <h5 class="teks-coklat mb-3">
                            <i class="bi bi-list"></i> Daftar Tempat
                        </h5>
                        <div id="daftarTempat">
                            <?php foreach($daftar_tempat as $tempat): ?>
                            <div class="item-tempat" 
                                 data-id="<?php echo $tempat['id']; ?>"
                                 data-kategori="<?php echo htmlspecialchars($tempat['kategori']); ?>"
                                 data-lat="<?php echo $tempat['latitude']; ?>"
                                 data-lng="<?php echo $tempat['longitude']; ?>"
                                 onclick="zoomKeTempat(<?php echo $tempat['id']; ?>, <?php echo $tempat['latitude']; ?>, <?php echo $tempat['longitude']; ?>)">
                                <span class="badge bg-coklat mb-2">
                                    <?php echo htmlspecialchars($tempat['kategori']); ?>
                                </span>
                                <h6 class="teks-coklat mb-1">
                                    <?php echo htmlspecialchars($tempat['judul']); ?>
                                </h6>
                                <small class="text-muted">
                                    <i class="bi bi-geo-alt"></i> 
                                    <?php echo substr(htmlspecialchars($tempat['alamat']), 0, 50); ?>...
                                </small>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Map -->
                <div class="col-lg-9">
                    <div id="peta"></div>
                    <div class="mt-3">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> 
                            Klik marker di peta atau daftar di samping untuk melihat detail lokasi. 
                            Gunakan filter kategori untuk menampilkan tempat tertentu.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
    <script>
        // Initialize map
        const peta = L.map('peta').setView([-7.7956, 110.3695], 12);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(peta);

        // Data tempat
        const daftarTempat = <?php echo json_encode($daftar_tempat); ?>;
        const tempatHighlight = <?php echo json_encode($tempat_highlight); ?>;
        
        // Marker cluster
        const markers = L.markerClusterGroup({
            spiderfyOnMaxZoom: true,
            showCoverageOnHover: false,
            zoomToBoundsOnClick: true
        });

        let semuaMarker = {};

        // Add markers
        daftarTempat.forEach(tempat => {
            if(tempat.latitude && tempat.longitude) {
                const marker = L.marker([tempat.latitude, tempat.longitude]);
                
                // Popup
                const gambar = tempat.gambar ? JSON.parse(tempat.gambar) : [];
                const htmlGambar = gambar.length > 0 ? 
                    `<img src="${gambar[0]}" style="width: 100%; max-height: 150px; object-fit: cover; margin-bottom: 10px; border-radius: 5px;">` : '';
                
                const kontenPopup = `
                    ${htmlGambar}
                    <div style="font-family: Georgia, serif;">
                        <div style="background: var(--primary-brown); color: white; padding: 5px 10px; border-radius: 15px; display: inline-block; font-size: 0.8rem; margin-bottom: 8px;">
                            ${tempat.kategori}
                        </div>
                        <h5 style="color: var(--primary-brown); margin: 0 0 8px 0;">
                            <strong>${tempat.judul}</strong>
                        </h5>
                        <p style="margin: 0 0 5px 0; font-size: 0.9rem; color: #666;">
                            <i class="bi bi-geo-alt"></i> ${tempat.alamat}
                        </p>
                        ${tempat.jam_buka ? `
                        <p style="margin: 0 0 10px 0; font-size: 0.9rem; color: #666;">
                            <i class="bi bi-clock"></i> ${tempat.jam_buka}
                        </p>` : ''}
                        <a href="detail_tempat.php?slug=${tempat.slug}" 
                           class="btn btn-traditional btn-sm" 
                           style="text-decoration: none; display: inline-block; margin-top: 5px;">
                            <i class="bi bi-info-circle"></i> Lihat Detail
                        </a>
                    </div>
                `;
                
                marker.bindPopup(kontenPopup, {maxWidth: 300});
                marker.dataTempat = tempat;
                
                markers.addLayer(marker);
                semuaMarker[tempat.id] = marker;
            }
        });

        peta.addLayer(markers);

        // Highlight tempat jika ada
        if(tempatHighlight && semuaMarker[tempatHighlight]) {
            const marker = semuaMarker[tempatHighlight];
            peta.setView([marker.dataTempat.latitude, marker.dataTempat.longitude], 16);
            marker.openPopup();
        }

        // Filter
        document.querySelectorAll('.tombol-filter').forEach(btn => {
            btn.addEventListener('click', function() {
                const kategori = this.dataset.kategori;
                
                // Update tombol aktif
                document.querySelectorAll('.tombol-filter').forEach(b => {
                    b.classList.remove('active', 'btn-traditional');
                    b.classList.add('btn-outline-secondary');
                });
                this.classList.add('active', 'btn-traditional');
                this.classList.remove('btn-outline-secondary');
                
                // Clear markers
                markers.clearLayers();
                
                // Filter tempat
                document.querySelectorAll('.item-tempat').forEach(item => {
                    if(kategori === 'semua' || item.dataset.kategori === kategori) {
                        item.style.display = 'block';
                        const idTempat = parseInt(item.dataset.id);
                        if(semuaMarker[idTempat]) {
                            markers.addLayer(semuaMarker[idTempat]);
                        }
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });

        function zoomKeTempat(id, lat, lng) {
            peta.setView([lat, lng], 16);
            if(semuaMarker[id]) {
                semuaMarker[id].openPopup();
            }
            
            // Highlight di sidebar
            document.querySelectorAll('.item-tempat').forEach(item => {
                item.classList.remove('active');
            });
            document.querySelector(`[data-id="${id}"]`).classList.add('active');
        }
    </script>
</body>
</html>
<?php mysqli_close($koneksi); ?>