<?php
require_once 'auth.php';
require_once '../config/database.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    
    if (!$product) {
        header('Location: products.php');
        exit();
    }
}

if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $old_price = $_POST['old_price'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_on_sale = isset($_POST['is_on_sale']) ? 1 : 0;
    $is_new = isset($_POST['is_new']) ? 1 : 0;
    
    if ($_FILES['image']['name']) {
        $image = $_FILES['image']['name'];
        $target = "../images/".basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
        
        $stmt = $conn->prepare("UPDATE products SET name=?, description=?, old_price=?, price=?, image=?, category=?, is_featured=?, is_on_sale=?, is_new=? WHERE id=?");
        $stmt->execute([$name, $description, $old_price, $price, $image, $category, $is_featured, $is_on_sale, $is_new, $id]);
    } else {
        $stmt = $conn->prepare("UPDATE products SET name=?, description=?, old_price=?, price=?, category=?, is_featured=?, is_on_sale=?, is_new=? WHERE id=?");
        $stmt->execute([$name, $description, $old_price, $price, $category, $is_featured, $is_on_sale, $is_new, $id]);
    }
    
    header('Location: products.php');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sửa sản phẩm</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="admin-container">
        <h2>Sửa sản phẩm</h2>
        
        <form method="POST" enctype="multipart/form-data" class="edit-product-form">
            <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            <textarea name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>
            <input type="number" name="old_price" value="<?php echo $product['old_price']; ?>" step="0.01" required>
            <input type="number" name="price" value="<?php echo $product['price']; ?>" step="0.01" required>
            <input type="text" name="category" value="<?php echo htmlspecialchars($product['category']); ?>" required>
            <input type="file" name="image">
            <img src="../images/<?php echo $product['image']; ?>" width="100">
            <label>
                <input type="checkbox" name="is_featured" <?php echo $product['is_featured'] ? 'checked' : ''; ?>> Nổi bật
            </label>
            <label>
                <input type="checkbox" name="is_on_sale" <?php echo $product['is_on_sale'] ? 'checked' : ''; ?>> Khuyến mãi
            </label>
            <label>
                <input type="checkbox" name="is_new" <?php echo $product['is_new'] ? 'checked' : ''; ?>> Mới
            </label>
            <button type="submit" name="update">Cập nhật</button>
        </form>
    </div>
</body>
</html>