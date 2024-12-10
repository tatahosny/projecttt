<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone = $_POST['phone'];
    $birth_date = $_POST['birth_date'];

    $error_message = "";

    // التحقق من صحة المدخلات
    if (
        empty($email) || empty($password) || empty($confirm_password) ||
        empty($first_name) || empty($last_name) || empty($phone) || empty($birth_date)
    ) {
        $error_message = "يجب ملء جميع الحقول.";
    } elseif (strlen($first_name) < 3 || strlen($last_name) < 3) {
        $error_message = "يجب أن يكون الاسم الأول والاسم الثاني على الأقل 3 أحرف.";
    } elseif (!preg_match('/^\d{10}$/', $phone)) {
        $error_message = "يجب أن يكون رقم الهاتف مكونًا من 10 أرقام.";
    } elseif (strtotime($birth_date) > strtotime('-18 years')) {
        $error_message = "يجب أن يكون عمرك 18 عامًا أو أكثر.";
    } elseif ($password !== $confirm_password) {
        $error_message = "كلمات المرور لا تتطابق.";
    } else {
        // التحقق من وجود البريد الإلكتروني في قاعدة البيانات
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error_message = "البريد الإلكتروني مُستخدم بالفعل.";
        } else {
            // إدخال المستخدم في قاعدة البيانات
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (email, password, first_name, last_name, phone, birth_date) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssssss", $email, $hashed_password, $first_name, $last_name, $phone, $birth_date);

            if ($stmt->execute()) {
                header("Location: login.php");
                exit();
            } else {
                $error_message = "فشل تسجيل الحساب. حاول مرة أخرى.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء حساب جديد</title>
    
    <style>
        /* إعداد الخلفية الداكنة */
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to bottom, #333333, #555555);
            color: #f0f0f0;
        }

        /* تصميم الحاوية الرئيسية */
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* واجهة تسجيل الحساب */
        .register-panel {
            background: #444;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
            text-align: center;
            width: 400px;
            animation: fadeIn 1s ease-in-out;
        }

        /* الرسائل */
        .error-message {
            color: #e74c3c;
            font-size: 14px;
            margin: 10px 0;
        }

        /* مدخلات النموذج */
        input[type="email"],
        input[type="password"],
        input[type="text"],
        input[type="tel"],
        input[type="date"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: none;
            border-radius: 5px;
            box-sizing: border-box;
        }

        /* الزر */
        .btn {
            background: linear-gradient(to right, #3498db, #2980b9);
            border: none;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .btn:hover {
            background: linear-gradient(to right, #2980b9, #1f5f8b);
        }

        /* تأثيرات المتحركة */
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
    </style>

</head>
<body>
    <div class="container">
        <!-- واجهة التسجيل -->
        <div class="register-panel">
            <h2>إنشاء حساب جديد</h2>
            <?php if (!empty($error_message)) : ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <form action="register.php" method="POST">
                <input type="email" name="email" placeholder="البريد الإلكتروني" required>
                <input type="password" name="password" placeholder="كلمة المرور" required>
                <input type="password" name="confirm_password" placeholder="تأكيد كلمة المرور" required>
                <input type="text" name="first_name" placeholder="الاسم الأول" required>
                <input type="text" name="last_name" placeholder="الاسم الثاني" required>
                <input type="tel" name="phone" placeholder="رقم الهاتف (10 أرقام)" pattern="\d{10}" required>
                <input type="date" name="birth_date" placeholder="تاريخ الميلاد" required>
                <button type="submit" class="btn">إنشاء الحساب</button>
            </form>
        </div>
    </div>
</body>
</html>
