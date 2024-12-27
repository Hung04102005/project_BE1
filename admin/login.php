<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && $user['role'] === 'admin') {
        if (password_verify($password, $user['password'])) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            header('Location: products.php');
            exit();
        }
    }
    $error = "Sai tên đăng nhập hoặc mật khẩu!";
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>
    <div class="login-container">
        <h2>Đăng nhập Admin</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Tên đăng nhập" required>
            <input type="password" name="password" placeholder="Mật khẩu" required>
            <button type="submit">Đăng nhập</button>
        </form>
    </div>     
    <style>
    /* Toàn bộ body */
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        background: #f4f4f4;
    }

    /* Container login */
    .login-container {
        width: 100%;
        max-width: 400px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        padding: 30px;
        box-sizing: border-box;
        text-align: center;
    }

    /* Tiêu đề */
    .login-container h2 {
        margin-bottom: 20px;
        font-size: 24px;
        color: #333;
    }

    /* Input và nút */
    .login-container input {
        width: 100%;
        padding: 12px 15px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
        box-sizing: border-box;
        transition: border-color 0.3s;
    }

    .login-container input:focus {
        border-color: #007bff;
        outline: none;
    }

    .login-container button {
        width: 100%;
        padding: 12px 15px;
        background: #007bff;
        color: #fff;
        font-size: 16px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .login-container button:hover {
        background: #0056b3;
    }

    /* Thông báo lỗi */
    .login-container .error {
        color: #e74c3c;
        background: #fdecea;
        border: 1px solid #e74c3c;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 15px;
    }
</style>
  
</body>

</html>