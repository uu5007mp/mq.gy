<?php
// 出力バッファリングを開始
ob_start();

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

// クライアントのIPアドレスを取得 (Cloudflare経由の場合)
function getClientBANIP() {
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        return $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    return $_SERVER['REMOTE_ADDR']; // Cloudflareが使われていない場合のフォールバック
}

// IPアドレスを取得
$ip_address = getClientBANIP();

// IPアドレスがBANされているか確認
$sql = "SELECT reason FROM IP_BAN WHERE ip_address = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $ip_address);
$stmt->execute();
$stmt->store_result();

// BANされている場合、理由を取得
if ($stmt->num_rows > 0) {
    $stmt->bind_result($ban_reason);
    $stmt->fetch();

    // セッションにBAN理由を保存
    session_start();
    $_SESSION['ban_reason'] = $ban_reason;

    // BANページへリダイレクト
    header("Location: ipban.php");
    exit();  // 必ずexit()で終了
}

// データベース接続を閉じる
$stmt->close();
$conn->close();

// 出力バッファリングを終了し、出力を送信
ob_end_flush();
?>