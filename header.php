<?php
include 'config/database.php';

// Kiểm tra session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Lấy thông tin cấu hình
$stmt = $conn->prepare("SELECT * FROM config");
$stmt->execute();
$configs = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Lấy số lượng sản phẩm yêu thích
$favorites_count = 0;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    try {
        $favorites_stmt = $conn->prepare("SELECT COUNT(*) as total FROM favorites WHERE user_id = :user_id");
        $favorites_stmt->execute(['user_id' => $user_id]);
        $favorites_count = $favorites_stmt->fetch()['total'];
    } catch (PDOException $e) {
        // Xử lý khi bảng favorites không tồn tại
        $favorites_count = 0;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="script.js"></script>
    <style>
        .logo-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .shop-name {
            font-size: 20px;
            font-weight: bold;
            color: rgb(228, 76, 182);
            text-transform: uppercase;
            font-family: 'Arial', sans-serif;
            margin: 0;
        }

        .slider {
            position: relative;
            width: 100%;
            max-width: 800px;
            margin: 20px auto;
            overflow: hidden;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .slide {
            display: none;
        }

        .slide img {
            width: 100%;
            height: auto;
            display: block;
        }

        .favorites-icon {
            position: relative;
            margin-left: 20px;
        }

        .favorites-count {
            position: absolute;
            top: -10px;
            right: -10px;
            background: red;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
        }

        .cart-icon {
            position: relative;
            margin-left: 20px;
        }

        .cart-count {
            position: absolute;
            top: -10px;
            right: -10px;
            background: green;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
        }

        .login-btn {
            margin-left: 20px;
            font-size: 16px;
            color: #444;
            text-decoration: none;
        }

        .logout-btn {
            margin-left: 20px;
            font-size: 16px;
            color: #444;
            text-decoration: none;
        }

        .dropdown {
            display: none;
            position: absolute;
            top: 50px;
            right: 10px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .dropdown a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: #444;
        }

        .dropdown a:hover {
            background-color: #f1f1f1;
        }

        .dropdown-btn {
            display: none;
            font-size: 24px;
            cursor: pointer;
        }

        @media (max-width: 1350px) and (max-height: 953px) {
            .navbar a,
            .cart-icon,
            .favorites-icon,
            .logout-btn {
                display: none;
            }

            .dropdown-btn {
                display: block;
            }

            .dropdown {
                display: block;
            }
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="logo-container">
            <a href="index.php" class="logo">
                <img src="images/<?php echo htmlspecialchars($configs['logo']); ?>" alt="Logo">
            </a>
            <span class="shop-name">Hung Food Store</span>
        </div>

        <nav class="navbar">
            <a href="index.php">Trang chủ</a>
            <a href="menu.php">Thực đơn</a>
            <a href="about.php">Giới thiệu</a>
            <a href="contact.php">Liên hệ</a>
            <div class="search-container">
                <form class="search-form" action="search.php" method="GET">
                    <input type="text" name="keyword" class="search-box" placeholder="Tìm kiếm món ăn..." required>
                    <button type="submit" class="search-submit">Tìm kiếm</button>
                </form>

                <?php
                $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
                $cart_count = 0;

                if ($user_id) {
                    $count_sql = "SELECT SUM(quantity) as total FROM cart WHERE user_id = :user_id";
                    $count_stmt = $conn->prepare($count_sql);
                    $count_stmt->execute(['user_id' => $user_id]);
                    $count_result = $count_stmt->fetch();
                    $cart_count = $count_result['total'] ?? 0;
                ?>
                    <a href="cart.php" class="cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count"><?php echo $cart_count; ?></span>
                    </a>
                    <a href="favorites.php" class="favorites-icon">
                        <i class="fas fa-heart"></i>
                        <span class="favorites-count"><?php echo $favorites_count; ?></span>
                    </a>
                    <a href="user/user_logout.php" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Đăng xuất
                    </a>
                <?php } ?>
            </div>
        </nav>

        <div class="dropdown-btn" onclick="toggleDropdown()">
            <i class="fas fa-bars"></i>
        </div>
        <div class="dropdown" id="dropdown-menu">
            <a href="index.php">Trang chủ</a>
            <a href="menu.php">Thực đơn</a>
            <a href="about.php">Giới thiệu</a>
            <a href="contact.php">Liên hệ</a>
            <a href="cart.php" class="cart-icon">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count"><?php echo $cart_count; ?></span>
            </a>
            <a href="favorites.php" class="favorites-icon">
                <i class="fas fa-heart"></i>
                <span class="favorites-count"><?php echo $favorites_count; ?></span>
            </a>
            <a href="user/user_logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Đăng xuất
            </a>
        </div>
    </header>

    <script>
        function toggleDropdown() {
            var dropdown = document.getElementById("dropdown-menu");
            if (dropdown.style.display === "block") {
                dropdown.style.display = "none";
            } else {
                dropdown.style.display = "block";
            }
        }
    </script>
</body>

</html>
