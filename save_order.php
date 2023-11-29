<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the userid, product_id, and quantity are set in the POST data
    if (isset($_POST['userid']) && isset($_POST['product_id']) && isset($_POST['quantity'])) {
        $userid = $_POST['userid'];
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];

        // Check if a record already exists for the user and product
        $existingStmt = $conn->prepare("SELECT * FROM cartlist WHERE userid = ? AND product_id = ?");
        $existingStmt->bind_param("si", $userid, $product_id);
        $existingStmt->execute();
        $existingResult = $existingStmt->get_result();

        if ($existingResult->num_rows > 0) {
            // If the record exists, update the quantity
            $updateStmt = $conn->prepare("UPDATE cartlist SET quantity = quantity + ? WHERE userid = ? AND product_id = ?");
            $updateStmt->bind_param("isi", $quantity, $userid, $product_id);

            if ($updateStmt->execute()) {
                echo json_encode(["success" => true]);
            } else {
                echo json_encode(["success" => false, "error" => "Failed to update quantity"]);
            }

            $updateStmt->close();
        } else {
            // If the record does not exist, insert a new record
            $insertStmt = $conn->prepare("INSERT INTO cartlist(userid, product_id, quantity) VALUES (?, ?, ?)");
            $insertStmt->bind_param("sii", $userid, $product_id, $quantity);

            if ($insertStmt->execute()) {
                echo json_encode(["success" => true]);
            } else {
                echo json_encode(["success" => false, "error" => "Failed to save order"]);
            }

            $insertStmt->close();
        }

        $existingStmt->close();
    } else {
        // Return an error response if userid, product_id, or quantity is not set
        echo json_encode(["success" => false, "error" => "Invalid request"]);
    }
} else {
    // Return an error response for invalid request method
    echo json_encode(["success" => false, "error" => "Invalid request method"]);
}
?>
