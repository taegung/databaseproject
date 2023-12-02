<?php
include 'config.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>장바구니</title>
    <link rel="stylesheet" href="a.css">
</head>
<body>

<?php
if (isset($_SESSION["userid"])) {
    // Fetch orders for the logged-in user
    $userid = $_SESSION["userid"];
    $sql = "SELECT cartlist.*, products.product_name, products.price
            FROM cartlist
            INNER JOIN products ON cartlist.product_id = products.product_id
            WHERE cartlist.userid = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<h2>장바구니</h2>";
        echo "<table border='1'>";
        echo "<tr><th>제품명</th><th>수량</th><th>가격</th><th>취소</th></tr>";

        $totalPrice = 0;
        $cartItems = array(); // Initialize an array to store cart items

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['product_name']}</td>";
            echo "<td>{$row['quantity']}</td>";
            $productPrice = $row['price'] * $row['quantity'];
            echo "<td>{$productPrice}원</td>";
            echo "<td><button onclick='cancelOrder({$row['cartlist_id']})'>장바구니 취소</button></td>";
            echo "</tr>";

            $totalPrice += $productPrice;

            // Add item to cartItems array
            $cartItems[] = $row;
        }
        echo "<button onclick='placeOrder()'>상품 전체 주문</button>";
        echo "<tr><td colspan='3'>총 가격</td><td>{$totalPrice}원</td></tr>";
        echo "</table>";

    } else {
        echo "장바구니가 비어있습니다.";
    }

    $stmt->close();
} else {
    echo "로그인이 필요합니다.";
}
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
    function cancelOrder(cartlistId) {
        var confirmation = confirm("정말로 장바구니에서 제거하시겠습니까?");
        
        if (confirmation) {
            // Use AJAX to delete the order from the database
            $.ajax({
                type: "POST",
                url: "cancel_order.php", // Replace with the actual PHP script to cancel the order
                data: { cartlist_id: cartlistId },
                success: function (data) {
                    // Reload the page to reflect the changes
                    location.reload();
                },
                error: function (xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }
    }

    function placeOrder() {
        var confirmation = confirm("정말로 상품을 주문하시겠습니까?");
        
        if (confirmation) {
            // Use AJAX to place the order
            $.ajax({
                type: "POST",
                url: "place_order.php", // Create this PHP script to handle order placement
                success: function (data) {
                    // If the order is successful, redirect to a.php
                    window.location.href = "a.php";
                },
                error: function (xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }
    }
</script>

</body>
</html>
