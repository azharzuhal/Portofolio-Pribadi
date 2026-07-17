<?php
session_start();
include 'config.php';

// Ambil data portofolio dari database
$stmt = $pdo->query("SELECT * FROM portofolio");
$portfolios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Portofolio Zuhal</title>
    <link rel="stylesheet" href="csss/indexx.css?v=<?= time(); ?>">
</head>
<body>
    <header>
        <h1>Portofolio Zuhal</h1>
        <div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="admin.php" class="admin-button">Admin</a> <!-- Tampilkan tombol Admin jika login -->
                <a href="logout.php" class="logout-button">Logout</a>
            <?php else: ?>
                <a href="login.php" class="login-button">Login Admin</a>
            <?php endif; ?>
        </div>
    </header>
    <section class="hero">
        <h2>Selamat Datang di Portofolio Saya</h2>
    </section>
    <section class="profile">
        <img src="image/fotoku.JPG" alt="Profile Image" class="profile-image">
        <div class="profile-info">
            <h3>Azhartamma Zuhal Budiazka</h3>
            <p>Mahasiswa Informatika Universitas Islam Indonesia.</p>
        </div>
    </section>
    <section class="about-me">
        <h1>Tentang Saya</h1>
        <div class="aboutme-info">
            <div>
                <h2>PROFIL</h2>
                <p>Mahasiswa Universitas Islam Indonesia angkatan 2023 jurusan Informatika Fakultas Teknologi Industri memiliki motivasi dan semangat tinggi melakukan suatu hal yang seru dan unik.</p>
            </div>
            <div>
                <h2>PENDIDIKAN</h2>
                <p>SMK Negeri 1 Jogonalan (Juli 2020 – Juni 2023)</p>
                <ul>
                    <li>Teknik Komputer dan Jaringan</li>
                </ul>
                <p>Universitas Islam Indonesia (September 2023 – Sekarang)</p>
                <ul>
                    <li>S1 Informatika</li>
                </ul>
            </div>
            <div>
                <h2>AKTIVITAS ORGANISASI</h2>
                <p>Koperasi Mahasiswa UII</p>
                <ul>
                    <li><strong>Magang Staff Hubineks (Hubungan Internal Eksternal)</strong> (2023 – 2024)
                        <ul>
                            <li>Berperan sebagai Humas di Kopma UII, membuat desain dan konten untuk sosisal media Kopma UII.</li>
                        </ul>
                    </li>
                    <li><strong>Staff Hubineks (Hubungan Internal Eksternal)</strong> (2024 – 2025)
                        <ul>
                            <li>Berperan sebagai Humas di Kopma UII dan mengelola akun sosisal media Kopma UII.</li>
                        </ul>
                    </li>
                    <li><strong>Staff Ahli Hubineks (Hubungan Internal Eksternal)</strong> (2025 – 2026)
                        <ul>
                            <li>Berperan sebagai Humas di Kopma UII dan mengelola akun sosisal media Kopma UII.</li>
                        </ul>
                    </li>
                    <li><strong>Dewan Pengawas II</strong> (2026 - Sekarang)
                        <ul>
                            <li>Mengawasi jalannya kegiatan di organisasi Koperasi Mahasiswa UII selama 1 (satu) tahun periode kepengurusan.</li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div>
                <h2>KEPANITIAAN</h2>
                <ul>
                    <li><strong>Panitia Pesona Ta'aruf UII</strong> (2024)</li>
                    <li><strong>Panitia Porsematik UII</strong> (2024)</li>
                    <li><strong>Panitia Pekan Ta'aruf FTI UII</strong> (2025)</li>
                    <li><strong>Panitia INPUT UII</strong> (2025)</li>
                </ul>
            </div>
            <div>
                <h2>SERTIFIKASI</h2>
                <ul>
                    <li><strong>Cisco Networking Academy</strong> (Februari 2026)
                        <ul>
                            <li>Junior Cybersecurity Analyst Career Path.</li>
                        </ul>
                    </li>
                    <li><strong>Badan Nasional Sertifikasi Profesi (BNSP)</strong> (November 2022 – November 2025)
                        <ul>
                            <li>Sertifikat Kompetensi – Teknik Komputer dan Jaringan (KKNI Level II).</li>
                            <li>ID Kredensial: No. 61100 2522 2 0000748 2022</li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </section>
    <section class="projects">
        <h2>Portofolio Saya</h2>
        <div class="portfolio-grid">
            <?php foreach ($portfolios as $portfolio): ?>
                <!-- Menampilkan Portofolio -->
                <div class="portfolio-item" onclick="openModal(this)">
                    <img src="<?= htmlspecialchars($portfolio['image']); ?>" alt="Image">
                    <h3><?= htmlspecialchars($portfolio['title']); ?></h3>
                    <p class="desc-preview"><?= htmlspecialchars($portfolio['description']); ?></p>
                    <div class="full-description" style="display:none;"><?= nl2br(htmlspecialchars($portfolio['description'])); ?></div>
                    <p class="portfolio-time">
                        <?= date('d M Y, H:i', strtotime($portfolio['uploaded_files'])); ?> <!-- Dibuat pada:  -->
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <footer>
        <p>© 2026 Portofolio Saya. Hak Cipta Dilindungi.</p>
        <p>Terakhir diperbarui: <?= date('d M Y, H:i'); ?> </p>
        <p>
            <a href="https://www.instagram.com/azharzuhal" target="_blank" class="social-link">Instagram</a> |
            <a href="https://github.com/azharzuhal" target="_blank" class="social-link">GitHub</a> |
            <a href="https://www.linkedin.com/in/azhartamma-zuhal-budiazka" target="_blank" class="social-link">LinkedIn</a>
        </p>
    </footer>

    <!-- Modal Portofolio -->
    <div id="portfolioModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <img id="modalImg" src="" alt="Portfolio Image">
            <h3 id="modalTitle"></h3>
            <div id="modalDesc"></div>
        </div>
    </div>

    <script>
        function openModal(element) {
            document.getElementById('modalImg').src = element.querySelector('img').src;
            document.getElementById('modalTitle').innerText = element.querySelector('h3').innerText;
            document.getElementById('modalDesc').innerHTML = element.querySelector('.full-description').innerHTML;
            
            document.getElementById('portfolioModal').style.display = "flex";
            document.body.style.overflow = "hidden"; // Mencegah scroll
        }

        function closeModal() {
            document.getElementById('portfolioModal').style.display = "none";
            document.body.style.overflow = "auto";
        }

        // Tutup modal jika user mengklik di luar modal
        window.onclick = function(event) {
            var modal = document.getElementById('portfolioModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>