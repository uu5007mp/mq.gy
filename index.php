<?php
session_start();

// IPBANチェックを実行
include('ipban1.php');
?>

<?php
// データベース接続設定
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

// 初期化
$shortened_url = '';
$error_message = '';

// IPアドレスを取得する関数
function getClientIP() {
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        return $_SERVER['HTTP_CF_CONNECTING_IP']; // Cloudflare経由のIP
    }
    return $_SERVER['REMOTE_ADDR']; // Cloudflareが使われていない場合のフォールバック
}

// Turnstile認証をチェックする関数
function verifyTurnstile($token) {
    $secretKey = "シークレット キー"; // Cloudflareのシークレットキーを設定
    $url = "https://challenges.cloudflare.com/turnstile/v0/siteverify";
    $data = [
        'secret' => $secretKey,
        'response' => $token
    ];

    // cURLを使用してTurnstile認証を検証
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    return isset($result['success']) && $result['success'] === true;
}

// POSTリクエストの処理
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['url']) && isset($_POST['cf-turnstile-response'])) {
    $original_url = $_POST['url'];
    $turnstile_token = $_POST['cf-turnstile-response'];

    // Turnstile認証を確認
    if (!verifyTurnstile($turnstile_token)) {
        $_SESSION['error_message'] = "Turnstile認証に失敗しました。";
    } else {
        // URLの検証（簡単なバリデーション）
        if (!filter_var($original_url, FILTER_VALIDATE_URL)) {
            $_SESSION['error_message'] = "無効なURLです。もう一度入力してください。";
        } else {
            // 短縮URLを生成する（ランダムな6文字の文字列を生成）
            $shortened_key = substr(md5($original_url . time()), 0, 6);
            $ip_address = getClientIP(); // クライアントIPアドレスを取得

            // データベースに保存するSQL
            $stmt = $conn->prepare("INSERT INTO urls (original_url, shortened_url, ip_address) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $original_url, $shortened_key, $ip_address);

            if ($stmt->execute()) {
                $_SESSION['shortened_url'] = "https://mq.gy/" . $shortened_key;
            } else {
                $_SESSION['error_message'] = "URLの保存に失敗しました。";
            }

            $stmt->close();
        }
    }

    // POSTリクエスト後にリダイレクトして、フォームの再送信を防ぐ
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// セッションから結果を取得し、セッションをクリア
if (isset($_SESSION['shortened_url'])) {
    $shortened_url = $_SESSION['shortened_url'];
    unset($_SESSION['shortened_url']);
}
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
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



<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URL短縮サービス - mq.gy</title>
    <meta name="description" content="mq.gy のURL短縮サービスを使って、長いURLを短縮し、簡単に共有できます。無料で簡単にURLを短縮し、リンクの管理も可能です。">
    <meta property="og:image" content="https://mq.gy/image/preview-image.png">
    <meta property="og:image:width" content="600"> 
    <meta property="og:image:height" content="600">
    <meta property="og:image:type" content="image/png">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="URL短縮サービス - mq.gy">
    <meta name="twitter:description" content="mq.gy のURL短縮サービスを使って、長いURLを短縮し、簡単に共有できます。">
    <meta name="twitter:image" content="https://mq.gy/image/preview-image.png">
    <meta name="twitter:url" content="https://mq.gy">

    <link rel="icon" href="https://mq.gy/image/favicon.ico">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 100%;
            text-align: center;
            position: relative;
        }
        h1 {
            color: #38b3ff;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        input[type="text"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        input[type="submit"] {
            background-color: #38b3ff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #2a9fd6;
        }
        .result, .error {
            margin-top: 20px;
            font-size: 16px;
        }
        .result a {
            color: #38b3ff;
            text-decoration: none;
        }
        .error {
            color: red;
        }
        .copy-btn {
            background-color: #38b3ff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: inline-block;
            text-align: center;
            line-height: 1.5;
            margin-top: 10px;
        }
        .copy-btn:hover {
            background-color: #2a9fd6;
        }

        .extension-button {
            position: fixed;
            bottom: 10px;
            right: 10px;
            width: 100px;
            height: 100px;
            background-color: #ff5722;
            color: #fff;
            border: none;
            border-radius: 50%;
            font-size: 14px;
            cursor: pointer;
            text-align: center;
            transition: background-color 0.3s ease;
            z-index: 1000;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .extension-button:hover {
            background-color: #e64a19;
        }

        .extension-button a {
            color: #fff;
            text-decoration: none;
            display: block;
            width: 100%;
            height: 100%;
            text-align: center;
            line-height: 100px;
        }

        .notification {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: #ff5722;
            color: #fff;
            font-weight: bold;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }
    </style>
</head>
<body>
    <div class="notification">
        お知らせ: botで追加したと思われるURLを削除致しました。
    </div>

    <div class="container">
        <h1>短縮URLmq.gy</h1>

        <form method="post" action="">
            <input type="text" id="urlInput" name="url" placeholder="ここにURLを入力" required>
            <div class="cf-turnstile" data-sitekey="サイト キー"></div>
            <input type="submit" value="短縮する">
        </form>

        <?php if ($shortened_url): ?>
            <div class="result">
                短縮されたURL: <a href="<?php echo htmlspecialchars($shortened_url); ?>"><?php echo htmlspecialchars($shortened_url); ?></a>
                <button class="copy-btn" onclick="copyToClipboard('<?php echo htmlspecialchars($shortened_url); ?>')">コピーする</button>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="api-link">
            <p>※APIもあります。詳細は <a href="https://mq.gy/apitukaikata.php">こちら</a> をご覧ください。</p>
            <p>プライバシーポリシーは <a href="https://mq.gy/privacy.php">こちら</a> からご覧いただけます。</p>
        </div>

        <button class="extension-button">
            <a href="https://chromewebstore.google.com/detail/%E7%9F%AD%E7%B8%AEURL%20mq.gy/jmpjlknbjidegmkajpplfealcgnbcnja?hl=ja&authuser=0" target="_blank">
                拡張機能公開!<br>
            </a>
        </button>

        <script>
            function copyToClipboard(text) {
                const tempInput = document.createElement('input');
                document.body.appendChild(tempInput);
                tempInput.value = text;
                tempInput.select();
                document.execCommand('copy');
                document.body.removeChild(tempInput);
                alert('URLがコピーされました: ' + text);
            }
        </script>
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    </div>
</body>
</html>
