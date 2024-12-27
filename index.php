<?php
include 'config/database.php';
session_start();

// Số sản phẩm trên mỗi trang
$products_per_page = 6;

// Lấy số trang hiện tại từ URL, mặc định là trang 1
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Tính offset cho LIMIT trong câu SQL
$offset = ($current_page - 1) * $products_per_page;

// Lấy tổng số sản phẩm
$total_sql = "SELECT COUNT(*) as total FROM products";
$total_stmt = $conn->query($total_sql);
$total_products = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Tính tổng số trang
$total_pages = ceil($total_products / $products_per_page);

// Lấy sản phẩm cho trang hiện tại
$sql = "SELECT * FROM products LIMIT :limit OFFSET :offset";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':limit', $products_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch featured, on sale, and new products
$featured_products = $conn->query("SELECT * FROM products WHERE is_featured = 1")->fetchAll(PDO::FETCH_ASSOC);
$on_sale_products = $conn->query("SELECT * FROM products WHERE is_on_sale = 1")->fetchAll(PDO::FETCH_ASSOC);
$new_products = $conn->query("SELECT * FROM products WHERE is_new = 1")->fetchAll(PDO::FETCH_ASSOC);

// Fetch current configurations
$stmt = $conn->prepare("SELECT * FROM config");
$stmt->execute();
$configs = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="script.js"></script>
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="slider">
        <?php
        $slider_images = explode(',', $configs['slider_images']);
        foreach ($slider_images as $image) {
            echo '<div class="slide"><img src="images/' . htmlspecialchars($image) . '" alt="Slider Image"></div>';
        }
        ?>
    </div>

    <div class="container">
        <h3 class="sub-heading">Thực đơn của chúng tôi</h3>
        <h1 class="main-heading">Món ăn đặc biệt hôm nay</h1>

        <div class="box-container">
            <?php foreach ($featured_products as $product): ?>
                <div class="box">
                    <div class="image">
                        <img src="images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <a href="#" class="fas fa-heart"></a>
                        <span class="badge badge-featured">Nổi bật</span>
                        <i class="fas fa-star icon-featured"></i>
                    </div>
                    <div class="content">
                        <div class="stars">
                            <?php
                            $rating = $product['rating'];
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $rating) {
                                    echo '<i class="fas fa-star"></i>';
                                } elseif ($i - $rating < 1) {
                                    echo '<i class="fas fa-star-half-alt"></i>';
                                } else {
                                    echo '<i class="far fa-star"></i>';
                                }
                            }
                            ?>
                        </div>
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="product-desc">
                            <?php
                            $description = htmlspecialchars($product['description']);
                            echo substr($description, 0, 50) . (strlen($description) > 50 ? '...' : '');
                            ?>
                        </p>
                        <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="btn">Xem Chi Tiết</a>
                        <span class="price"><?php echo number_format($product['price'], 0, ',', '.'); ?>đ</span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <h1 class="main-heading">Món ăn khuyến mại</h1>
        <div class="box-container">
            <?php foreach ($on_sale_products as $product): ?>
                <div class="box">
                    <div class="image">
                        <img src="images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <a href="#" class="fas fa-heart"></a>
                        <span class="badge badge-sale">Khuyến mại</span>
                        <i class="fas fa-tags icon-sale"></i>
                    </div>
                    <div class="content">
                        <div class="stars">
                            <?php
                            $rating = $product['rating'];
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $rating) {
                                    echo '<i class="fas fa-star"></i>';
                                } elseif ($i - $rating < 1) {
                                    echo '<i class="fas fa-star-half-alt"></i>';
                                } else {
                                    echo '<i class="far fa-star"></i>';
                                }
                            }
                            ?>
                        </div>
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="product-desc">
                            <?php
                            $description = htmlspecialchars($product['description']);
                            echo substr($description, 0, 50) . (strlen($description) > 50 ? '...' : '');
                            ?>
                        </p>
                        <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="btn">Xem Chi Tiết</a>
                        <span class="price">
                            <span class="old-price"><?php echo number_format($product['old_price'], 0, ',', '.'); ?>đ</span>
                            <span class="new-price"><?php echo number_format($product['price'], 0, ',', '.'); ?>đ</span>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <h1 class="main-heading">Món ăn mới nhất</h1>
        <div class="box-container">
            <?php foreach ($new_products as $product): ?>
                <div class="box">
                    <div class="image">
                        <img src="images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <a href="#" class="fas fa-heart"></a>
                        <span class="badge badge-new">Mới</span>
                        <i class="fas fa-bell icon-new"></i>
                    </div>
                    <div class="content">
                        <div class="stars">
                            <?php
                            $rating = $product['rating'];
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $rating) {
                                    echo '<i class="fas fa-star"></i>';
                                } elseif ($i - $rating < 1) {
                                    echo '<i class="fas fa-star-half-alt"></i>';
                                } else {
                                    echo '<i class="far fa-star"></i>';
                                }
                            }
                            ?>
                        </div>
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="product-desc">
                            <?php
                            $description = htmlspecialchars($product['description']);
                            echo substr($description, 0, 50) . (strlen($description) > 50 ? '...' : '');
                            ?>
                        </p>
                        <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="btn">Xem Chi Tiết</a>
                        <span class="price"><?php echo number_format($product['price'], 0, ',', '.'); ?>đ</span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="pagination">
            <?php if ($total_pages > 1): ?>
                <?php if ($current_page > 1): ?>
                    <a href="?page=<?php echo $current_page - 1; ?>" class="btn">Trang trước</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>"
                        class="btn <?php echo $i === $current_page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($current_page < $total_pages): ?>
                    <a href="?page=<?php echo $current_page + 1; ?>" class="btn">Trang sau</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    <script src="script.js"></script>
    <script>
        $(document).ready(function() {
            let currentIndex = 0;
            const slides = $('.slide');
            const totalSlides = slides.length;

            function showSlide(index) {
                slides.hide();
                slides.eq(index).show();
            }

            function nextSlide() {
                currentIndex = (currentIndex + 1) % totalSlides;
                showSlide(currentIndex);
            }

            showSlide(currentIndex);
            setInterval(nextSlide, 3000); // Change slide every 3 seconds
        });
    </script>
    <style>
        .logo-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .shop-name {
            font-size: 20px;
            font-weight: bold;
            color: rgb(228, 76, 182);
            text-transform: uppercase;
            font-family: 'Arial', sans-serif;
        }

        .slider {
            position: relative;
            width: 100%;
            max-width: 800px;
            /* Adjust the max-width as needed */
            margin: 20px auto;
            /* Center the slider */
            overflow: hidden;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .slide {
            display: none;
        }

        .slide img {
            width: 100%;
            height: auto;
            display: block;
        }

        .box .image .badge {
            position: absolute;
            bottom: 10px;
            left: 10px;
            font-size: 14px;
            padding: 5px 10px;
            color: #fff;
            border-radius: 20px;
            text-transform: uppercase;
        }

        .badge-featured {
            background: #ff8c00;
        }

        .badge-sale {
            background: #d63031;
        }

        .badge-new {
            background: #6ab04c;
        }

        .main-heading {
            text-align: center;
            /* Căn giữa nội dung theo chiều ngang */
            font-size: 36px;
            /* Bạn có thể điều chỉnh kích thước font theo ý muốn */
            margin-bottom: 30px;
            /* Tạo khoảng cách phía dưới */
        }

        .old-price {
            text-decoration: line-through;
            color: #888;
            margin-right: 10px;
        }

        .new-price {
            color: #d63031;
            font-weight: bold;
        }
    </style>
</body>

</html>