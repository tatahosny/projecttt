<?php
require_once 'db.php';

// التحقق من وجود معرف المنتج في الرابط
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php"); // إعادة التوجيه إذا كان المعرف غير صحيح
    exit;
}

$product_id = $_GET['id'];
$query = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

// التحقق من وجود المنتج
if ($result->num_rows === 0) {
    $error_message = "لم يتم العثور على المنتج المطلوب.";
    header("Location: index.php?error=" . urlencode($error_message));
    exit;
}

$product = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل المنتج</title>
    
    <!-- تضمين الـ CSS -->
    <style>
        /* إعدادات عامة */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* تصميم الجسم */
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to bottom, #eceff1, #d9e2ec);
            margin: 0;
            padding: 0;
        }

        /* الترويسة */
        header {
            background: #333;
            color: #fff;
            padding: 10px 0;
            text-align: center;
            font-size: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        }

        /* قسم المحتوى */
        main {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 20px;
            gap: 20px;
            flex-wrap: wrap;
        }

        /* تصميم نافذة تفاصيل المنتج */
        .product-details-container {
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 20px;
            border-radius: 10px;
            animation: fadeIn 0.8s ease-in-out;
            width: 100%;
            max-width: 600px;
            text-align: center;
        }

        /* تأثير التحريك */
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

        /* صور المنتجات */
        img {
            max-width: 100%;
            max-height: 400px;
            border-radius: 8px;
            margin-bottom: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        /* النصوص */
        h1 {
            color: #333;
            font-size: 1.8rem;
            margin-bottom: 10px;
        }

        p {
            color: #555;
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        /* تصميم الأزرار */
        .btn {
            margin-top: 10px;
            padding: 10px 20px;
            color: #fff;
            background: #007bff;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease, transform 0.2s ease;
            display: inline-block;
            font-size: 1rem;
        }

        /* تأثير التحريك للأزرار عند التحميل */
        .btn:hover {
            background: #0056b3;
            transform: scale(1.1);
        }

        /* تصميم الـ Alerts التفاعلية */
        .alert {
            background-color: #ff4d4d;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            text-align: center;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.3);
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: none;
            animation: bounce 0.6s ease-out;
        }

        @keyframes bounce {
            0% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
            100% {
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <!-- الترويسة -->
    <header>
        تفاصيل المنتج
    </header>

    <!-- المحتوى الرئيسي -->
    <main>
        <div class="product-details-container">
            <!-- صورة المنتج -->
            <?php if ($product['image'] && file_exists($product['image'])): ?>
                <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
            <?php else: ?>
                <img src="path/to/default-image.jpg" alt="صورة غير متوفرة">
            <?php endif; ?>
            <div>
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                <p><?php echo htmlspecialchars($product['description']); ?></p>
                <p><strong>السعر: </strong><?php echo htmlspecialchars($product['price']); ?> جنيه</p>
                <a href="cart.php?action=add&product_id=<?php echo $product['id']; ?>" class="btn">أضف إلى السلة</a>
            </div>
        </div>
    </main>
</body>
</html>
