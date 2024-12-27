<?php
include '../config/database.php';
session_start();

// Kiểm tra quyền truy cập của admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Xử lý xóa sản phẩm
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Lấy thông tin ảnh để xóa file
    $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    
    // Xóa file ảnh
    if ($product && $product['image']) {
        $image_path = "../images/" . $product['image'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    
    // Xóa sản phẩm từ database
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    if ($stmt->execute([$id])) {
        // Chuyển hướng với thông báo thành công
        header("Location: products.php?success=delete");
        exit();
    } else {
        // Chuyển hướng với thông báo lỗi
        header("Location: products.php?error=delete");
        exit();
    }
}

// Hiển thị thông báo
if (isset($_GET['success']) && $_GET['success'] == 'delete') {
    $message = "Xóa sản phẩm thành công!";
    $message_type = "success";
} elseif (isset($_GET['error']) && $_GET['error'] == 'delete') {
    $message = "Có lỗi xảy ra khi xóa sản phẩm!";
    $message_type = "error";
}

// Xử lý tìm kiếm
$search_keyword = isset($_GET['search']) ? trim($_GET['search']) : '';

// Xử lý phân trang
$items_per_page = $search_keyword ? 3 : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page-1) * $items_per_page;

// Lấy tổng số sản phẩm
$total_query = "SELECT COUNT(*) as total FROM products WHERE name LIKE :keyword";
$total_stmt = $conn->prepare($total_query);
$total_stmt->bindValue(':keyword', "%$search_keyword%", PDO::PARAM_STR);
$total_stmt->execute();
$total_rows = $total_stmt->fetch()['total'];
$total_pages = ceil($total_rows / $items_per_page);

// Lấy danh sách sản phẩm theo trang
$stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE :keyword ORDER BY id DESC LIMIT :start_from, :items_per_page");
$stmt->bindValue(':keyword', "%$search_keyword%", PDO::PARAM_STR);
$stmt->bindValue(':start_from', $start_from, PDO::PARAM_INT);
$stmt->bindValue(':items_per_page', $items_per_page, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quản lý sản phẩm</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-form {
            display: flex;
            align-items: center;
        }

        .search-form input {
            padding: 5px;
            margin-right: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        table th, table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
            border-right: 1px solid #ddd; /* Thêm đường kẻ phân cách giữa các cột */
        }

        table th {
            background-color: #007bff;
            color: #fff;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        table tr:hover {
            background-color: #ddd;
        }

        table tr:last-child td {
            border-bottom: none;
        }

        table th:last-child, table td:last-child {
            border-right: none; /* Loại bỏ đường kẻ phân cách ở cột cuối cùng */
        }

        .actions {
            display: flex;
            gap: 10px;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 5px;
        }

        .pagination a {
            padding: 5px 10px;
            border: 1px solid #ddd;
            text-decoration: none;
            color: #333;
            border-radius: 5px;
        }

        .pagination a.active {
            background-color: #007bff;
            color: #fff;
        }

        .pagination a:hover {
            background-color: #ddd;
        }

        .product-description {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Hiển thị thông báo -->
        <?php if (isset($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="header-actions">
            <h2>Quản lý sản phẩm</h2>
            <a href="add_product.php" class="btn-add">+ Thêm sản phẩm mới</a>
        </div>

        <!-- Form tìm kiếm -->
        <form method="GET" class="search-form">
            <input type="text" name="search" placeholder="Tìm kiếm sản phẩm..." value="<?php echo htmlspecialchars($search_keyword); ?>">
            <button type="submit" class="btn-search"><i class="fas fa-search"></i></button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Hình ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Mô tả</th>
                    <th>Giá cũ</th>
                    <th>Giá mới</th>
                    <th>Danh mục</th>
                    <th>Nổi bật</th>
                    <th>Khuyến mãi</th>
                    <th>Mới</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td><img src="../images/<?php echo htmlspecialchars($product['image']); ?>" class="product-image" alt="<?php echo htmlspecialchars($product['name']); ?>"></td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td class="product-description"><?php echo htmlspecialchars($product['description']); ?></td>
                        <td><?php echo number_format($product['old_price'], 0, ',', '.'); ?>đ</td>
                        <td><?php echo number_format($product['price'], 0, ',', '.'); ?>đ</td>
                        <td><?php echo htmlspecialchars($product['category']); ?></td>
                        <td><?php echo $product['is_featured'] ? 'Có' : 'Không'; ?></td>
                        <td><?php echo $product['is_on_sale'] ? 'Có' : 'Không'; ?></td>
                        <td><?php echo $product['is_new'] ? 'Có' : 'Không'; ?></td>
                        <td class="actions">
                            <a href="edit.php?id=<?php echo $product['id']; ?>" class="btn-edit" title="Sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="?delete=<?php echo $product['id']; ?>" 
                               onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')" 
                               class="btn-delete" title="Xóa">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Phân trang -->
        <div class="pagination">
            <?php if($total_pages > 1): ?>
                <?php if($page > 1): ?>
                    <a href="?page=1&search=<?php echo urlencode($search_keyword); ?>" class="first-page"><i class="fas fa-angle-double-left"></i></a>
                    <a href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search_keyword); ?>" class="prev-page"><i class="fas fa-angle-left"></i></a>
                <?php endif; ?>

                <?php
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $page + 2);

                for($i = $start_page; $i <= $end_page; $i++): ?>
                    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search_keyword); ?>" class="<?php echo ($page == $i) ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if($page < $total_pages): ?>
                    <a href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search_keyword); ?>" class="next-page"><i class="fas fa-angle-right"></i></a>
                    <a href="?page=<?php echo $total_pages; ?>&search=<?php echo urlencode($search_keyword); ?>" class="last-page"><i class="fas fa-angle-double-right"></i></a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>