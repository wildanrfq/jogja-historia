<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5 class="mb-3" style="color: var(--gold);">
                    <i class="bi bi-bank2"></i> Jogja Historia
                </h5>
                <p>Platform digital untuk mengeksplorasi dan melestarikan warisan budaya Yogyakarta. Temukan, pelajari, dan kunjungi situs-situs bersejarah yang mempesona.</p>
            </div>
            <div class="col-md-4 mb-4">
                <h5 class="mb-3" style="color: var(--gold);">Link Cepat</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="index.php" class="text-decoration-none" style="color: var(--cream);">
                            <i class="bi bi-house"></i> Beranda
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="jelajahi.php" class="text-decoration-none" style="color: var(--cream);">
                            <i class="bi bi-compass"></i> Jelajahi
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="peta.php" class="text-decoration-none" style="color: var(--cream);">
                            <i class="bi bi-map"></i> Peta Interaktif
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="rencana_perjalanan.php" class="text-decoration-none" style="color: var(--cream);">
                            <i class="bi bi-calendar-check"></i> Buat Itinerary
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="acara.php" class="text-decoration-none" style="color: var(--cream);">
                            <i class="bi bi-calendar-event"></i> Event
                        </a>
                    </li>
                </ul>
            </div>
            <div class="col-md-4 mb-4">
                <h5 class="mb-3" style="color: var(--gold);">Kontak & Media Sosial</h5>
                <div class="mb-3">
                    <p class="mb-2">
                        <i class="bi bi-envelope-fill"></i> 
                        <a href="mailto:info@jogjahistoria.id" class="text-decoration-none" style="color: var(--cream);">
                            info@jogjahistoria.id
                        </a>
                    </p>
                    <p class="mb-2">
                        <i class="bi bi-telephone-fill"></i> 
                        <a href="tel:+622743333333" class="text-decoration-none" style="color: var(--cream);">
                            +62 274 333 3333
                        </a>
                    </p>
                    <p class="mb-0">
                        <i class="bi bi-geo-alt-fill"></i> Yogyakarta, Indonesia
                    </p>
                </div>
                <div class="mt-3">
                    <h6 style="color: var(--gold);">Ikuti Kami</h6>
                    <a href="#" class="text-decoration-none me-3" style="color: var(--gold); font-size: 1.5rem;" title="Facebook">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="#" class="text-decoration-none me-3" style="color: var(--gold); font-size: 1.5rem;" title="Instagram">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="#" class="text-decoration-none me-3" style="color: var(--gold); font-size: 1.5rem;" title="Twitter">
                        <i class="bi bi-twitter"></i>
                    </a>
                    <a href="#" class="text-decoration-none" style="color: var(--gold); font-size: 1.5rem;" title="YouTube">
                        <i class="bi bi-youtube"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="border-dekoratif mt-4 pt-3">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                    <p class="mb-0">
                        &copy; <?php echo date('Y'); ?> Jogja Historia. 
                        <span class="teks-emas">❦</span> Semua Hak Dilindungi.
                    </p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <small>Dibuat dengan ❤️ untuk Yogyakarta</small>
                </div>
            </div>
        </div>
    </div>
</footer>

<button id="tombolScrollAtas" class="btn btn-traditional rounded-circle position-fixed" 
        style="bottom: 20px; right: 20px; width: 50px; height: 50px; display: none; z-index: 1000;"
        onclick="scrollKeAtas()">
    <i class="bi bi-arrow-up"></i>
</button>

<script>
window.onscroll = function() {
    var tombol = document.getElementById('tombolScrollAtas');
    if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
        tombol.style.display = "block";
    } else {
        tombol.style.display = "none";
    }
};

function scrollKeAtas() {
    window.scrollTo({top: 0, behavior: 'smooth'});
}
</script>