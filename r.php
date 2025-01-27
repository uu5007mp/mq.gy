<?php
// データベース接続の設定
$servername = "localhost";
$username = "ユーザー名";
$password = "パスワード";
$dbname = "url_shortener";

// データベース接続を作成
$conn = new mysqli($servername, $username, $password, $dbname);

// 接続確認
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// クエリパラメーターを使ったリダイレクト処理
if (isset($_GET['1'])) {
    $shortened_key = $_GET['1'];

    // データベースから元のURLを取得
    $stmt = $conn->prepare("SELECT original_url FROM urls WHERE shortened_url = ?");
    $stmt->bind_param("s", $shortened_key);
    $stmt->execute();
    $stmt->bind_result($original_url);

    if ($stmt->fetch()) {
        // 元のURLにリダイレクト
        header("Location: " . $original_url);
        exit();
    } else {
        echo "指定されたURLは見つかりませんでした。";
    }

    $stmt->close();
}

// データベース接続を閉じる
$conn->close();
?>
