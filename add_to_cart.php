<?php
session_start();
include 'config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    
    // Nếu chưa đăng nhập, tạo một session_id tạm thời
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : session_id();
    
    try {
        // Debug
        error_log("Product ID: " . $product_id);
        error_log("Quantity: " . $quantity);
        error_log("User ID: " . $user_id);
        
        // Kiểm tra sản phẩm đã có trong giỏ hàng chưa
        $check_sql = "SELECT * FROM cart WHERE product_id = :product_id AND user_id = :user_id";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->execute([
            'product_id' => $product_id,
            'user_id' => $user_id
        ]);
        $existing_item = $check_stmt->fetch();

        if ($existing_item) {
            // Nếu đã có, cập nhật số lượng
            $sql = "UPDATE cart SET quantity = quantity + :quantity 
                   WHERE product_id = :product_id AND user_id = :user_id";
        } else {
            // Nếu chưa có, thêm mới
            $sql = "INSERT INTO cart (user_id, product_id, quantity) 
                   VALUES (:user_id, :product_id, :quantity)";
        }

        $stmt = $conn->prepare($sql);
        $result = $stmt->execute([
            'user_id' => $user_id,
            'product_id' => $product_id,
            'quantity' => $quantity
        ]);

        // Debug
        error_log("SQL Result: " . ($result ? 'Success' : 'Failed'));

        // Lấy tổng số sản phẩm trong giỏ hàng
        $count_sql = "SELECT SUM(quantity) as total FROM cart WHERE user_id = :user_id";
        $count_stmt = $conn->prepare($count_sql);
        $count_stmt->execute(['user_id' => $user_id]);
        $count_result = $count_stmt->fetch();
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Đã thêm vào giỏ hàng',
            'cart_count' => $count_result['total'] ?? 0,
            'debug' => [
                'user_id' => $user_id,
                'product_id' => $product_id,
                'quantity' => $quantity
            ]
        ]);
    } catch(PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
} 