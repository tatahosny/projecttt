<?php
require_once 'db.php';
session_start();

// معالجة إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    
    $imageUrl = '';
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadUrl = 'https://up.top4top.io/upload.php'; // رابط رفع الصورة الخاص بـ top4top

        // إعداد البيانات المطلوبة لإرسال الصورة عبر POST
        $postFields = [
            'file' => new CURLFile($_FILES['image']['tmp_name'])
        ];

        // استخدام cURL لإرسال الصورة
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uploadUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        // معالجة رد الخادم
        $responseData = json_decode($response, true);

        if ($responseData && isset($responseData['url'])) {
            $imageUrl = $responseData['url']; // الحصول على الرابط من الاستجابة
        } else {
            $error_message = "فشل في رفع الصورة إلى top4top.";
        }
    }

    // إدخال المنتج في قاعدة البيانات
    $stmt = $conn->prepare("INSERT INTO products (name, price, description, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('sdsb', $name, $price, $description, $imageUrl);

    if ($stmt->execute()) {
        $success_message = "تم إضافة المنتج بنجاح.";
    } else {
        $error_message = "حدث خطأ أثناء إضافة المنتج.";
    }
}
?>



<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة منتج جديد</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to bottom, #333, #555);
            color: #fff;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: #444;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        }

        input,
        textarea {
            margin-bottom: 10px;
            padding: 10px;
            width: 100%;
            border: 1px solid #555;
            border-radius: 5px;
        }

        button {
            background: #007bff;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background: #0056b3;
        }

        .success-message,
        .error-message {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
        }

        .success-message {
            background-color: #4caf50;
        }

        .error-message {
            background-color: #f44336;
        }
    </style>
</head>
<body>
    <h1>إضافة منتج جديد</h1>

    <!-- الرسائل -->
    <?php if ($success_message): ?>
        <div class="success-message"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if ($error_message): ?>
        <div class="error-message"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <!-- نموذج إضافة منتج -->
    <form method="POST" enctype="multipart/form-data">
        <label for="name">اسم المنتج:</label>
        <input type="text" name="name" id="name" placeholder="أدخل اسم المنتج" required>
        
        <label for="price">السعر:</label>
        <input type="text" name="price" id="price" placeholder="أدخل سعر المنتج" required>
        
        <label for="description">الوصف:</label>
        <textarea name="description" id="description" rows="3" placeholder="أدخل وصف المنتج"></textarea>
        
        <label for="image">رفع صورة المنتج:</label>
        <input type="file" name="image" id="image" required>
        
        <button type="submit" name="add_product">إضافة المنتج</button>
    </form>
</body>
</html>
