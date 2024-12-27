<?php
session_start();
include 'config/database.php';  // Thêm kết nối database

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Cảm ơn bạn đã đặt hàng</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <?php include 'header.php'; ?>

    <main class="container">
        <div class="thank-you-container">
            <div class="thank-you-content">
                <i class="fas fa-check-circle success-icon"></i>
                <h1>Đơn hàng của bạn đã được xác nhận</h1>
                <p>Chúng tôi sẽ liên hệ với bạn trong thời gian sớm nhất.</p>
                <div class="buttons">
                    <a href="index.php" class="continue-shopping">
                        <i class="fas fa-shopping-cart"></i>
                        Tiếp tục mua sắm
                    </a>
                </div>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>

</html>