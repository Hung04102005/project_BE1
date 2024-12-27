<?php
session_start();
include 'config/database.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Vui lòng đăng nhập để thanh toán'
    ]);
    exit;
}

try {
    // Bắt đầu transaction
    $conn->beginTransaction();

    // Lấy thông tin giỏ hàng
    $sql = "SELECT cart.*, products.price 
            FROM cart 
            JOIN products ON cart.product_id = products.id 
            WHERE cart.user_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Tính tổng tiền
    $total_amount = 0;
    foreach ($cart_items as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }

    // Tạo đơn hàng mới
    $sql = "INSERT INTO orders (user_id, total_amount, status, created_at) 
            VALUES (:user_id, :total_amount, 'pending', NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindValue(':total_amount', $total_amount, PDO::PARAM_INT);
    $stmt->execute();
    $order_id = $conn->lastInsertId();

    // Thêm chi tiết đơn hàng
    foreach ($cart_items as $item) {
        $sql = "INSERT INTO order_details (order_id, product_id, quantity, price) 
                VALUES (:order_id, :product_id, :quantity, :price)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':order_id', $order_id, PDO::PARAM_INT);
        $stmt->bindValue(':product_id', $item['product_id'], PDO::PARAM_INT);
        $stmt->bindValue(':quantity', $item['quantity'], PDO::PARAM_INT);
        $stmt->bindValue(':price', $item['price'], PDO::PARAM_INT);
        $stmt->execute();
    }

    // Xóa giỏ hàng
    $sql = "DELETE FROM cart WHERE user_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();

    // Commit transaction
    $conn->commit();

    // Trả về phản hồi JSON
    echo json_encode([
        'status' => 'success',
        'message' => 'Đặt hàng thành công',
        'order_id' => $order_id
    ]);

} catch (PDOException $e) {
    // Rollback nếu có lỗi
    $conn->rollBack();
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi xử lý đơn hàng: ' . $e->getMessage()
    ]);
}
?>
