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
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        nav {
            background-color: #333;
            color: #fff;
            padding: 10px;
        }

        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: space-around;
        }

        nav ul li {
            display: inline;
            background-color: #808080; /* Gray background color */
            padding: 10px;
            border-radius: 5px;
            margin: 0 5px;
        }

        nav ul li a {
            text-decoration: none;
            color: #fff;
        }

        div {
            margin: 20px;
        }

        h2 {
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #333;
            color: #fff;
        }

        .order-button {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<nav>
    <ul>
        <li><a href="#">홈</a></li>
        <li><a href="#">서비스</a></li>
        <li><a href="#">문의</a></li>
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
    <table>
        <thead>
            <tr>
                <th>상품명</th>
                <th>가격</th>
                <th>재고량</th>
                <th>주문하기</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // 상품 목록 출력
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['product_name']}</td>";
                echo "<td>{$row['price']}원</td>";
                echo "<td>{$row['stock_quantity']}</td>";
                echo "<td><a class='order-button' href='#'>주문하기</a></td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>
