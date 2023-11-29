<?php
include 'config.php';

if (isset($_POST['category'])) {
    $category = $_POST['category'];

    // Use a prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT products.*, categories.category_name 
                            FROM products 
                            LEFT JOIN categories ON products.category_id = categories.category_id
                            WHERE categories.category_name = ?");
    $stmt->bind_param("s", $category);
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
} else {
    // If no category is provided, return all products (similar to the initial page load)
    $sql = "SELECT products.*, categories.category_name 
            FROM products 
            LEFT JOIN categories ON products.category_id = categories.category_id";
    $result = $conn->query($sql);

    // Generate HTML for all products
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['product_name']}</td>";
        echo "<td>{$row['price']}원</td>";
        echo "<td>{$row['stock_quantity']}</td>";
        echo "<td>{$row['category_name']}</td>";
        echo "<td><a class='order-button' href='#'>주문하기</a></td>";
        echo "</tr>";
    }
}
?>
