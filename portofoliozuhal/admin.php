<?php
session_start();
include 'config.php';

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?error=unauthorized');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    $hasImage = isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK;

    if (empty($title) && empty($description) && !$hasImage) {
        $error = "Mohon isi judul, deskripsi, dan unggah foto portofolio!";
    } elseif (empty($title)) {
        $error = "Mohon isi judul portofolio!";
    } elseif (empty($description)) {
        $error = "Mohon isi deskripsi portofolio!";
    } elseif (!$hasImage) {
        $error = "Mohon unggah foto portofolio!";
    } else {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = './uploaded_files/';
            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $stmt = $pdo->prepare("INSERT INTO portofolio (title, description, image) VALUES (?, ?, ?)");
                $stmt->execute([$title, $description, $dest_path]);
                header('Location: listportofolio.php');
                exit;
            } else {
                $error = "Error saat memindahkan file.";
            }
        } else {
            $error = "Format file tidak didukung. Hanya file: " . implode(', ', $allowedfileExtensions);
        }
    }
}

$portfolios = $pdo->query("SELECT * FROM portofolio")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <link rel="stylesheet" href="csss/admin.css?v=<?= time(); ?>">
</head>
<body>
    <header>
        <h1>Admin Panel</h1>
        <a href="logout.php">Logout</a>
        <a href="listportofolio.php">List Portofolio</a>
        <a href="index.php">Beranda</a>
    </header>
    <div class="container">
        <form method="POST" enctype="multipart/form-data">
            <h2>Add New Portfolio</h2>
            
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" placeholder="Enter portfolio title" value="<?= isset($title) ? htmlspecialchars($title) : '' ?>">
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" placeholder="Enter portfolio description"><?= isset($description) ? htmlspecialchars($description) : '' ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="image">Cover Image</label>
                <input type="file" id="image" name="image" accept="image/jpeg, image/png, image/jpg, image/webp">
            </div>
            
            <button type="submit">Upload Portfolio</button>
        </form>
        
    </div>
</body>
</html>
