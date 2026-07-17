<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) && empty($password)) {
        $error = "Mohon isi username dan password Anda!";
    } elseif (empty($username)) {
        $error = "Mohon isi username Anda!";
    } elseif (empty($password)) {
        $error = "Mohon isi password Anda!";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // Verifikasi password tanpa hashing
        if ($user && $password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            header('Location: index.php');
            exit;
        } else {
            $error = "Username atau password salah!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="csss/login.css?v=<?= time(); ?>">
</head>
<body>
    <div class="login-container">
        <header>
            <h1>Login</h1>
        </header>
        <h2>LOGIN HANYA BISA DILAKUKAN OLEH ADMIN</h2>
        <div class="form-container">
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            <form method="POST">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password">
                <button type="submit">Sign In</button>
            </form>
        </div>
    </div>
</body>
</html>
