<?php
session_start();
include 'config/database.php';

if(isset($_POST['cart_id'])) {
    try {
        $cart_id = $_POST['cart_id'];
        
        // Thêm điều kiện kiểm tra user_id để đảm bảo an toàn
        $sql = "DELETE FROM cart WHERE id = :cart_id AND user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        
        if($stmt->execute()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Đã xóa sản phẩm khỏi giỏ hàng'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Không thể xóa sản phẩm'
            ]);
        }
    } catch(PDOException $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Lỗi database: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Thiếu thông tin cart_id'
    ]);
}
?> 