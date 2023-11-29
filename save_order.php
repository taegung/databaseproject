<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the userid, product_id, and quantity are set in the POST data
    if (isset($_POST['userid']) && isset($_POST['product_id']) && isset($_POST['quantity'])) {
        $userid = $_POST['userid'];
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];

        // Use a prepared statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO orders(userid, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("sii", $userid, $product_id, $quantity);

        // Execute the statement
        if ($stmt->execute()) {
            // Return a success response
            echo json_encode(["success" => true]);
        } else {
            // Return an error response
            echo json_encode(["success" => false, "error" => "Failed to save order"]);
        }

        $stmt->close();
    } else {
        // Return an error response if userid, product_id, or quantity is not set
        echo json_encode(["success" => false, "error" => "Invalid request"]);
    }
} else {
    // Return an error response for invalid request method
    echo json_encode(["success" => false, "error" => "Invalid request method"]);
}
?>
