<?php
session_start();
$ban_reason = isset($_SESSION['ban_reason']) ? $_SESSION['ban_reason'] : null;
unset($_SESSION['ban_reason']);  // 一度表示したら理由を削除
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>アクセス禁止</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            text-align: center;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #d9534f;
        }
        p {
            font-size: 18px;
        }
        .reason {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>アクセスが禁止されています</h1>
        <p>あなたのIPアドレスは現在アクセスが制限されています。</p>

        <?php if ($ban_reason): ?>
            <div class="reason">
                <p><strong>理由:</strong> <?php echo htmlspecialchars($ban_reason); ?></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
