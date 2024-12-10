<?php
require_once 'db.php';
session_start();

// إضافة المنتج إلى السلة
if (isset($_GET['action']) && $_GET['action'] === 'add' && isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]++;
    } else {
        $_SESSION['cart'][$product_id] = 1;
    }
}

// تحديث الكمية
if (isset($_POST['action']) && $_POST['action'] === 'update' && isset($_POST['product_id'], $_POST['quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = max(1, intval($_POST['quantity']));
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] = $quantity;
    }
}

// حذف المنتج من السلة
if (isset($_GET['action']) && $_GET['action'] === 'remove' && isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
}

// جلب تفاصيل المنتجات في السلة
$cart_items = [];
if (!empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    $stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->bind_param(str_repeat('i', count($product_ids)), ...$product_ids);
    $stmt->execute();
    $cart_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// حساب الإجمالي الكلي
$total = 0;
foreach ($cart_items as $item) {
    $subtotal = $item['price'] * $_SESSION['cart'][$item['id']];
    $total += $subtotal;
}
$_SESSION['total'] = $total;
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عربة التسوق</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(to bottom, #1c1c1c, #555);
            color: #f0f0f0;
            margin: 0;
            padding: 0;
        }
        .container {
            display: flex;
            gap: 20px;
            margin: 20px;
        }
        .products {
            flex: 2;
            background: #333;
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
        }
        .product-item {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 10px;
            background: #444;
            padding: 10px;
            border-radius: 5px;
        }
        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }
        .product-details {
            flex-grow: 1;
        }
        .cart-summary {
            flex: 1;
            background: #333;
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
        }
        .summary-header {
            font-size: 1.2rem;
            margin-bottom: 15px;
        }
        .btn {
            display: inline-block;
            padding: 8px 15px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
            transition: background 0.3s ease;
            font-size: 0.9rem;
            cursor: pointer;
        }
        .btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="products">
        <h2>المنتجات في السلة</h2>
        <?php foreach ($cart_items as $item): ?>
            <div class="product-item">
                <img src="<?php echo $item['image_url']; ?>" alt="صورة المنتج" class="product-image">
                <div class="product-details">
                    <h4><?php echo $item['name']; ?></h4>
                    <p>السعر: <?php echo $item['price']; ?> جنيه</p>
                </div>
                <a href="product.php?id=<?php echo $item['id']; ?>" class="btn">عرض</a>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="cart-summary">
        <h3 class="summary-header">ملخص الطلب</h3>
        <p>إجمالي السعر: <?php echo $total; ?> جنيه</p>
        <p><a href="checkout.php" class="btn">إتمام الشراء</a></p>
    </div>
</div>

</body>
</html>
