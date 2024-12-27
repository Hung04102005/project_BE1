<?php
session_start();
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Vui lòng nhập đầy đủ thông tin';
    } else {
        try {
            // Debug: In ra thông tin
            echo "Email đang thử: " . $email . "<br>";

            // Sửa câu SQL để kiểm tra chính xác hơn
            $sql = "SELECT * FROM users WHERE email = :email";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Debug: Kiểm tra user
            if ($user) {
                echo "Tìm thấy user với ID: " . $user['id'] . "<br>";
                echo "Role: " . $user['role'] . "<br>";
                echo "Status: " . $user['status'] . "<br>";

                // Kiểm tra mật khẩu
                if (password_verify($password, $user['password'])) {
                    echo "Mật khẩu đúng!<br>";
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_role'] = $user['role'];

                    header('Location: ../index.php');
                    exit();
                } else {
                    echo "Mật khẩu không khớp!<br>";
                    echo "Hash trong DB: " . $user['password'] . "<br>";
                }
            } else {
                echo "Không tìm thấy user với email: " . $email . "<br>";
            }

            $error = 'Email hoặc mật khẩu không chính xác';
        } catch (PDOException $e) {
            echo "Lỗi Database: " . $e->getMessage() . "<br>";
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
    <title>Đăng nhập - Food Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
</head>

<body>

    <div class="auth-container login-page">
        <form class="auth-form" method="POST" action="">
            <h2>Đăng nhập</h2>

            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="email">Email:</label>
                <div class="input-with-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" required
                        value="<?php echo htmlspecialchars($email ?? ''); ?>">
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

            <button type="submit" class="auth-button">Đăng nhập</button>

            <p class="auth-links">
                Chưa có tài khoản? <a href="user_register.php">Đăng ký ngay</a>
            </p>
        </form>
    </div>

    <?php include '../footer.php'; ?>

    <script>
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const input = this.previousElementSibling;
                if (input.type === 'password') {
                    input.type = 'text';
                    this.classList.remove('fa-eye');
                    this.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    this.classList.remove('fa-eye-slash');
                    this.classList.add('fa-eye');
                }
            });
        });
    </script>
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