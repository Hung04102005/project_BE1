<?php
session_start();
include 'config/database.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Lấy sản phẩm yêu thích của user hiện tại
$sql = "SELECT favorites.*, products.name, products.price, products.image 
        FROM favorites 
        JOIN products ON favorites.product_id = products.id
        WHERE favorites.user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$favorites_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Xử lý xóa sản phẩm khỏi yêu thích
if (isset($_GET['remove_id'])) {
    $remove_id = $_GET['remove_id'];

    // Xóa khỏi cơ sở dữ liệu
    $delete_stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = :user_id AND product_id = :product_id");
    $delete_stmt->execute(['user_id' => $user_id, 'product_id' => $remove_id]);

    // Redirect lại trang yêu thích
    header('Location: favorites.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Danh sách yêu thích</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include 'header.php'; ?>

    <button onclick="history.back()" class="back-btn">
        <i class="fas fa-arrow-left"></i> Quay lại
    </button>

    <div class="favorites-container">
        <h1 class="favorites-title">Danh sách yêu thích của bạn</h1>

        <?php if (!empty($favorites_items)): ?>
            <div class="favorites-items">
                <?php foreach ($favorites_items as $item): ?>
                    <div class="favorites-item">
                        <div class="item-image">
                            <img src="images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        </div>
                        <div class="item-info">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <span class="price"><?php echo number_format($item['price'], 0, ',', '.'); ?>đ</span>
                            <a href="product-detail.php?id=<?php echo $item['product_id']; ?>" class="btn">Xem Chi Tiết</a>
                            <!-- Thêm nút Xóa khỏi yêu thích -->
                            <a href="favorites.php?remove_id=<?php echo $item['product_id']; ?>" class="remove-favorite-btn">
                                <i class="fas fa-trash-alt"></i> Xóa khỏi yêu thích
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Danh sách yêu thích của bạn đang trống.</p>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>

<style>
    
    .favorites-container {
        padding: 40px;
        max-width: 1000px;
        margin: 20px auto;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
    }

    .favorites-title {
        font-size: 30px;
        font-weight: 700;
        text-align: center;
        color: #444;
        margin-bottom: 40px;
        text-transform: uppercase;
        letter-spacing: 1.5px;
    }

    .favorites-items {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
        gap: 20px;
    }

    .favorites-item {
        background: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 12px;
        overflow: hidden;
        transition: box-shadow 0.3s ease, transform 0.3s ease;
        display: flex;
        gap: 15px;
        align-items: center;
        padding: 15px;
    }

    .favorites-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .item-image img {
        width: 150px;
        /* Giảm chiều ngang của ảnh */
        height: 150px;
        /* Giữ tỷ lệ cân đối */
        border-radius: 10px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .item-image img:hover {
        transform: scale(1.05);
    }

    .item-info {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
    }

    .item-info h3 {
        font-size: 20px;
        font-weight: 600;
        margin: 10px 0;
        color: #333;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }

    .price {
        color: #ff5722;
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .btn {
        display: inline-block;
        padding: 8px 15px;
        margin-top: 10px;
        background-color: #28a745;
        color: white;
        border-radius: 5px;
        font-size: 14px;
        font-weight: 500;
        text-align: center;
        text-decoration: none;
        transition: background-color 0.3s ease;
        width: 150px; /* Đảm bảo nút không quá dài */
    }

    .btn:hover {
        background-color: #218838;
    }

    .remove-favorite-btn {
        margin-top: 10px;
        display: inline-block;
        color: #e74c3c;
        font-size: 14px;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s ease;
    }

    .remove-favorite-btn:hover {
        color: #c0392b;
        text-decoration: underline;
    }

    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #ff5722;
        color: white;
        padding: 10px 20px;
        border: none;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        border-radius: 5px;
        margin-bottom: 20px;
        transition: background-color 0.3s ease;
    }

    .back-btn:hover {
        background-color: #e64a19;
    }
</style>