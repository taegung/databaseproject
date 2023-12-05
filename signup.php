<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userid = $_POST["userid"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $Address = $_POST["useraddress"]; // 추가: 배송지
    $gender = $_POST["gender"]; // 추가: 성별

    // 비밀번호 해싱
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // 데이터베이스에 데이터 삽입
    $sql = "INSERT INTO user (userid, username, password,useraddress, gender) 
            VALUES ('$userid', '$username', '$hashedPassword', '$Address', '$gender')";

    if ($conn->query($sql) === TRUE) {
        echo "회원 가입이 성공적으로 완료되었습니다.";

        // 회원 가입 성공 시 로그인 페이지로 이동
        header("Location: index.php");
        exit();
    } else {
        echo "회원 가입 오류: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>회원 가입 페이지</title>
</head>
<body>

<h2>회원 가입</h2>

<form method="post" action="<?php echo $_SERVER["PHP_SELF"];?>">
    사용자아이다: <input type="text" name="userid"><br>
    실제 이름: <input type="text" name="username"><br>
    비밀번호: <input type="password" name="password"><br>
    배송지: <input type="text" name="useraddress"><br> <!-- 추가: 배송지 입력 -->
    성별: 
    <input type="radio" name="gender" value="남자"> 남자
    <input type="radio" name="gender" value="여자"> 여자<br> <!-- 추가: 성별 입력 -->
    <input type="submit" value="회원 가입">
</form>

</body>
</html>
