<?php
include '../config/database.php';
session_start();

// Kiểm tra quyền truy cập của admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle logo upload
    if (isset($_FILES['logo']['name']) && $_FILES['logo']['name'] != '') {
        $logo = $_FILES['logo']['name'];
        $target = "../images/" . basename($logo);
        if (move_uploaded_file($_FILES['logo']['tmp_name'], $target)) {
            $stmt = $conn->prepare("UPDATE config SET value = ? WHERE name = 'logo'");
            $stmt->execute([$logo]);
        } else {
            echo "Logo upload failed!";
        }
    }

    // Handle slider images upload (maximum 3 images)
    if (isset($_FILES['slider_images']['name']) && !empty($_FILES['slider_images']['name'][0])) {
        $slider_images = [];

        // Lấy các ảnh slider hiện tại từ CSDL
        $stmt = $conn->prepare("SELECT value FROM config WHERE name = 'slider_images'");
        $stmt->execute();
        $current_slider_images = $stmt->fetch(PDO::FETCH_ASSOC)['value'];
        if ($current_slider_images) {
            $slider_images = explode(',', $current_slider_images);
        }

        // Xử lý tải ảnh mới lên
        foreach ($_FILES['slider_images']['name'] as $key => $image) {
            if ($image != '') {
                $target = "../images/" . basename($image);  // Đường dẫn đến ảnh trong thư mục
                if (move_uploaded_file($_FILES['slider_images']['tmp_name'][$key], $target)) {
                    $slider_images[] = $image;  // Thêm tên ảnh vào mảng
                } else {
                    echo "Failed to upload image: " . $image;
                }
            }
        }

        // Giới hạn số lượng ảnh slider tối đa là 3 ảnh
        if (count($slider_images) > 3) {
            $slider_images = array_slice($slider_images, -3);  // Lấy 3 ảnh cuối cùng
        }

        // Chuyển đổi mảng ảnh thành chuỗi để lưu vào CSDL
        $slider_images = implode(',', $slider_images);

        // Cập nhật lại ảnh slider trong CSDL
        $stmt = $conn->prepare("UPDATE config SET value = ? WHERE name = 'slider_images'");
        $stmt->execute([$slider_images]);
    }

    // Handle file upload limit
    if (isset($_POST['file_upload_limit'])) {
        $file_upload_limit = $_POST['file_upload_limit'];
        $stmt = $conn->prepare("UPDATE config SET value = ? WHERE name = 'file_upload_limit'");
        $stmt->execute([$file_upload_limit]);
    }

    header('Location: config.php');
    exit();
}

// Fetch current configurations
$stmt = $conn->prepare("SELECT * FROM config");
$stmt->execute();
$configs = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quản lý cấu hình</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .btn-submit {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-submit:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h2>Quản lý cấu hình</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="logo">Logo</label>
                <input type="file" name="logo" id="logo">
                <?php if (isset($configs['logo'])): ?>
                    <img src="../images/<?php echo htmlspecialchars($configs['logo']); ?>" alt="Logo" width="100">
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="slider_images">Ảnh slider (tối đa 3 ảnh)</label>
                <input type="file" name="slider_images[]" id="slider_images" multiple>
                <?php if (isset($configs['slider_images'])): ?>
                    <?php foreach (explode(',', $configs['slider_images']) as $image): ?>
                        <img src="../images/<?php echo htmlspecialchars($image); ?>" alt="Slider Image" width="100">
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="file_upload_limit">Giới hạn tải lên tệp (MB)</label>
                <input type="number" name="file_upload_limit" id="file_upload_limit" value="<?php echo htmlspecialchars($configs['file_upload_limit']); ?>">
            </div>
            <button type="submit" class="btn-submit">Lưu cấu hình</button>
        </form>
    </div>
</body>
</html>