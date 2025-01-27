<?php
session_start();

// IPBANチェックを実行
include('ipban1.php');
?>

<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

// データベース接続情報
$servername = "localhost";
$username = "ユーザー名";
$password = "パスワード";
$dbname = "url_shortener";

// データベース接続
$conn = new mysqli($servername, $username, $password, $dbname);

// 接続エラーチェック
if ($conn->connect_error) {
    echo json_encode(['error' => '接続失敗: ' . $conn->connect_error]);
    exit();
}

// クライアントIPアドレスを取得する関数
function getClientIP() {
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        return $_SERVER['HTTP_CF_CONNECTING_IP']; // Cloudflare経由のIP
    }
    return $_SERVER['REMOTE_ADDR']; // Cloudflareが使われていない場合のフォールバック
}

// クライアントのIPアドレスを取得
$ip_address = getClientIP();

// リクエスト制限のチェック
$time_limit_seconds = 20; // 20秒の制限
$stmt = $conn->prepare("SELECT UNIX_TIMESTAMP(last_request) AS last_request_time FROM request_logs WHERE ip_address = ?");
$stmt->bind_param("s", $ip_address);
$stmt->execute();
$result = $stmt->get_result();
$current_time = time();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $last_request_time = $row['last_request_time'];

    // 秒単位で比較
    if (($current_time - $last_request_time) < $time_limit_seconds) {
        echo json_encode(['error' => '20秒に一回しか使えません']);
        $stmt->close();
        $conn->close();
        exit();
    }

    // 更新クエリ
    $stmt = $conn->prepare("UPDATE request_logs SET last_request = NOW() WHERE ip_address = ?");
    $stmt->bind_param("s", $ip_address);
    $stmt->execute();
} else {
    // 新規レコードの挿入
    $stmt = $conn->prepare("INSERT INTO request_logs (ip_address, last_request) VALUES (?, NOW())");
    $stmt->bind_param("s", $ip_address);
    $stmt->execute();
}
$stmt->close();

// 短縮URLを生成する関数
function generateShortenedURL($length = 7) {
    return substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
}

// メインロジック
if (isset($_GET['url'])) {
    $original_url = $_GET['url'];

    // URLを解析
    $parsed_url = parse_url($original_url);

    if (!$parsed_url) {
        echo json_encode(['error' => '無効なURLです。']);
        exit();
    }

    $protocol = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
    $host = isset($parsed_url['host']) ? $parsed_url['host'] : '';
    $path = isset($parsed_url['path']) ? $parsed_url['path'] : '';
    $query = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
    $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';

    $punycodeHost = idn_to_ascii($host, 0, INTL_IDNA_VARIANT_UTS46);
    $full_punycode_url = $protocol . $punycodeHost . $path . $query . $fragment;

    if (filter_var($full_punycode_url, FILTER_VALIDATE_URL)) {
        $shortened_url = generateShortenedURL();
        $full_shortened_url = "https://mq.gy/$shortened_url";

        // URLをデータベースに保存
        $stmt = $conn->prepare("INSERT INTO urls (original_url, shortened_url, ip_address) VALUES (?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sss", $full_punycode_url, $shortened_url, $ip_address);
            if ($stmt->execute()) {
                $response = ['shortened_url' => $full_shortened_url];
            } else {
                $response = ['error' => 'クエリ実行エラー: ' . $stmt->error];
            }
            $stmt->close();
        } else {
            $response = ['error' => 'ステートメント準備エラー: ' . $conn->error];
        }
    } else {
        $response = ['error' => '無効なURLです。'];
    }
} else {
    $response = ['error' => 'URLパラメータが不足しています'];
}

$conn->close();

// JSONレスポンスを返す
echo json_encode($response, JSON_UNESCAPED_SLASHES);
?>