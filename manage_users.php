<?php
require_once 'db.php';
session_start();

// إضافة مستخدم جديد
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $username, $email, $password);

    if ($stmt->execute()) {
        $success_message = "تم إضافة المستخدم بنجاح.";
    } else {
        $error_message = "حدث خطأ أثناء إضافة المستخدم.";
    }
}

// تحديث مستخدم
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $id = $_POST['user_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
    $stmt->bind_param('ssi', $username, $email, $id);

    if ($stmt->execute()) {
        $success_message = "تم تحديث المستخدم بنجاح.";
    } else {
        $error_message = "حدث خطأ أثناء التحديث.";
    }
}

// حذف مستخدم
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_id'])) {
    $delete_user_id = $_POST['delete_user_id'];

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param('i', $delete_user_id);

    if ($stmt->execute()) {
        $success_message = "تم حذف المستخدم بنجاح.";
    } else {
        $error_message = "حدث خطأ أثناء حذف المستخدم.";
    }
}

// جلب جميع المستخدمين من قاعدة البيانات
$result = $conn->query("SELECT * FROM users ORDER BY id DESC");
$users = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المستخدمين</title>
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
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

        .btn {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
            color: #fff;
        }

        .btn-danger {
            background: #e53935;
        }

        .btn-danger:hover {
            background: #d32f2f;
        }

        .btn-success {
            background: #4caf50;
        }

        .btn-success:hover {
            background: #45a049;
        }

        form {
            margin-bottom: 20px;
            text-align: center;
        }

        input[type="text"], input[type="email"], input[type="password"] {
            padding: 8px;
            margin: 5px;
            border-radius: 5px;
            border: 1px solid #aaa;
        }
    </style>
</head>
<body>
    <h1>إدارة المستخدمين</h1>

    <!-- الرسائل -->
    <?php if (isset($success_message)): ?>
        <div class="success-message"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if (isset($error_message)): ?>
        <div class="error-message"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <!-- نموذج إضافة مستخدم -->
    <form method="POST">
        <input type="text" name="username" placeholder="اسم المستخدم" required>
        <input type="email" name="email" placeholder="البريد الإلكتروني" required>
        <input type="password" name="password" placeholder="كلمة المرور" required>
        <button type="submit" name="add_user" class="btn btn-success">إضافة مستخدم</button>
    </form>

    <!-- عرض المستخدمين -->
    <?php if (empty($users)): ?>
        <p style="text-align: center;">لا توجد مستخدمين حالياً.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>رقم المستخدم</th>
                    <th>اسم المستخدم</th>
                    <th>البريد الإلكتروني</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo $user['username']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td>
                            <!-- تحديث مستخدم -->
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <input type="text" name="username" value="<?php echo $user['username']; ?>" required>
                                <input type="email" name="email" value="<?php echo $user['email']; ?>" required>
                                <button type="submit" name="update_user" class="btn btn-success">تحديث</button>
                            </form>

                            <!-- حذف مستخدم -->
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="delete_user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذا المستخدم؟');">حذف</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
