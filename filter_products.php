<?php
include 'config.php';
if (isset($_POST['PHPSESSID'])) {
    session_id($_POST['PHPSESSID']);
}
session_start();

// Initialize a variable to check if the user is logged in
$isLoggedIn = isset($_SESSION["userid"]);

if (isset($_POST['category'])) {
    $category = $_POST['category'];

    // Use a prepared statement to prevent SQL injection
    if ($category === "") {
        // If "전체" (all) category is selected, retrieve all products
        $sql = "SELECT products.*, categories.category_name 
                FROM products 
                LEFT JOIN categories ON products.category_id = categories.category_id";
    } else {
        // If a specific category is selected, filter by that category
        $sql = "SELECT products.*, categories.category_name 
                FROM products 
                LEFT JOIN categories ON products.category_id = categories.category_id
                WHERE categories.category_id = ?";
    }

    $stmt = $conn->prepare($sql);

    if ($category !== "") {
        // Only bind parameter if a specific category is selected
        $stmt->bind_param("i", $category);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    // Generate HTML for filtered products
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['product_name']}</td>";
        echo "<td>{$row['price']}원</td>";
        echo "<td>{$row['stock_quantity']}</td>";
        echo "<td>{$row['category_name']}</td>";

       
            echo "<td>";
            echo "<input type='number' id='quantity{$row['product_id']}' placeholder='수량' />";
            echo "</td>";
            echo "<td>";
            echo "<a class='order-button' href='#' onclick='orderProduct({$row['product_id']})'>주문하기</a>";
            echo "</td>";
        

        echo "</tr>";
    }

    $stmt->close();
}
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
    function orderProduct(productId) {
        // Check if the user is logged in
        var isLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
        var quantity = $("#quantity" + productId).val();

        if (isLoggedIn) {
            var userId = "<?php echo $_SESSION["userid"]; ?>";

            // Use AJAX to save the order in the user_products table
            $.ajax({
                type: "POST",
                url: "save_cart.php", // Replace with the actual PHP script to save the order
                data: { userid: userId, product_id: productId, quantity: quantity },
                success: function (data) {
                    alert("주문이 성공적으로 저장되었습니다!");
                },
                error: function (xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        } else {
            // Redirect the user to the login page if not logged in
            window.location.href = "login.php";
        }
    }
</script>
