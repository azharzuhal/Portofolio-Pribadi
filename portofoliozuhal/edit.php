<?php
session_start();
include 'config.php';

// Cek jika pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?error=unauthorized');
    exit;
}

// Cek jika ada ID portofolio yang diberikan
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil data portofolio dari database
    $stmt = $pdo->prepare("SELECT * FROM portofolio WHERE id = ?");
    $stmt->execute([$id]);
    $portfolio = $stmt->fetch();

    if (!$portfolio) {
        echo "Portofolio tidak ditemukan.";
        exit;
    }
}

// Proses pengeditan portofolio
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($title) && empty($description)) {
        $error = "Mohon isi judul dan deskripsi portofolio!";
    } elseif (empty($title)) {
        $error = "Mohon isi judul portofolio!";
    } elseif (empty($description)) {
        $error = "Mohon isi deskripsi portofolio!";
    } else {
        // Cek apakah ada file gambar baru yang diupload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['image']['tmp_name'];
            $fileName = $_FILES['image']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($fileExtension, $allowedfileExtensions)) {
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $dest_path = './uploaded_files/' . $newFileName;

                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    // Hapus gambar lama dari server
                    if ($portfolio['image'] && file_exists($portfolio['image'])) {
                        unlink($portfolio['image']);
                    }
                    
                    // Update database dengan gambar baru
                    $stmt = $pdo->prepare("UPDATE portofolio SET title = ?, description = ?, image = ? WHERE id = ?");
                    $stmt->execute([$title, $description, $dest_path, $id]);
                    
                    header('Location: listportofolio.php?t=' . time());
                    exit;
                } else {
                    $error = "Error saat mengunggah gambar.";
                }
            } else {
                $error = "Format file tidak didukung.";
            }
        } else {
            // Update database tanpa mengubah gambar
            $stmt = $pdo->prepare("UPDATE portofolio SET title = ?, description = ? WHERE id = ?");
            $stmt->execute([$title, $description, $id]);
            
            header('Location: listportofolio.php?t=' . time());
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Portofolio</title>
    <link rel="stylesheet" href="csss/admin.css?v=<?= time(); ?>">
</head>
<body>
    <header>
        <h1>Edit Portofolio</h1>
        <a href="listportofolio.php">Kembali</a>
    </header>
    <div class="container">
        <form method="POST" enctype="multipart/form-data">
            <h2>Ubah Data Portofolio</h2>
            
            <?php if (isset($error)) echo "<p style='color: #fb7185; text-align: center; margin-bottom: 1rem;'>$error</p>"; ?>

            <div class="form-group">
                <label for="title">Judul Portofolio</label>
                <input type="text" name="title" id="title" value="<?= htmlspecialchars($_POST['title'] ?? $portfolio['title']); ?>">
            </div>

            <div class="form-group">
                <label for="description">Deskripsi Portofolio</label>
                <textarea name="description" id="description"><?= htmlspecialchars($_POST['description'] ?? $portfolio['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label>Foto Saat Ini:</label>
                <img src="<?= htmlspecialchars($portfolio['image']); ?>" style="max-height: 150px; border-radius: 8px; margin-bottom: 10px; display: block; border: 1px solid var(--border-color); object-fit: cover;">
                <label for="image">Ganti Foto Baru (Kosongkan jika tidak ingin mengubah)</label>
                <input type="file" id="image" name="image" accept="image/jpeg, image/png, image/jpg, image/webp">
            </div>

            <button type="submit">Simpan Perubahan</button>
        </form>
    </div>
</body>
</html> 