<?php
require_once 'db.php';
session_start();

// مسار تخزين الصور
$upload_dir = 'uploads/';

// إنشاء مجلد التحميل إذا لم يكن موجودًا
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// إضافة منتج جديد
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    
    // معالجة رفع الصورة
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $image_name = time() . '_' . $_FILES['image']['name'];
        $upload_path = $upload_dir . $image_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
            $image_url = $upload_path;
        } else {
            $error_message = "فشل في رفع الصورة.";
            $image_url = 'default.png';
        }
    } else {
        $image_url = 'default.png';
    }

    $stmt = $conn->prepare("INSERT INTO products (name, price, description, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('sdsb', $name, $price, $description, $image_url);

    if ($stmt->execute()) {
        $success_message = "تمت إضافة المنتج بنجاح.";
    } else {
        $error_message = "فشل في إضافة المنتج.";
    }
}

// جلب المنتجات من قاعدة البيانات
$products_result = $conn->query("SELECT * FROM products ORDER BY id DESC");
$products = $products_result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المنتجات</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: #f8f9fa;
            color: #333;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            margin: 10px 0;
            text-align: center;
        }

        table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
            background: #fff;
        }

        img {
            max-width: 100px;
            height: auto;
            border-radius: 5px;
        }

        .btn-success {
            background-color: #28a745;
            color: white;
            transition: background 0.2s;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
            transition: background 0.2s;
        }
    </style>
</head>
<body>
    <h1>إدارة المنتجات</h1>

    <!-- نموذج إضافة منتج -->
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="اسم المنتج" required>
        <input type="number" name="price" placeholder="السعر" required>
        <input type="text" name="description" placeholder="تفاصيل المنتج" required>
        <input type="file" name="image" required>
        <button type="submit" name="add_product" class="btn-success">أضف المنتج</button>
    </form>

    <!-- عرض المنتجات -->
    <table>
        <thead>
            <tr>
                <th>الصورة</th>
                <th>رقم المنتج</th>
                <th>اسم المنتج</th>
                <th>السعر</th>
                <th>التفاصيل</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><img src="<?php echo $product['image']; ?>" alt="صورة المنتج"></td>
                    <td><?php echo $product['id']; ?></td>
                    <td><?php echo $product['name']; ?></td>
                    <td><?php echo $product['price']; ?> جنيه</td>
                    <td><?php echo $product['description']; ?></td>
                    <td>
                        <!-- حذف المنتج -->
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="delete_product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" class="btn-danger" onclick="return confirm('هل أنت متأكد من الحذف؟');">حذف</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
