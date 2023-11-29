<?php
session_start();
include 'config.php';

// 로그아웃 처리
if (isset($_GET["logout"])) {
    session_destroy(); // 세션 파기
    header("Location: index.php"); // 로그인 페이지로 이동
    exit();
}

// 로그인 여부 확인
if (isset($_SESSION["userid"])) {
    $welcome_message = "안녕하세요, {$_SESSION["userid"]}님!";
    $button_label = "로그아웃";
    $button_action = "index.php?logout=1"; // 로그아웃 처리 링크
} else {
    $welcome_message = "로그인이 필요합니다.";
    $button_label = "로그인";
    $button_action = "login.php"; // 로그인 페이지로 이동
}

// 데이터베이스에서 모든 상품 불러오기
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
        <li><a href="#">홈</a></li>
        <li><a href="#">서비스</a></li>
        <li><a href="#">장바구니</a></li>
        <?php if (isset($_SESSION["userid"])) { ?>
            <li><a href="<?php echo $button_action; ?>"><?php echo $button_label; ?></a></li>
            <li><?php echo $welcome_message; ?></li>
        <?php } else { ?>
            <li><a href="signup.php">회원가입</a></li>
            <li><a href="login.php">로그인</a></li>
        <?php } ?>
    </ul>
</nav>

<!-- 상품 목록 표시 -->
<div>
    <h2>상품 목록</h2>

    <!-- 카테고리 필터링 버튼 -->
    <div>
        <label>카테고리 선택:</label>
        <select id="categoryFilter">
            <option value="">전체</option>
            <?php
            // 카테고리 목록 출력
            $categoryQuery = "SELECT * FROM categories";
            $categoryResult = $conn->query($categoryQuery);

            while ($categoryRow = $categoryResult->fetch_assoc()) {
                echo "<option value='{$categoryRow['category_id']}'>{$categoryRow['category_name']}</option>";
            }
            ?>
        </select>
        <button onclick="applyCategoryFilter()">적용</button>
    </div>

    <!-- 상품 목록 테이블 -->
    <table id="productTable">
        <thead>
            <tr>
                <th>상품명</th>
                <th>가격</th>
                <th>재고량</th>
                <th>카테고리</th>
                <th>주문하기</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // 상품 목록 출력
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
                echo "<td><a class='order-button' href='#' onclick='orderProduct({$row['product_id']})'>주문하기</a></td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
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

            // Use AJAX to save the order in the user_products table
            $.ajax({
                type: "POST",
                url: "save_order.php", // Replace with the actual PHP script to save the order
                data: { userid: userId, product_id: productId },
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
