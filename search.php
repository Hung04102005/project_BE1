<?php
include 'config/database.php';

// Lấy từ khóa tìm kiếm và làm sạch dữ liệu
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 3; // Số sản phẩm mỗi trang
$offset = ($page - 1) * $limit;

try {
    // Query tìm kiếm trong bảng products sử dụng PDO
    $stmt = $conn->prepare("SELECT * FROM products WHERE LOWER(name) LIKE LOWER(:keyword) LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':keyword', "%$keyword%", PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Query để đếm tổng số sản phẩm
    $count_stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE LOWER(name) LIKE LOWER(:keyword)");
    $count_stmt->execute(['keyword' => "%$keyword%"]);
    $total_products = $count_stmt->fetchColumn();
    $total_pages = ceil($total_products / $limit);

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Kết quả tìm kiếm - <?php echo htmlspecialchars($keyword); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="search-result-container">
        <div class="search-result-heading">
            <span class="search-text">Kết quả tìm kiếm cho</span> 
            "<span class="search-keyword"><?php echo htmlspecialchars($keyword); ?></span>" 
            <span class="search-count">(<?php echo $total_products; ?> sản phẩm)</span>
        </div>
    </div>

    <div class="container">
        <button onclick="history.back()" class="back-btn">
            <i class="fas fa-arrow-left"></i> Quay lại
        </button>

        <div class="search-results">
            <div class="products-container">
                <?php if(!empty($results)): ?>
                    <?php foreach($results as $row): ?>
                        <div class="product-box">
                            <img src="images/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                            <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                            <p class="price"><?php echo number_format($row['price']); ?>đ</p>
                            <a href="product-detail.php?id=<?php echo $row['id']; ?>" class="btn">Xem chi tiết</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-results">Không tìm thấy sản phẩm nào phù hợp với từ khóa "<?php echo htmlspecialchars($keyword); ?>"</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?keyword=<?php echo urlencode($keyword); ?>&page=<?php echo $page - 1; ?>">&laquo; Trang trước</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?keyword=<?php echo urlencode($keyword); ?>&page=<?php echo $i; ?>" class="<?php if ($i == $page) echo 'active'; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?keyword=<?php echo urlencode($keyword); ?>&page=<?php echo $page + 1; ?>">Trang sau &raquo;</a>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    <script src="script.js"></script>
</body>
</html>