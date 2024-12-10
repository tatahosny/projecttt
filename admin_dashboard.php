<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: admin_login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to bottom, #2c2c2c, #3a3a3a, #2c2c2c);
            color: #fff;
            margin: 0;
            padding: 0;
        }

        header {
            background: #333;
            padding: 15px 0;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.5);
        }

        header h1 {
            margin: 0;
        }

        nav {
            margin: 20px auto;
            text-align: center;
        }

        nav a {
            display: inline-block;
            margin: 0 10px;
            padding: 10px 20px;
            background: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s ease;
        }

        nav a:hover {
            background: #0056b3;
        }

        main {
            text-align: center;
            margin: 20px;
        }
    </style>
</head>
<body>
    <header>
        <h1>لوحة التحكم</h1>
    </header>

    <nav>
        <a href="manage_orders.php">إدارة الطلبات</a>
        <a href="manage_products.php">إدارة المنتجات</a>
        <a href="manage_users.php">إدارة المستخدمين</a>
        <a href="logout.php">تسجيل الخروج</a>
    </nav>

    <main>
        <p>مرحبًا بك في لوحة التحكم.</p>
    </main>
</body>
</html>
