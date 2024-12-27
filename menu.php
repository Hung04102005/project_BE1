<?php
include 'config/database.php';
session_start();

// Lấy thông tin sản phẩm
$stmt = $conn->prepare("SELECT * FROM products");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy số lượng sản phẩm yêu thích của người dùng nếu đã đăng nhập
$favorites_count = 0;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $count_sql = "SELECT COUNT(*) as total FROM favorites WHERE user_id = :user_id";
    $count_stmt = $conn->prepare($count_sql);
    $count_stmt->execute(['user_id' => $user_id]);
    $count_result = $count_stmt->fetch();
    $favorites_count = $count_result['total'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thực đơn</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="script.js"></script>
</head>
<body>
    <?php include 'header.php'; ?>

    <section class="menu" id="menu">
        <div class="menu-title">
            <h3 class="sub-heading">Thực đơn của chúng tôi</h3>
            <h1 class="main-heading">Tất cả món ăn</h1>
        </div>

        <div class="box-container">
            <?php foreach ($products as $product): ?>
                <div class="box">
                    <div class="image">
                        <img src="images/<?php echo htmlspecialchars($product['image']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <a href="javascript:void(0)" class="fas fa-heart add-to-favorites" data-product-id="<?php echo $product['id']; ?>"></a>
                    </div>
                    <div class="content">
                        <div class="stars">
                            <?php
                            $rating = $product['rating'];
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $rating) {
                                    echo '<i class="fas fa-star"></i>';
                                } elseif ($i - $rating < 1) {
                                    echo '<i class="fas fa-star-half-alt"></i>';
                                } else {
                                    echo '<i class="far fa-star"></i>';
                                }
                            }
                            ?>
                        </div>
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="product-desc">
                            <?php 
                            $description = htmlspecialchars($product['description']);
                            echo substr($description, 0, 50) . (strlen($description) > 50 ? '...' : '');
                            ?>
                        </p>
                        <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="btn">Xem Chi Tiết</a>
                        <span class="price"><?php echo number_format($product['price'], 0, ',', '.'); ?>đ</span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <footer>
        <div class="favorites-count">
            <i class="fas fa-heart"></i> <?php echo $favorites_count; ?>
        </div>
    </footer>

    <script>
        $(document).ready(function() {
    // Xử lý thêm vào yêu thích
    $('.add-to-favorites').click(function(e) {
        e.preventDefault();
        
        let $heartIcon = $(this);  // Lưu phần tử trái tim
        let product_id = $heartIcon.data('product-id');  // Lấy product_id từ data attribute của nút

        $.ajax({
            url: 'add_to_favorites.php',
            type: 'POST',
            data: {
                product_id: product_id
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Cập nhật số lượng yêu thích
                    $('.favorites-count').text(response.favorites_count);

                    // Cập nhật biểu tượng trái tim
                    if (response.action === 'added') {
                        $heartIcon.addClass('fas fa-heart').removeClass('far fa-heart');  // Thêm yêu thích
                    } else if (response.action === 'removed') {
                        $heartIcon.addClass('far fa-heart').removeClass('fas fa-heart');  // Xóa yêu thích
                    }

                    alert(response.message);  // Thông báo cho người dùng
                } else {
                    alert('Có lỗi: ' + response.message);  // Thông báo lỗi
                }
            },
            error: function(xhr, status, error) {
                console.log("Error:", xhr.responseText);
                alert('Có lỗi xảy ra khi thêm vào danh sách yêu thích!');  // Thông báo lỗi nếu AJAX thất bại
            }
        });
    });
});

    </script>

</body>
</html>
