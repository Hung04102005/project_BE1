<?php
require_once 'auth.php';
require_once '../config/database.php';

if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $old_price = $_POST['old_price'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_on_sale = isset($_POST['is_on_sale']) ? 1 : 0;
    $is_new = isset($_POST['is_new']) ? 1 : 0;
    
    $image = $_FILES['image']['name'];
    $target = "../images/".basename($image);
    move_uploaded_file($_FILES['image']['tmp_name'], $target);
    
    $stmt = $conn->prepare("INSERT INTO products (name, description, old_price, price, image, category, is_featured, is_on_sale, is_new) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $description, $old_price, $price, $image, $category, $is_featured, $is_on_sale, $is_new]);
    
    header('Location: products.php');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Thêm sản phẩm</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="admin-container">
        <div class="header-actions">
            <h2>Thêm sản phẩm</h2>
            <a href="products.php" class="btn-back">← Quay lại</a>
        </div>

        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-error">Có lỗi xảy ra khi thêm sản phẩm!</div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="add-product-form">
            <div class="form-group">
                <label>Tên sản phẩm:</label>
                <input type="text" name="name" required>
            </div>

            <div class="form-group">
                <label>Mô tả:</label>
                <textarea name="description" required></textarea>
            </div>

            <div class="form-group">
                <label>Giá cũ:</label>
                <input type="number" name="old_price" step="0.01" required>
            </div>

            <div class="form-group">
                <label>Giá mới:</label>
                <input type="number" name="price" step="0.01" required>
            </div>

            <div class="form-group">
                <label>Danh mục:</label>
                <input type="text" name="category" required>
            </div>

            <div class="form-group">
                <label>Hình ảnh:</label>
                <input type="file" name="image" required>
            </div>

            <div class="form-group">
                <label>Nổi bật:</label>
                <input type="checkbox" name="is_featured">
            </div>

            <div class="form-group">
                <label>Khuyến mãi:</label>
                <input type="checkbox" name="is_on_sale">
            </div>

            <div class="form-group">
                <label>Mới:</label>
                <input type="checkbox" name="is_new">
            </div>

            <button type="submit" name="add" class="btn-submit">Thêm sản phẩm</button>
        </form>
    </div>
</body>
</html>