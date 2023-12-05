<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userid = $_POST["userid"];
    $password = $_POST["password"];

    // 입력 값으로 데이터베이스에서 사용자 확인
    $sql = "SELECT userid, password FROM user WHERE userid = '$userid'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashedPassword = $row["password"];

        // 패스워드 확인
        if (password_verify($password, $hashedPassword)) {
            $_SESSION["userid"] = $row["userid"];
            header("Location: index.php"); // 로그인 성공 시 이동할 페이지
            exit();
        } else {
            $error = "비밀번호가 올바르지 않습니다.";
        }
    } else {
        $error = "유효하지 않은 사용자명입니다.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>로그인 페이지</title>
</head>
<body>

<h2>로그인</h2>

<form method="post" action="<?php echo $_SERVER["PHP_SELF"];?>">
    사용자ID: <input type="text" name="userid"><br>
    비밀번호: <input type="password" name="password"><br>
    <input type="submit" value="로그인">
</form>

<?php
// 로그인 실패 시 에러 메시지 출력
if (isset($error)) {
    echo "<p style='color:red;'>$error</p>";
}
?>

</body>
</html>
