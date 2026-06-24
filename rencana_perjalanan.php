
<?php
session_start();
require_once 'config.php';

// Get all places
$sql = "SELECT * FROM tempat ORDER BY kategori, judul";
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
    <title>Buat Itinerary - Jogja Historia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .item-pilih-tempat {
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid transparent;
        }
        
        .item-pilih-tempat:hover {
            border-color: var(--gold);
            background-color: rgba(212, 175, 55, 0.1);
        }
        
        .item-itinerary {
            background: white;
            border: 3px solid var(--light-brown);
            border-left: 5px solid var(--gold);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            position: relative;
            cursor: move;
        }
        
        .item-itinerary:hover {
            box-shadow: 0 4px 15px rgba(107, 68, 35, 0.2);
        }
        
        .tombol-hapus {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            font-size: 1.2rem;
            line-height: 1;
            cursor: pointer;
        }
        
        .pegangan-drag {
            cursor: move;
            color: var(--light-brown);
            margin-right: 10px;
        }
        
        #daftarItinerary.kosong {
            border: 3px dashed var(--light-brown);
            border-radius: 10px;
            padding: 50px;
            text-align: center;
            color: var(--light-brown);
        }
    </style>
</head>
<body class="pola-wayang">
    <?php include 'includes/navbar.php'; ?>

    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12 mt-5">
                    <h1>Buat Rencana Perjalanan.</h1>
                    <p class="lead">Rencanakan kunjungan Anda ke tempat-tempat bersejarah di Yogyakarta.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <!-- Available Places -->
                <div class="col-lg-4">
                    <div class="card kartu-tradisional">
                        <div class="card-body">
                            <h4 class="teks-coklat mb-3">
                                <i class="bi bi-list-ul"></i> Pilih Tempat
                            </h4>
                            
                            <!-- Search -->
                            <div class="mb-3">
                                <input type="text" id="pencarianTempat" class="form-control" 
                                       placeholder="Cari tempat...">
                            </div>
                            
                            <!-- Category Filter -->
                            <div class="mb-3">
                                <select id="filterKategori" class="form-select">
                                    <option value="">Semua Kategori</option>
                                    <?php
                                    $kategori_unik = array_unique(array_column($daftar_tempat, 'kategori'));
                                    sort($kategori_unik);
                                    foreach($kategori_unik as $kat):
                                    ?>
                                    <option value="<?php echo htmlspecialchars($kat); ?>">
                                        <?php echo htmlspecialchars($kat); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <!-- Places List -->
                            <div id="daftarTempatPilih" style="max-height: 500px; overflow-y: auto;">
                                <?php foreach($daftar_tempat as $tempat): ?>
                                <div class="item-pilih-tempat p-3 mb-2 rounded" 
                                     data-id="<?php echo $tempat['id']; ?>"
                                     data-kategori="<?php echo htmlspecialchars($tempat['kategori']); ?>"
                                     data-judul="<?php echo htmlspecialchars($tempat['judul']); ?>"
                                     data-alamat="<?php echo htmlspecialchars($tempat['alamat']); ?>"
                                     data-jam="<?php echo htmlspecialchars($tempat['jam_buka']); ?>"
                                     data-gambar="<?php 
                                        $gambar = json_decode($tempat['gambar'], true);
                                        echo isset($gambar[0]) ? htmlspecialchars($gambar[0]) : '';
                                     ?>"
                                     onclick="tambahKeItinerary(this)">
                                    <div class="d-flex align-items-start">
                                        <?php 
                                        $gambar = json_decode($tempat['gambar'], true);
                                        $url_gambar = isset($gambar[0]) ? $gambar[0] : 'https://via.placeholder.com/60';
                                        ?>
                                        <img src="<?php echo $url_gambar; ?>" 
                                             style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px; margin-right: 10px;">
                                        <div class="flex-grow-1">
                                            <span class="badge bg-coklat small">
                                                <?php echo htmlspecialchars($tempat['kategori']); ?>
                                            </span>
                                            <h6 class="teks-coklat mb-1">
                                                <?php echo htmlspecialchars($tempat['judul']); ?>
                                            </h6>
                                            <small class="text-muted">
                                                <i class="bi bi-geo-alt"></i>
                                                <?php echo substr(htmlspecialchars($tempat['alamat']), 0, 40); ?>...
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Itinerary Builder -->
                <div class="col-lg-8">
                    <div class="card kartu-tradisional">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="teks-coklat mb-0">
                                    <i class="bi bi-calendar-check"></i> Itinerary Anda
                                </h4>
                                <div>
                                    <button class="btn btn-outline-secondary btn-sm me-2" onclick="hapusSemuaItinerary()">
                                        <i class="bi bi-trash"></i> Hapus Semua
                                    </button>
                                    <button class="btn btn-traditional btn-sm" onclick="simpanItinerary()">
                                        <i class="bi bi-save"></i> Simpan
                                    </button>
                                </div>
                            </div>

                            <!-- Itinerary Title -->
                            <div class="mb-4">
                                <label class="form-label teks-coklat">
                                    <strong>Nama Itinerary:</strong>
                                </label>
                                <input type="text" id="judulItinerary" class="form-control" 
                                       placeholder="Misal: Tur Sejarah 1 Hari" value="Itinerary Saya">
                            </div>

                            <!-- Summary -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="alert alert-info">
                                        <strong>Total Tempat:</strong> 
                                        <span id="totalTempat">0</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="alert alert-info">
                                        <strong>Estimasi Durasi:</strong> 
                                        <span id="estimasiDurasi">0 jam</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Itinerary List -->
                            <div id="daftarItinerary" class="kosong">
                                <i class="bi bi-calendar-x" style="font-size: 4rem;"></i>
                                <p class="mt-3">Belum ada tempat yang dipilih</p>
                                <p class="small">Klik tempat di sebelah kiri untuk menambahkan ke itinerary</p>
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-4 text-end">
                                <button class="btn btn-outline-secondary me-2" onclick="cetakItinerary()">
                                    <i class="bi bi-printer"></i> Cetak
                                </button>
                                <button class="btn btn-outline-secondary me-2" onclick="bagikanItinerary()">
                                    <i class="bi bi-share"></i> Bagikan
                                </button>
                                <button class="btn btn-traditional" onclick="exportItinerary()">
                                    <i class="bi bi-download"></i> Export PDF
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script>
        let tempatItinerary = [];
        
        function tambahKeItinerary(element) {
            const tempat = {
                id: element.dataset.id,
                judul: element.dataset.judul,
                kategori: element.dataset.kategori,
                alamat: element.dataset.alamat,
                jam: element.dataset.jam,
                gambar: element.dataset.gambar
            };
            
            if(tempatItinerary.find(p => p.id === tempat.id)) {
                alert('Tempat ini sudah ada dalam itinerary!');
                return;
            }
            
            tempatItinerary.push(tempat);
            renderItinerary();
        }
        
        function hapusDariItinerary(index) {
            tempatItinerary.splice(index, 1);
            renderItinerary();
        }
        
        function renderItinerary() {
            const container = document.getElementById('daftarItinerary');
            
            if(tempatItinerary.length === 0) {
                container.className = 'kosong';
                container.innerHTML = `
                    <i class="bi bi-calendar-x" style="font-size: 4rem;"></i>
                    <p class="mt-3">Belum ada tempat yang dipilih</p>
                    <p class="small">Klik tempat di sebelah kiri untuk menambahkan ke itinerary</p>
                `;
                updateRingkasan();
                return;
            }
            
            container.className = '';
            container.innerHTML = tempatItinerary.map((tempat, index) => `
                <div class="item-itinerary" data-index="${index}">
                    <button class="tombol-hapus" onclick="hapusDariItinerary(${index})">&times;</button>
                    <div class="d-flex">
                        <i class="bi bi-grip-vertical pegangan-drag fs-4"></i>
                        <div class="flex-shrink-0 me-3">
                            <div class="badge bg-emas text-dark" style="font-size: 1rem; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                ${index + 1}
                            </div>
                        </div>
                        <img src="${tempat.gambar || 'https://via.placeholder.com/80'}" 
                             style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; margin-right: 15px;">
                        <div class="flex-grow-1">
                            <span class="badge bg-coklat small">${tempat.kategori}</span>
                            <h5 class="teks-coklat mb-1">${tempat.judul}</h5>
                            <p class="text-muted mb-1 small">
                                <i class="bi bi-geo-alt"></i> ${tempat.alamat}
                            </p>
                            ${tempat.jam ? `
                            <p class="text-muted mb-0 small">
                                <i class="bi bi-clock"></i> ${tempat.jam}
                            </p>` : ''}
                        </div>
                    </div>
                </div>
            `).join('');
            
            $("#daftarItinerary").sortable({
                handle: ".pegangan-drag",
                update: function(event, ui) {
                    const urutanBaru = [];
                    $('.item-itinerary').each(function() {
                        const index = $(this).data('index');
                        urutanBaru.push(tempatItinerary[index]);
                    });
                    tempatItinerary = urutanBaru;
                    renderItinerary();
                }
            });
            
            updateRingkasan();
        }
        
        function updateRingkasan() {
            document.getElementById('totalTempat').textContent = tempatItinerary.length;
            const jam = tempatItinerary.length * 2;
            document.getElementById('estimasiDurasi').textContent = `${jam} jam`;
        }
        
        function hapusSemuaItinerary() {
            if(confirm('Yakin ingin menghapus semua tempat dari itinerary?')) {
                tempatItinerary = [];
                renderItinerary();
            }
        }
        
        function simpanItinerary() {
            if(tempatItinerary.length === 0) {
                alert('Tambahkan tempat terlebih dahulu!');
                return;
            }
            
            const judul = document.getElementById('judulItinerary').value;
            const data = {
                judul: judul,
                tempat: tempatItinerary,
                durasi: tempatItinerary.length * 120
            };
            
            let tersimpan = JSON.parse(localStorage.getItem('itinerary_tersimpan') || '[]');
            tersimpan.push(data);
            localStorage.setItem('itinerary_tersimpan', JSON.stringify(tersimpan));
            
            alert('Itinerary berhasil disimpan!');
        }
        
        // Search
        document.getElementById('pencarianTempat').addEventListener('input', function(e) {
            filterTempat();
        });
        
        document.getElementById('filterKategori').addEventListener('change', function() {
            filterTempat();
        });
        
        function filterTempat() {
            const pencarian = document.getElementById('pencarianTempat').value.toLowerCase();
            const kategori = document.getElementById('filterKategori').value;
            
            document.querySelectorAll('.item-pilih-tempat').forEach(item => {
                const judul = item.dataset.judul.toLowerCase();
                const itemKategori = item.dataset.kategori;
                
                const cocokPencarian = !pencarian || judul.includes(pencarian);
                const cocokKategori = !kategori || itemKategori === kategori;
                
                item.style.display = (cocokPencarian && cocokKategori) ? 'block' : 'none';
            });
        }
        
        function cetakItinerary() {
            if(tempatItinerary.length === 0) {
                alert('Belum ada tempat dalam itinerary!');
                return;
            }
            window.print();
        }
        
        function bagikanItinerary() {
            if(tempatItinerary.length === 0) {
                alert('Belum ada tempat dalam itinerary!');
                return;
            }
            
            const teks = `Itinerary Saya:\n${tempatItinerary.map((p, i) => `${i+1}. ${p.judul}`).join('\n')}`;
            
            if (navigator.share) {
                navigator.share({
                    title: 'Itinerary Jogja Historia',
                    text: teks
                });
            } else {
                navigator.clipboard.writeText(teks);
                alert('Itinerary berhasil disalin ke clipboard!');
            }
        }
        
        function exportItinerary() {
            alert('Fitur export PDF akan segera hadir!');
        }
    </script>
</body>
</html>
<?php mysqli_close($koneksi); ?>