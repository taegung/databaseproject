<?php
include 'config.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>주문 내역</title>
    <link rel="stylesheet" href="a.css">
</head>
<body>

<?php
if (isset($_SESSION["userid"])) {
    $userid = $_SESSION["userid"];

    // Fetch orders for the logged-in user through order_details
    $sql = "SELECT order_details.*, products.product_name, orders.order_date
            FROM order_details
            INNER JOIN orders ON order_details.order_id = orders.order_id
            INNER JOIN products ON order_details.product_id = products.product_id
            WHERE orders.userid = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<h2>주문 내역</h2>";
        echo "<table border='1'>";
        echo "<tr><th>주문 번호</th><th>제품명</th><th>수량</th><th>가격</th><th>주문 일자</th></tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['order_id']}</td>";
            echo "<td>{$row['product_name']}</td>";
            echo "<td>{$row['quantity']}</td>";
            echo "<td>{$row['total_price']}원</td>";
            echo "<td>{$row['order_date']}</td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "주문 내역이 없습니다.";
    }

    $stmt->close();
} else {
    echo "로그인이 필요합니다.";
}
?>

</body>
</html>
