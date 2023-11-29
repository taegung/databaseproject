<link href="a.css" rel="stylesheet" type="text/css" />
<?php
include 'config.php';
session_start();

if (isset($_SESSION["userid"])) {
    // Fetch orders for the logged-in user
    $userid = $_SESSION["userid"];
    $sql = "SELECT orders.*, products.product_name
            FROM orders
            INNER JOIN products ON orders.product_id = products.product_id
            WHERE orders.userid = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<h2>장바구니</h2>";
        echo "<table border='1'>";
        echo "<tr><th>제품명</th><th>수량</th></tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['product_name']}</td>";
            echo "<td>{$row['quantity']}</td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "장바구니가 비어있습니다.";
    }

    $stmt->close();
} else {
    echo "로그인이 필요합니다.";
}
?>
