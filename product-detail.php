<?php
include 'config/database.php';
session_start();

// Lấy ID sản phẩm từ URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Lấy thông tin sản phẩm
$stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute(['id' => $product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// Lấy từ khóa từ tên sản phẩm
$keywords = explode(' ', $product['name']);
$keywords = array_map('trim', $keywords);
$keywords = array_filter($keywords);

// Tạo câu truy vấn để tìm các sản phẩm liên quan
$related_sql = "SELECT * FROM products WHERE id != :id AND (";
foreach ($keywords as $index => $keyword) {
    $related_sql .= "name LIKE :keyword$index";
    if ($index < count($keywords) - 1) {
        $related_sql .= " OR ";
    }
}
$related_sql .= ") LIMIT 6";

$related_stmt = $conn->prepare($related_sql);
$related_stmt->bindValue(':id', $product_id, PDO::PARAM_INT);
foreach ($keywords as $index => $keyword) {
    $related_stmt->bindValue(":keyword$index", "%$keyword%", PDO::PARAM_STR);
}
$related_stmt->execute();
$related_products = $related_stmt->fetchAll(PDO::FETCH_ASSOC);

// Xử lý bình luận và đánh giá
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'])) {
    $user_id = $_SESSION['user_id'];
    $comment = htmlspecialchars($_POST['comment']);
    $rating = (int)$_POST['rating'];

    $stmt = $conn->prepare("INSERT INTO reviews (product_id, user_id, comment, rating) VALUES (:product_id, :user_id, :comment, :rating)");
    $stmt->execute([
        'product_id' => $product_id,
        'user_id' => $user_id,
        'comment' => $comment,
        'rating' => $rating
    ]);
}

// Lấy danh sách bình luận và đánh giá
$reviews_stmt = $conn->prepare("SELECT r.*, u.username FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = :product_id ORDER BY r.created_at DESC");
$reviews_stmt->execute(['product_id' => $product_id]);
$reviews = $reviews_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Chi tiết sản phẩm</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <button onclick="history.back()" class="back-btn">
        <i class="fas fa-arrow-left"></i> Quay lại
    </button>

    <section class="product-detail">
        <div class="product-container">
            <div class="product-image">
                <img src="images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
            <div class="product-info">
                <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
                <div class="product-rating">
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

                <div class="product-price">
                    <?php echo number_format($product['price'], 0, ',', '.'); ?>đ
                </div>

                <p class="product-description">
                    <?php echo htmlspecialchars($product['description']); ?>
                </p>

                <div class="quantity-control">
                    <span>Số lượng:</span>
                    <button class="quantity-btn" onclick="decreaseQuantity()">-</button>
                    <input type="number" id="quantity" value="1" min="1" class="quantity-input">
                    <button class="quantity-btn" onclick="increaseQuantity()">+</button>
                </div>

                <button class="add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>">
                    <i class="fas fa-shopping-cart"></i> Thêm vào giỏ hàng
                </button>
            </div>
        </div>
    </section>

    <section class="reviews">
        <h2>Bình luận và đánh giá</h2>
        <?php if (isset($_SESSION['user_id'])): ?>
            <form method="POST" class="review-form">
                <textarea name="comment" placeholder="Viết bình luận của bạn..." required></textarea>
                <div class="rating">
                    <span>Đánh giá:</span>
                    <select name="rating" required>
                        <option value="5">5 sao</option>
                        <option value="4">4 sao</option>
                        <option value="3">3 sao</option>
                        <option value="2">2 sao</option>
                        <option value="1">1 sao</option>
                    </select>
                </div>
                <button type="submit" class="btn">Gửi</button>
            </form>
        <?php else: ?>
            <p>Vui lòng <a href="login.php">đăng nhập</a> để bình luận và đánh giá.</p>
        <?php endif; ?>

        <div class="review-list">
            <?php foreach ($reviews as $review): ?>
                <div class="review">
                    <div class="review-header">
                        <span class="username"><?php echo htmlspecialchars($review['username']); ?></span>
                        <span class="rating">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <?php if ($i <= $review['rating']): ?>
                                    <i class="fas fa-star"></i>
                                <?php else: ?>
                                    <i class="far fa-star"></i>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </span>
                    </div>
                    <p class="comment"><?php echo htmlspecialchars($review['comment']); ?></p>
                    <span class="date"><?php echo date('d/m/Y H:i', strtotime($review['created_at'])); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="related-products">
        <h2>Sản phẩm liên quan</h2>
        <div class="box-container">
            <?php foreach ($related_products as $related_product): ?>
                <div class="box">
                    <div class="image">
                        <img src="images/<?php echo htmlspecialchars($related_product['image']); ?>" alt="<?php echo htmlspecialchars($related_product['name']); ?>">
                        <a href="#" class="fas fa-heart add-to-favorites" data-product-id="<?php echo $related_product['id']; ?>"></a>
                    </div>
                    <div class="content">
                        <h3><?php echo htmlspecialchars($related_product['name']); ?></h3>
                        <p class="product-desc">
                            <?php
                            $description = htmlspecialchars($related_product['description']);
                            echo substr($description, 0, 50) . (strlen($description) > 50 ? '...' : '');
                            ?>
                        </p>
                        <a href="product-detail.php?id=<?php echo $related_product['id']; ?>" class="btn">Xem Chi Tiết</a>
                        <span class="price"><?php echo number_format($related_product['price'], 0, ',', '.'); ?>đ</span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <?php include 'footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="script.js"></script>
    <script>
        $(document).ready(function() {
            $('.add-to-cart-btn').click(function(e) {
                e.preventDefault();
                
                let product_id = $(this).data('product-id');
                let quantity = $('#quantity').val();

                // Kiểm tra trạng thái đăng nhập
                <?php if (!isset($_SESSION['user_id'])): ?>
                    window.location.href = 'user/user_login.php';
                <?php else: ?>
                    $.ajax({
                        url: 'add_to_cart.php',
                        type: 'POST',
                        data: {
                            product_id: product_id,
                            quantity: quantity
                        },
                        dataType: 'json',
                        success: function(response) {
                            console.log("Success response:", response);
                            if (response.status === 'success') {
                                $('.cart-count').text(response.cart_count);
                                alert('Đã thêm sản phẩm vào giỏ hàng!');
                            } else {
                                alert('Có lỗi: ' + response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log("Error:", xhr.responseText);
                            alert('Có lỗi xảy ra khi thêm vào giỏ hàng!');
                        }
                    });
                <?php endif; ?>
            });
        });

        function decreaseQuantity() {
            let input = document.getElementById('quantity');
            if(input.value > 1) {
                input.value = parseInt(input.value) - 1;
            }
        }

        function increaseQuantity() {
            let input = document.getElementById('quantity');
            input.value = parseInt(input.value) + 1;
        }
    </script>
    <style>
        /* Phần chi tiết món ăn */
        .product-detail {
            margin-bottom: 10px;
            /* Giảm khoảng cách dưới của phần món ăn */
        }

        /* Phần bình luận và đánh giá */
        .reviews {
            margin-top: 10px;
            /* Giảm khoảng cách với phần trên */
            padding: 15px;
            background-color: #f9f9f9;
            /* Màu nền nhẹ để phân tách */
            border: 1px solid #ddd;
            /* Viền nhạt để tạo khung */
            border-radius: 8px;
            /* Bo tròn góc */
            width: 80%;
            /* Căn chỉnh chiều rộng */
            margin-left: auto;
            margin-right: auto;
        }

        .reviews h2 {
            font-size: 24px;
            font-weight: bold;
            text-align: left;
            margin-bottom: 15px;
        }

        /* Form thêm bình luận */
        .review-form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            /* Khoảng cách giữa các phần tử */
            margin-bottom: 20px;
        }

        .review-form textarea {
            width: 100%;
            min-height: 80px;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .review-form .rating {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .review-form .rating select {
            padding: 5px;
            font-size: 14px;
        }

        .review-form .btn {
            background-color: #e74c3c;
            /* Màu đỏ tươi */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .review-form .btn:hover {
            background-color: #c0392b;
            /* Màu đậm hơn khi hover */
        }

        /* Danh sách các bình luận */
        .review-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
            /* Khoảng cách giữa các bình luận */
        }

        .review {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
        }

        .review .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .review .username {
            font-weight: bold;
            font-size: 16px;
        }

        .review .rating i {
            color: #f39c12;
            /* Màu vàng cho sao */
        }

        .review .comment {
            margin-bottom: 10px;
            font-size: 14px;
        }

        .review .date {
            font-size: 12px;
            color: #999;
        }
    </style>
</body>
</html>