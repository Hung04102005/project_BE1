<?php
session_start();
include 'config/database.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Lấy sản phẩm trong giỏ hàng của user hiện tại
$sql = "SELECT cart.*, products.name, products.price, products.image 
        FROM cart 
        JOIN products ON cart.product_id = products.id
        WHERE cart.user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tính tổng tiền
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giỏ hàng</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <button onclick="history.back()" class="back-btn">
        <i class="fas fa-arrow-left"></i> Quay lại
    </button>

    <div class="cart-container">
        <h1 class="cart-title">Giỏ hàng của bạn</h1>
        
        <?php if (!empty($cart_items)): ?>
            <div class="cart-items">
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item" data-price="<?php echo $item['price']; ?>">
                        <div class="item-image">
                            <img src="images/<?php echo htmlspecialchars($item['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($item['name']); ?>">
                        </div>
                        <div class="item-details">
                            <h3 class="item-name"><?php echo htmlspecialchars($item['name']); ?></h3>
                            <div class="item-price"><?php echo number_format($item['price'], 0, ',', '.'); ?>đ</div>
                            <div class="quantity-control">
                                <button class="quantity-decrease">-</button>
                                <input type="number" 
                                       class="quantity-input" 
                                       value="<?php echo $item['quantity']; ?>"
                                       min="1" 
                                       max="99"
                                       readonly>
                                <button class="quantity-increase">+</button>
                            </div>
                            <div class="item-total">
                                Thành tiền: <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>đ
                            </div>
                            <button class="delete-btn" data-cart-id="<?php echo $item['id']; ?>" onclick="deleteCartItem(<?php echo $item['id']; ?>)">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="cart-summary">
                <div class="summary-row">
                    <span>Tổng tiền:</span>
                    <span class="cart-total-amount"><?php echo number_format($total, 0, ',', '.'); ?>đ</span>
                </div>
                <button class="checkout-btn" onclick="processCheckout()">
                    <i class="fas fa-shopping-cart"></i>
                    Thanh toán
                </button>
            </div>
        <?php else: ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <p>Giỏ hàng của bạn đang trống</p>
                <a href="index.php" class="continue-shopping">Tiếp tục mua sắm</a>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="script.js"></script>
</body>
</html> 