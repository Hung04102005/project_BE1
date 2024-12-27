<?php
include 'config/database.php';
session_start();

// Kiểm tra người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Vui lòng đăng nhập để thêm vào danh sách yêu thích.'
    ]);
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'];  // Lấy product_id từ yêu cầu POST

try {
    // Kiểm tra sản phẩm đã có trong danh sách yêu thích chưa
    $check_sql = "SELECT * FROM favorites WHERE product_id = :product_id AND user_id = :user_id";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->execute([
        'product_id' => $product_id,
        'user_id' => $user_id
    ]);
    $existing_item = $check_stmt->fetch();

    if ($existing_item) {
        // Nếu sản phẩm đã có trong yêu thích, xóa nó
        $delete_sql = "DELETE FROM favorites WHERE product_id = :product_id AND user_id = :user_id";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->execute([
            'product_id' => $product_id,
            'user_id' => $user_id
        ]);

        // Trả về số lượng yêu thích mới và thông báo
        $count_sql = "SELECT COUNT(*) as total FROM favorites WHERE user_id = :user_id";
        $count_stmt = $conn->prepare($count_sql);
        $count_stmt->execute(['user_id' => $user_id]);
        $count_result = $count_stmt->fetch();

        echo json_encode([
            'status' => 'success',
            'message' => 'Sản phẩm đã được xóa khỏi danh sách yêu thích.',
            'favorites_count' => $count_result['total'] ?? 0,
            'action' => 'removed'
        ]);
    } else {
        // Thêm sản phẩm vào danh sách yêu thích
        $sql = "INSERT INTO favorites (user_id, product_id) VALUES (:user_id, :product_id)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'user_id' => $user_id,
            'product_id' => $product_id
        ]);

        // Trả về số lượng yêu thích mới và thông báo
        $count_sql = "SELECT COUNT(*) as total FROM favorites WHERE user_id = :user_id";
        $count_stmt = $conn->prepare($count_sql);
        $count_stmt->execute(['user_id' => $user_id]);
        $count_result = $count_stmt->fetch();

        echo json_encode([
            'status' => 'success',
            'message' => 'Đã thêm sản phẩm vào danh sách yêu thích.',
            'favorites_count' => $count_result['total'] ?? 0,
            'action' => 'added'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
