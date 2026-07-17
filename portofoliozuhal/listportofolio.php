<?php
session_start();
include 'config.php';

// Debugging: Check if the table exists
// memeriksa apakah tabel bernama portofolio ada di dalam database
try {
    $result = $pdo->query("SHOW TABLES LIKE 'portofolio'");
    if ($result->rowCount() == 0) {
        echo "Table 'portofolio' does not exist.";
    }
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}

// Cek jika ada permintaan untuk menghapus portofolio
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    try {
        // Ambil path gambar lama
        $stmt_img = $pdo->prepare("SELECT image FROM portofolio WHERE id = ?");
        $stmt_img->execute([$delete_id]);
        $old_image = $stmt_img->fetchColumn();

        // Hapus file gambar dari server jika ada
        if ($old_image && file_exists($old_image)) {
            unlink($old_image);
        }

        $stmt = $pdo->prepare("DELETE FROM portofolio WHERE id = ?");
        $stmt->execute([$delete_id]);
        header('Location: listportofolio.php'); // Redirect setelah menghapus
        exit;
    } catch (PDOException $e) {
        echo 'Error deleting portfolio: ' . $e->getMessage();
    }
}

// Ambil data portofolio dari database
try {
    $stmt = $pdo->query("SELECT * FROM portofolio");
    $portfolios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Error fetching portfolios: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Portofolio</title>
    <link rel="stylesheet" href="csss/listportofolio.css?v=<?= time(); ?>">
</head>
<body>
    <header>
        <h1>Daftar Portofolio</h1>
        <nav>
            <a href="admin.php">Tambah Portofolio</a>
            <a href="index.php">Kembali ke Beranda</a>
        </nav>
    </header>
    <div class="container">
        <?php if (count($portfolios) > 0): ?>
            <ul>
                <?php foreach ($portfolios as $portfolio): ?>
                    <li>
                        <!-- Menampilkan list portofolio -->
                        <h2><?= htmlspecialchars($portfolio['title']); ?></h2>
                        <p><?= htmlspecialchars($portfolio['description']); ?></p>
                        <img src="<?= htmlspecialchars($portfolio['image']); ?>?t=<?= time(); ?>" alt="Image">
                        <a href="listportofolio.php?delete_id=<?= $portfolio['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus portofolio ini?');" class="delete-button">Hapus</a>
                        <a href="edit.php?id=<?= $portfolio['id']; ?>" class="edit-button">Edit</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <!-- Tidak ada portofolio -->
            <p>Tidak ada portofolio yang ditambahkan.</p>
        <?php endif; ?>
    </div>
</body>
</html>
