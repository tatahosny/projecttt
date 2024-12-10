<?php
require_once 'db.php';

$username = 'admin'; // اسم المستخدم
$password = 'admin123'; // كلمة المرور

// تشفير كلمة المرور
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// إدخال المشرف في قاعدة البيانات
$stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $hashedPassword);

if ($stmt->execute()) {
    echo "تم إضافة المشرف بنجاح.";
} else {
    echo "حدث خطأ أثناء إضافة المشرف.";
}
?>
