<?php
include 'config.php';

if (isset($_POST['category'])) {
    $category = $_POST['category'];

    // Use a prepared statement to prevent SQL injection
    if ($category === "") {
        // If "전체" (all) category is selected, retrieve all products
        $sql = "SELECT products.*, categories.category_name 
                FROM products 
                LEFT JOIN categories ON products.category_id = categories.category_id";
    } else {
        // If a specific category is selected, filter by that category
        $sql = "SELECT products.*, categories.category_name 
                FROM products 
                LEFT JOIN categories ON products.category_id = categories.category_id
                WHERE categories.category_id = ?";
    }

    $stmt = $conn->prepare($sql);

    if ($category !== "") {
        // Only bind parameter if a specific category is selected
        $stmt->bind_param("i", $category);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    // Generate HTML for filtered products
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['product_name']}</td>";
        echo "<td>{$row['price']}원</td>";
        echo "<td>{$row['stock_quantity']}</td>";
        echo "<td>{$row['category_name']}</td>";
        echo "<td><a class='order-button' href='#'>주문하기</a></td>";
        echo "</tr>";
    }

    $stmt->close();
}
?>
