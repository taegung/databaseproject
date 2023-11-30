<?php
include 'config.php';
session_start();

if (isset($_SESSION["userid"])) {
    $userid = $_SESSION["userid"];

    // Fetch cart items for the logged-in user
    $sql = "SELECT cartlist.*, products.price, products.stock_quantity
            FROM cartlist
            INNER JOIN products ON cartlist.product_id = products.product_id
            WHERE cartlist.userid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userid);
    $stmt->execute();
    $result = $stmt->get_result();

    // Initialize an array to store updates to stock quantities
    $stockUpdates = array();

    // Insert cart items into the orders table and update stock quantities
    while ($row = $result->fetch_assoc()) {
        $product_id = $row['product_id'];
        $quantity = $row['quantity'];
        $product_price = $row['price'];
        $total_price = $quantity * $product_price;

        // Update stock quantity
        $newStockQuantity = $row['stock_quantity'] - $quantity;
        $stockUpdates[$product_id] = $newStockQuantity;

        // Insert into orders table
        $insertSql = "INSERT INTO orders (userid, product_id, quantity, total_price)
                      VALUES (?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bind_param("siii", $userid, $product_id, $quantity, $total_price);
        $insertStmt->execute();
        $insertStmt->close();
    }

    // Update product stock quantities
    foreach ($stockUpdates as $product_id => $newStockQuantity) {
        $updateSql = "UPDATE products SET stock_quantity = ? WHERE product_id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("ii", $newStockQuantity, $product_id);
        $updateStmt->execute();
        $updateStmt->close();
    }

    // Clear the user's cart after placing the order
    $clearCartSql = "DELETE FROM cartlist WHERE userid = ?";
    $clearCartStmt = $conn->prepare($clearCartSql);
    $clearCartStmt->bind_param("s", $userid);
    $clearCartStmt->execute();
    $clearCartStmt->close();

    echo "주문이 완료되었습니다.";
} else {
    echo "로그인이 필요합니다.";
}
?>
