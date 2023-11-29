<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the user_product_id is set in the POST data
    if (isset($_POST['user_product_id'])) {
        $userProductId = $_POST['user_product_id'];

        // Use a prepared statement to prevent SQL injection
        $stmt = $conn->prepare("DELETE FROM cartlist WHERE user_product_id = ?");
        $stmt->bind_param("i", $userProductId);

        // Execute the statement
        if ($stmt->execute()) {
            // Return a success response
            echo json_encode(["success" => true]);
        } else {
            // Return an error response
            echo json_encode(["success" => false, "error" => "Failed to cancel order"]);
        }

        $stmt->close();
    } else {
        // Return an error response if user_product_id is not set
        echo json_encode(["success" => false, "error" => "Invalid request"]);
    }
} else {
    // Return an error response for invalid request method
    echo json_encode(["success" => false, "error" => "Invalid request method"]);
}
?>
