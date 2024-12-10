<?php
// الاتصال بقاعدة البيانات
require_once 'db.php';

// بدء الجلسة
session_start();

// حذف الطلب
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_order_id'])) {
    $delete_order_id = $_POST['delete_order_id'];
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param('i', $delete_order_id);
    if ($stmt->execute()) {
        $success_message = "تم حذف الطلب بنجاح.";
    } else {
        $error_message = "حدث خطأ أثناء حذف الطلب.";
    }
}

// تحديث حالة الطلب
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param('si', $status, $order_id);
    if ($stmt->execute()) {
        $success_message = "تم تحديث حالة الطلب بنجاح.";
    } else {
        $error_message = "حدث خطأ أثناء تحديث حالة الطلب.";
    }
}

// جلب الطلبات من قاعدة البيانات
$result = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
$orders = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الطلبات</title>
    <style>
        /* إعدادات عامة */
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

        .success-message, .error-message {
            margin: 20px 0;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }

        .success-message {
            background-color: #4caf50;
            color: #fff;
        }

        .error-message {
            background-color: #f44336;
            color: #fff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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
        }

        .btn-danger {
            background: #e53935;
            color: #fff;
        }

        .btn-danger:hover {
            background: #d32f2f;
        }

        .btn {
            background: #007bff;
            color: #fff;
        }

        .btn:hover {
            background: #0056b3;
        }

        select {
            padding: 5px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>إدارة الطلبات</h1>

    <?php if (isset($success_message)): ?>
        <div class="success-message"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if (isset($error_message)): ?>
        <div class="error-message"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <?php if (empty($orders)): ?>
        <p style="text-align: center;">لا توجد طلبات حالياً.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>رقم الطلب</th>
                    <th>اسم العميل</th>
                    <th>رقم الهاتف</th>
                    <th>العنوان</th>
                    <th>الحالة</th>
                    <th>تاريخ الإنشاء</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo $order['id']; ?></td>
                        <td><?php echo $order['customer_name']; ?></td>
                        <td><?php echo $order['phone']; ?></td>
                        <td><?php echo $order['address']; ?></td>
                        <td><?php echo $order['status']; ?></td>
                        <td><?php echo $order['created_at']; ?></td>
                        <td>
                            <!-- زر حذف الطلب -->
                            <form method="POST" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا الطلب؟');">
                                <input type="hidden" name="delete_order_id" value="<?php echo $order['id']; ?>">
                                <button type="submit" class="btn btn-danger">حذف</button>
                            </form>

                            <!-- تحديث حالة الطلب -->
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="status" required>
                                    <option value="جديد" <?php if ($order['status'] === 'جديد') echo 'selected'; ?>>جديد</option>
                                    <option value="قيد التنفيذ" <?php if ($order['status'] === 'قيد التنفيذ') echo 'selected'; ?>>قيد التنفيذ</option>
                                    <option value="مكتمل" <?php if ($order['status'] === 'مكتمل') echo 'selected'; ?>>مكتمل</option>
                                </select>
                                <button type="submit" class="btn">تحديث</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
