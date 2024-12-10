<?php
$grand_total = $_GET['grand_total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نجاح العملية</title>
    <style>
        body {
            text-align: center;
            font-family: 'Arial', sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 50px;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 20px;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            display: inline-block;
            margin: 20px auto;
        }

        button {
            padding: 10px 20px;
            background: #007BFF;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
            font-size: 16px;
        }

        button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<div class="success-message">
    <h1>✅ تم إرسال طلبك بنجاح!</h1>
    <p>الإجمالي الكلي: <?php echo $grand_total; ?> جنيه</p>
</div>

<form action="generate_invoice.php" method="POST">
    <button type="submit">طباعة الفاتورة PDF</button>
</form>

</body>
</html>
