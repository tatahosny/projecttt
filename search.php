<?php
require_once 'db.php';

$searchQuery = isset($_GET['query']) ? trim($_GET['query']) : '';

if ($searchQuery) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE ? OR description LIKE ?");
    $searchTerm = "%$searchQuery%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
} else {
    $stmt = $conn->prepare("SELECT * FROM products");
}

$stmt->execute();
$result = $stmt->get_result();

while ($product = $result->fetch_assoc()) {
    echo '
        <div class="product-card">
            <img src="' . $product['image'] . '" alt="' . $product['name'] . '" style="width:100%; height:150px; object-fit:cover; border-radius:8px;">
            <h3 style="margin:10px 0;">' . $product['name'] . '</h3>
            <p style="font-size: 0.9em;">' . substr($product['description'], 0, 50) . '...</p>
            <p style="font-size: 1em; margin: 10px 0;"><strong>السعر:</strong> ' . $product['price'] . ' جنيه</p>
            <a href="product-details.php?id=' . $product['id'] . '" class="btn">تفاصيل المنتج</a>
            <a href="cart.php?action=add&product_id=' . $product['id'] . '" class="btn">أضف إلى السلة</a>
        </div>
    ';
}
?>
