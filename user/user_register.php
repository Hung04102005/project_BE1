<?php
session_start();
include '../config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Vui lòng nhập đầy đủ thông tin';
    } elseif ($password !== $confirm_password) {
        $error = 'Mật khẩu xác nhận không khớp';
    } else {
        try {
            // Kiểm tra email đã tồn tại chưa
            $check_sql = "SELECT id FROM users WHERE email = :email";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $check_stmt->execute();
            
            if ($check_stmt->fetch()) {
                $error = 'Email đã được sử dụng';
            } else {
                // Tạo username từ email
                $username = explode('@', $email)[0];
                $base_username = $username;
                $counter = 1;
                
                // Kiểm tra username đã tồn tại chưa
                while (true) {
                    $check_username_sql = "SELECT id FROM users WHERE username = :username";
                    $check_username_stmt = $conn->prepare($check_username_sql);
                    $check_username_stmt->bindValue(':username', $username, PDO::PARAM_STR);
                    $check_username_stmt->execute();
                    
                    if (!$check_username_stmt->fetch()) {
                        break;
                    }
                    $username = $base_username . $counter;
                    $counter++;
                }
                
                // Thêm người dùng mới
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO users (username, name, email, password, role, status) 
                        VALUES (:username, :name, :email, :password, 'user', 'active')";
                $stmt = $conn->prepare($sql);
                $stmt->bindValue(':username', $username, PDO::PARAM_STR);
                $stmt->bindValue(':name', $name, PDO::PARAM_STR);
                $stmt->bindValue(':email', $email, PDO::PARAM_STR);
                $stmt->bindValue(':password', $hashed_password, PDO::PARAM_STR);
                
                if ($stmt->execute()) {
                    $success = 'Đăng ký thành công! Vui lòng đăng nhập.';
                    header('refresh:2;url=user_login.php');
                } else {
                    $error = 'Có lỗi xảy ra, vui lòng thử lại';
                }
            }
        } catch (PDOException $e) {
            $error = 'Có lỗi xảy ra, vui lòng thử lại sau';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - Food Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body>


    <div class="auth-container">
        <form class="auth-form" method="POST" action="">
            <h2>Đăng ký tài khoản</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="name">Họ tên:</label>
                <div class="input-with-icon">
                    <i class="fas fa-user"></i>
                    <input type="text" id="name" name="name" required 
                           value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <div class="input-with-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu:</label>
                <div class="input-with-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" required>
                    <i class="fas fa-eye toggle-password"></i>
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Xác nhận mật khẩu:</label>
                <div class="input-with-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <i class="fas fa-eye toggle-password"></i>
                </div>
            </div>

            <button type="submit" class="auth-button">
                <i class="fas fa-user-plus"></i> Đăng ký
            </button>
            
            <p class="auth-links">
                Đã có tài khoản? <a href="user_login.php">Đăng nhập</a>
            </p>
        </form>
    </div>

    <?php include '../footer.php'; ?>
    
    <script src="script.js"></script>
    <style>
        /* Auth Container Styles */
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f7f7f7;
            padding: 20px;
        }

        .auth-form {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .auth-form h2 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
            font-weight: 600;
        }

        /* Form Groups */
        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }

        /* Input with Icons */
        .input-with-icon {
            position: relative;
        }

        .input-with-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }

        .input-with-icon input {
            width: 100%;
            padding: 12px 40px;
            border: 2px solid #eee;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .input-with-icon input:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
            outline: none;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
        }

        /* Button Styles */
        .auth-button {
            width: 100%;
            padding: 14px;
            background: #4CAF50;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .auth-button:hover {
            background: #45a049;
        }

        .auth-button i {
            margin-right: 8px;
        }

        /* Alert Styles */
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .alert i {
            margin-right: 10px;
        }

        .alert-error {
            background: #ffe6e6;
            color: #d63031;
            border: 1px solid #ffcccc;
        }

        .alert-success {
            background: #e8f5e9;
            color: #2ecc71;
            border: 1px solid #c8e6c9;
        }

        /* Links */
        .auth-links {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }

        .auth-links a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .auth-links a:hover {
            color: #45a049;
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            .auth-form {
                padding: 20px;
            }

            .auth-form h2 {
                font-size: 24px;
            }

            .input-with-icon input {
                font-size: 14px;
            }
        }
    </style>
</body>
</html> 