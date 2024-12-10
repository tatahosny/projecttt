<?php
require_once 'db.php';

// جلب المنتجات من قاعدة البيانات
$query = "SELECT * FROM products";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المنتجات</title>

    <!-- تضمين CSS وتحسين الستايل -->
    <style>
        /* إعداد عام */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to bottom, #2c2c2c, #555);
            color: #f0f0f0;
        }

        header {
            background: #333;
            padding: 10px 0;
            color: #fff;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }

        nav ul {
            list-style: none;
            padding: 0;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        nav a {
            color: #f0f0f0;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        nav a:hover {
            color: #007bff;
        }

        /* تصميم المنتجات */
        .products-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
        }

        .product-card {
            background: #444;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            width: 250px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            animation: bounceIn 1s ease-out;
        }

        /* تأثير التحريك - Bounce In */
        @keyframes bounceIn {
            0% {
                transform: translateY(-20px);
                opacity: 0;
            }
            50% {
                transform: translateY(10px);
                opacity: 0.7;
            }
            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .product-card:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        }

        .btn {
            display: inline-block;
            padding: 8px 12px;
            margin: 5px 0;
            color: #fff;
            background: #5cb85c;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s ease;
        }

        .btn:hover {
            background: #4cae4c;
        }

        img.product-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            background: #555; /* في حالة الصورة لا تظهر */
        }
    </style>
</head>
<body>
    <!-- ترويسة الموقع -->
    <header>
        <nav>
            <ul>
                <li><a href="index.html">الصفحة الرئيسية</a></li>
                <li><a href="cart.php">عربة التسوق</a></li>
            </ul>
        </nav>
    </header>

    <!-- قسم البحث -->
    <main>
        <h1 style="text-align:center; margin:10px 0;">المنتجات المتاحة</h1>
        
        <!-- قسم المنتجات -->
        <div class="products-container" id="products-container">
            <?php while ($product = $result->fetch_assoc()): ?>
                <div class="product-card">
                    <!-- التحقق من وجود الصورة أو عرض صورة افتراضية -->
                    <img 
                        src="<?php echo isset($product['image']) && !empty($product['image']) ? $product['image'] : 'images/default-product.jpg'; ?>" 
                        alt="<?php echo htmlspecialchars($product['name']); ?>"
                        class="product-image"
                    >
                    <h3 style="margin:10px 0;"><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p style="font-size: 0.9em;"><?php echo substr($product['description'], 0, 50); ?>...</p>
                    <p style="font-size: 1em; margin: 10px 0;"><strong>السعر:</strong> <?php echo htmlspecialchars($product['price']); ?> جنيه</p>
                    <a href="product-details.php?id=<?php echo $product['id']; ?>" class="btn">تفاصيل المنتج</a>
                    <a href="cart.php?action=add&product_id=<?php echo $product['id']; ?>" class="btn">أضف إلى السلة</a>
                </div>
            <?php endwhile; ?>
        </div>
    </main>
</body>
</html>
