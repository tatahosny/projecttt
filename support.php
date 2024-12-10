<?php
require_once 'db.php';
session_start();

// معالجة إرسال الرسالة من المستخدم
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_support'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO support_requests (name, email, message) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $name, $email, $message);

    if ($stmt->execute()) {
        $success_message = "تم إرسال الرسالة بنجاح.";
    } else {
        $error_message = "حدث خطأ أثناء إرسال الرسالة.";
    }
}

// جلب الرسائل من قاعدة البيانات
$result = $conn->query("SELECT * FROM support_requests ORDER BY created_at DESC");
$support_requests = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الدعم الفني</title>
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
            text-align: center;
            margin: 20px auto;
            max-width: 600px;
        }

        input[type="text"], input[type="email"], textarea {
            padding: 10px;
            margin: 5px;
            width: 100%;
            border-radius: 5px;
            border: 1px solid #aaa;
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

        table {
            margin-top: 30px;
            width: 100%;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
            background: #444;
        }

        table th {
            background: #555;
        }

        .success-message {
            background-color: #4caf50;
            color: #fff;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin: 10px auto;
            max-width: 400px;
        }

        .error-message {
            background-color: #f44336;
            color: #fff;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin: 10px auto;
            max-width: 400px;
        }
    </style>
</head>
<body>
    <h1>الدعم الفني</h1>

    <!-- الرسائل -->
    <?php if (isset($success_message)): ?>
        <div class="success-message"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if (isset($error_message)): ?>
        <div class="error-message"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <!-- نموذج إرسال الرسالة -->
    <form method="POST">
        <input type="text" name="name" placeholder="الاسم" required>
        <input type="email" name="email" placeholder="البريد الإلكتروني" required>
        <textarea name="message" placeholder="اكتب رسالتك هنا..." rows="5" required></textarea>
        <button type="submit" name="send_support">إرسال الرسالة</button>
    </form>

    <!-- عرض الرسائل المُقدمة -->
    <h2 style="text-align: center; margin-top: 30px;">الرسائل الواردة</h2>
    <?php if (empty($support_requests)): ?>
        <p style="text-align: center;">لا توجد رسائل حالياً.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>الرقم</th>
                    <th>الاسم</th>
                    <th>البريد الإلكتروني</th>
                    <th>الرسالة</th>
                    <th>تاريخ الإرسال</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($support_requests as $request): ?>
                    <tr>
                        <td><?php echo $request['id']; ?></td>
                        <td><?php echo $request['name']; ?></td>
                        <td><?php echo $request['email']; ?></td>
                        <td><?php echo $request['message']; ?></td>
                        <td><?php echo $request['created_at']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
