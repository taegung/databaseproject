<?php
session_start();
include 'config.php';

if (isset($_GET["logout"])) {
    session_destroy(); 
    header("Location: index.php");
    exit();
}


if (isset($_SESSION["userid"])) {
    $welcome_message = "{$_SESSION["userid"]}님!";
    $button_label = "로그아웃";
    $button_action = "index.php?logout=1"; 
} else {
    $welcome_message = "로그인이 필요합니다.";
    $button_label = "로그인";
    $button_action = "login.php"; 
}


$sql = "SELECT * FROM products";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>메인 화면</title>
    <link href="a.css" rel="stylesheet" type="text/css" />
    
</head>
<body>

<nav>
    <ul>
        <li><a href="index.php">홈</a></li>
        
        <?php if (isset($_SESSION["userid"])) { ?>
            <li><a href="<?php echo $button_action; ?>"><?php echo $button_label; ?></a></li>
            <li><?php echo $welcome_message; ?></li>
            <li><a href="orderlist.php">장바구니</a></li>
            <li><a href="a.php">주문목록</a></li>
        <?php } else { ?>
            <li><a href="signup.php">회원가입</a></li>
            <li><a href="login.php">로그인</a></li>
        <?php } ?>
    </ul>
</nav>


<div>
    <h2>상품 목록</h2>

    <div>
        <label>카테고리 선택:</label>
        <select id="categoryFilter">
            <option value="">전체</option>
            <?php
      
            $categoryQuery = "SELECT * FROM categories";
            $categoryResult = $conn->query($categoryQuery);

            while ($categoryRow = $categoryResult->fetch_assoc()) {
                echo "<option value='{$categoryRow['category_id']}'>{$categoryRow['category_name']}</option>";
            }
            ?>
        </select>
        <button onclick="applyCategoryFilter()">적용</button>
    </div>


<table id="productTable">
    <thead>
        <tr>
            <th>상품명</th>
            <th>가격</th>
            <th>재고량</th>
            <th>카테고리</th>
            <th>수량</th> 
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php
     
        $sql = "SELECT products.*, categories.category_name 
                FROM products 
                LEFT JOIN categories ON products.category_id = categories.category_id";
        $result = $conn->query($sql);

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['product_name']}</td>";
            echo "<td>{$row['price']}원</td>";
            echo "<td>{$row['stock_quantity']}</td>";
            echo "<td>{$row['category_name']}</td>";
            echo "<td><input type='number' id='quantity{$row['product_id']}' value='1' min='1'></td>";
            echo "<td><a class='order-button' href='#' onclick='orderProduct({$row['product_id']})'>장바구니담기</a></td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>
<div>
    <h2>인기상품 순위</h2>
    <table id="popularProductTable">
        <thead>
            <tr>
                <th>순위</th>
                <th>상품명</th>
                <th>주문량</th>
            </tr>
        </thead>
        <tbody>
            <?php
          
            $popularProductsQuery = "
            SELECT
            RANK() OVER (ORDER BY products.sell_quantity DESC) AS product_rank,
            products.product_name,
            products.sell_quantity AS total_quantity
        FROM
            products
        ORDER BY
            total_quantity DESC
        LIMIT 5"; // Adjust the LIMIT as needed

            $popularProductsResult = $conn->query($popularProductsQuery);
            
            $rank = 1;
            while ($popularProduct = $popularProductsResult->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$rank}</td>";
                echo "<td>{$popularProduct['product_name']}</td>";
                echo "<td>{$popularProduct['total_quantity']}</td>";
                echo "</tr>";
                $rank++;
            }
            ?>
        </tbody>
    </table>
</div>


</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
 function applyCategoryFilter() {
    var categoryName = document.getElementById("categoryFilter").value;

    // Include the session ID in the data
    var sessionId = "<?php echo session_id(); ?>";

    // Use AJAX to fetch updated product data
    $.ajax({
        type: "POST",
        url: "filter_products.php",
        data: { category: categoryName, PHPSESSID: sessionId },
        success: function (data) {
            // Replace the tbody content with the updated product data
            $("#productTable tbody").html(data);
        },
        error: function (xhr, status, error) {
            console.error('Error:', error);
        }
    });
}

  function orderProduct(productId) {
    // Check if the user is logged in
    <?php if (isset($_SESSION["userid"])) { ?>
        var userId = "<?php echo $_SESSION["userid"]; ?>";
        
        // Get the quantity from the input field
        var quantity = $("#quantity" + productId).val();

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
    <?php } else { ?>
        // Redirect the user to the login page if not logged in
        window.location.href = "login.php";
    <?php } ?>
  }
</script>





</body>
</html>
