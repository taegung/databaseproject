<?php
include 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the user is logged in
    if (isset($_SESSION["userid"])) {
        $userid = $_SESSION["userid"];

        // Iterate through the cart items and insert them into the "orders" table
        $sqlInsertOrder = "INSERT INTO orders (userid, product_id, quantity, total_price) VALUES (?, ?, ?, ?)";
        $stmtInsertOrder = $conn->prepare($sqlInsertOrder);

        $sqlDeleteCart = "DELETE FROM cartlist WHERE user_product_id = ?";
        $stmtDeleteCart = $conn->prepare($sqlDeleteCart);

        $totalPrice = 0;

        foreach ($_POST['items'] as $item) {
            $product_id = $item['product_id'];
            $quantity = $item['quantity'];
            $productPrice = $item['price'] * $quantity;

            // Insert order details into the "orders" table
            $stmtInsertOrder->bind_param("siii", $userid, $product_id, $quantity, $productPrice);
            $stmtInsertOrder->execute();

            // Delete the item from the cartlist
            $stmtDeleteCart->bind_param("i", $item['user_product_id']);
            $stmtDeleteCart->execute();

            $totalPrice += $productPrice;
        }

        $stmtInsertOrder->close();
        $stmtDeleteCart->close();

        // Optionally, you may want to include additional logic or validation here

        // Return a success response
        echo json_encode(["success" => true, "total_price" => $totalPrice]);
    } else {
        // Return an error response if the user is not logged in
        echo json_encode(["success" => false, "error" => "User not logged in"]);
    }
} else {
    // Return an error response for invalid request method
    echo json_encode(["success" => false, "error" => "Invalid request method"]);
}
?>
