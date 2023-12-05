<?php
include 'config.php';
session_start();

if (isset($_SESSION["userid"])) {
    $userid = $_SESSION["userid"];

    // Fetch cart items for the logged-in user
    $cartSql = "SELECT cartlist.*, products.price, products.stock_quantity, products.sell_quantity
                 FROM cartlist
                 INNER JOIN products ON cartlist.product_id = products.product_id
                 WHERE cartlist.userid = ?";
    $cartStmt = $conn->prepare($cartSql);
    $cartStmt->bind_param("s", $userid);
    $cartStmt->execute();
    $cartResult = $cartStmt->get_result();

    // Initialize variables for order insertion
    $orderSql = "INSERT INTO orders (userid) VALUES (?)";
    $orderStmt = $conn->prepare($orderSql);
    $orderStmt->bind_param("s", $userid);
    $orderStmt->execute();

    // Get the order ID of the newly inserted order
    $orderId = $conn->insert_id;

    // Prepare statement for inserting into order_details
    $orderDetailsSql = "INSERT INTO order_details (order_id, product_id, quantity, total_price) VALUES (?, ?, ?, ?)";
    $orderDetailsStmt = $conn->prepare($orderDetailsSql);

    // Start a transaction to ensure data consistency
    $conn->begin_transaction();

    try {
        // Iterate through cart items and insert into order_details
        while ($cartRow = $cartResult->fetch_assoc()) {
            $product_id = $cartRow['product_id'];
            $quantity = $cartRow['quantity'];
            $product_price = $cartRow['price'];
            $total_price = $quantity * $product_price;

            // Insert into order_details
            $orderDetailsStmt->bind_param("iiii", $orderId, $product_id, $quantity, $total_price);
            $orderDetailsStmt->execute();

            // Update product sell quantity and stock quantity
            $newStockQuantity = $cartRow['stock_quantity'] - $quantity;
            $newSellQuantity = $cartRow['sell_quantity'] + $quantity;

            $updateProductSql = "UPDATE products SET stock_quantity = ?, sell_quantity = ? WHERE product_id = ?";
            $updateProductStmt = $conn->prepare($updateProductSql);
            $updateProductStmt->bind_param("iii", $newStockQuantity, $newSellQuantity, $product_id);
            $updateProductStmt->execute();
        }

        // Commit the transaction
        $conn->commit();

        // Clear the user's cart after placing the order
        $clearCartSql = "DELETE FROM cartlist WHERE userid = ?";
        $clearCartStmt = $conn->prepare($clearCartSql);
        $clearCartStmt->bind_param("s", $userid);
        $clearCartStmt->execute();
        $clearCartStmt->close();

        echo "주문이 완료되었습니다.";
    } catch (Exception $e) {
        // Rollback the transaction if an error occurs
        $conn->rollback();
        echo "주문 처리 중 오류가 발생했습니다. 다시 시도해주세요.";
    }

    $orderStmt->close();
    $orderDetailsStmt->close();
    $cartStmt->close();
} else {
    echo "로그인이 필요합니다.";
}
?>
