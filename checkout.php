<?php
session_start();
require_once 'db.php';

// حساب الإجمالي الكلي من السلة
$total = 0;
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    $stmt = $conn->prepare("SELECT id, price FROM products WHERE id IN ($placeholders)");
    $stmt->bind_param(str_repeat('i', count($product_ids)), ...$product_ids);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $product_id = $row['id'];
        $price = $row['price'];
        $quantity = $_SESSION['cart'][$product_id];
        $total += $price * $quantity;
    }
    $_SESSION['total'] = $total; // تخزين الإجمالي في الجلسة
} else {
    die("السلة فارغة.");
}

// معالجة البيانات عند إتمام الطلب
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $province = $_POST['province'];
    $cart_items = $_SESSION['cart'];
    $total = $_SESSION['total']; // الحصول على الإجمالي من الجلسة

    // تحديد رسوم الشحن حسب المحافظة
    $shipping_costs = [
        'القاهرة' => 30,
        'الجيزة' => 25,
        'الإسكندرية' => 40,
        'الأقصر' => 50,
    ];

    $shipping_cost = isset($shipping_costs[$province]) ? $shipping_costs[$province] : 0;
    $grand_total = $total + $shipping_cost;

    // تخزين الطلب في قاعدة البيانات
    $stmt = $conn->prepare("INSERT INTO orders (name, phone, address, province, total, shipping_cost, grand_total, order_items) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        die("تحضير الاستعلام فشل: " . $conn->error);
    }
    
    $order_items = json_encode($cart_items ?? []);
    $bind_result = $stmt->bind_param("ssssddds", $name, $phone, $address, $province, $total, $shipping_cost, $grand_total, $order_items);
    
    if ($bind_result === false) {
        die("ربط المتغيرات فشل: " . $stmt->error);
    }
    
    $execute_result = $stmt->execute();
    
    if ($execute_result === false) {
        die("تنفيذ الاستعلام فشل: " . $stmt->error);
    }
    
    // حذف السلة
    unset($_SESSION['cart']);

    // عرض رسالة النجاح
    header("Location: success.php?grand_total=$grand_total");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إتمام الشراء</title>
    <style>
        /* إعدادات عامة */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(to bottom, #1c1c1c, #555);
            color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* تصميم النموذج */
        .checkout-form {
            background: #333;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
            width: 90%;
            max-width: 400px;
            animation: fadeIn 1.5s ease-in-out;
        }

        h2 {
            text-align: center;
            color: #fff;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #f0f0f0;
        }

        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #444;
            border-radius: 5px;
            background: #1c1c1c;
            color: #f0f0f0;
            font-size: 14px;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.5);
        }

        button {
            width: 100%;
            padding: 10px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        button:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }

        button:active {
            background: #00408b;
            transform: translateY(0);
        }

        /* تأثير التحريك */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
</head>
<body>

<div class="checkout-form">
    <h2>إتمام الشراء</h2>
    <form action="checkout.php" method="POST">
        <div class="form-group">
            <label for="name">الاسم الكامل:</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="phone">رقم الهاتف:</label>
            <input type="text" id="phone" name="phone" required>
        </div>
        <div class="form-group">
            <label for="address">العنوان بالتفصيل:</label>
            <input type="text" id="address" name="address" required>
        </div>
        <div class="form-group">
            <label for="province">المحافظة:</label>
            <select id="province" name="province" required>
                <option value="القاهرة">القاهرة</option>
                <option value="الجيزة">الجيزة</option>
                <option value="الإسكندرية">الإسكندرية</option>
                <option value="الأقصر">الأقصر</option>
            </select>
        </div>
        <input type="hidden" name="total" value="<?php echo $_SESSION['total']; ?>">
        <button type="submit">تأكيد الطلب</button>
    </form>
</div>

</body>
</html>
