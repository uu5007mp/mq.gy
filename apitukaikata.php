<?php
session_start();

// IPBANチェックを実行
include('IPBAN.php');
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APIの使い方 - mq.gy</title>
    <meta name="description" content="mq.gy のAPIを使用してURLを短縮する方法を説明します。APIエンドポイントを使って、短縮URLを簡単に取得できます。">
    <meta name="keywords" content="API, URL短縮, mq.gy">
    <meta name="author" content="Your Name">
    <meta property="og:title" content="APIの使い方 - mq.gy">
    <meta property="og:description" content="mq.gy のAPIを使用してURLを短縮する方法を説明します。APIエンドポイントを使って、短縮URLを簡単に取得できます。">
    <meta property="og:url" content="http://mq.gy/APItukaikata.html">
    <meta property="og:image" content="http://mq.gy/image/preview-image.png">
    <meta property="og:type" content="website">
    <link rel="canonical" href="http://mq.gy/APItukaikata.html">
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
            max-width: 600px;
            width: 100%;
            text-align: center;
        }

        h1 {
            color: #38b3ff;
            margin-bottom: 20px;
        }

        p {
            font-size: 16px;
            line-height: 1.6;
        }

        pre {
            background: #f4f4f4;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
            text-align: left;
        }

        code {
            font-family: monospace;
        }

        .api-link {
            display: block;
            margin-top: 20px;
            font-size: 14px;
            color: #38b3ff;
            text-decoration: none;
        }

        .api-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>APIの使い方</h1>
        <p>mq.gy のURL短縮サービスには、以下のAPIエンドポイントを使用して</p>
        <p>URLを短縮する機能があります。※20秒に一回の制限あり</p>
        <p>APIエンドポイント: <code>https://mq.gy/api.php?url=URL</code></p>
        <p>このエンドポイントに対して、URLパラメータを指定することで、短縮されたURLを取得できます。</p>
        <pre>
{
    "shortened_url": "https://mq.gy/=○○"
}
        </pre>
        <p>もし無効なURLやURLパラメータが不足している場合、以下のエラーが返されます。</p>
        <pre>
{
    "error": "無効なURLです。URLは 'http://' または 'https://' で始まる必要があります。"
}
        </pre>
        <p>上記の JSON レスポンスは、エラー内容を示しています。</p>
        <a href="index.php" class="api-link">戻る</a>
    </div>
</body>
</html>