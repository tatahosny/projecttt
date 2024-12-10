<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            header('Location: index.php');
            exit;
        } else {
            $error = "كلمة المرور غير صحيحة.";
        }
    } else {
        $error = "البريد الإلكتروني غير موجود.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول</title>
    
    <style>
        /* إعداد الخلفية الداكنة */
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to bottom, #1c1c1c, #555);
            color: #f0f0f0;
        }

        /* تصميم الحاوية الرئيسية */
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            gap: 20px;
            animation: fadeIn 1s ease-in-out;
        }

        /* Sidebar مع صورة */
        .sidebar {
            display: flex;
            justify-content: center;
            align-items: center;
            background: rgba(0, 0, 0, 0.8);
            flex: 1;
            border-radius: 15px;
            padding: 10px;
            animation: slideIn 1s ease-in-out;
        }

        .sidebar img {
            height: 70%;
            max-width: 100%;
            border-radius: 10px;
            transition: transform 0.3s ease;
        }

        .sidebar img:hover {
            transform: scale(1.1);
        }

        /* واجهة تسجيل الدخول */
        .login-panel {
            background: linear-gradient(to bottom, #333, #555);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.6);
            color: #f0f0f0;
            max-width: 400px;
            text-align: center;
            animation: zoomIn 1s ease-in-out;
        }

        /* المدخلات */
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border: 1px solid #007bff;
            outline: none;
        }

        /* الزر */
        .btn {
            background: linear-gradient(to right, #5cb85c, #4cae4c);
            border: none;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
            margin: 5px 0;
        }

        .btn:hover {
            background: linear-gradient(to right, #4cae4c, #3e8e41);
        }

        /* الرسالة الخطأ */
        .error-message {
            color: #e74c3c;
            font-size: 14px;
            margin: 10px 0;
        }

        /* تأثير التحريك */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes slideIn {
            0% {
                transform: translateX(-10px);
            }
            100% {
                transform: translateX(10px);
            }
        }

        @keyframes zoomIn {
            from {
                transform: scale(0.7);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const img = document.querySelector('.sidebar img');
            img.addEventListener('mouseover', function () {
                img.style.transform = 'scale(1.2)';
            });

            img.addEventListener('mouseout', function () {
                img.style.transform = 'scale(1)';
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <!-- Sidebar مع صورة -->
        <div class="sidebar">
            <img src="https://files.oaiusercontent.com/file-Dma6APPpkiHDFqmFxprgw6?se=2024-12-07T22%3A56%3A56Z&sp=r&sv=2024-08-04&sr=b&rscc=max-age%3D604800%2C%20immutable%2C%20private&rscd=attachment%3B%20filename%3D03eb4ffc-c896-4eef-861d-098ee9e5a4da.webp&sig=OC7qU5RFCM8VFhJIsE3sH5/4Ycq3NIgsK/EiOt40SE8%3D" alt="صورة مشروب قهوة">
        </div>

        <!-- واجهة تسجيل الدخول -->
        <div class="login-panel">
            <h2>تسجيل الدخول</h2>
            <form action="login.php" method="POST">
                <input type="email" name="email" placeholder="البريد الإلكتروني" required>
                <input type="password" name="password" placeholder="كلمة المرور" required>
                <button type="submit" class="btn">تسجيل الدخول</button>
            </form>
            <a href="register.php">
                <button type="button" class="btn" style="background: #007bff;">إنشاء حساب جديد</button>
            </a>
            <?php if (!empty($error)) : ?>
                <p class="error-message"><?php echo $error; ?></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>



