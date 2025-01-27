<?php
session_start();

// IPBANチェックを実行
include('ipban1.php');
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>プライバシーポリシー - mq.gy</title>

    <!-- SEO対策のためのmetaタグを追加 -->
    <meta name="description" content="mq.gyのプライバシーポリシーの詳細情報。収集する情報、使用目的、第三者への情報提供について説明しています。">
    <meta name="keywords" content="プライバシーポリシー, mq.gy, 情報収集, 個人情報保護">
    <meta name="robots" content="index, follow">
    <meta property="og:image" content="https://mq.gy/image/preview-image.png">
    <link rel="icon" href="https://mq.gy/image/favicon.ico">


    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header, footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px 0;
        }

        main {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
        }

        h1 {
            color: white; /* h1の文字色を白に設定 */
            background-color: #333; /* 背景色を追加して調整 */
            padding: 10px 0;
        }

        h2 {
            color: #333;
        }

        p, ul {
            margin-bottom: 20px;
        }

        ul {
            list-style-type: disc;
            padding-left: 20px;
        }

        a {
            color: #1a73e8;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        /* トップに戻るボタンのスタイル */
        .top-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #1a73e8;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }

        .top-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <header>
        <h1>プライバシーポリシー</h1>
        <p>最終更新日: 2024年9月17日</p>
    </header>

    <main>
        <section>
            <h2>はじめに</h2>
            <p>mq.gy（以下、「当サイト」）に置けるプライバシーポリシーついて説明します。</p>
        </section>

        <section>
            <h2>収集する情報</h2>
            <p>当サイトは、次の情報を収集することがあります:</p>
            <ul>
                <li>ユーザーがアクセスしたサイトのURL</li>
                <li>ユーザーのIPアドレス</li>
            </ul>
        </section>

        <section>
            <h2>情報の使用目的</h2>
            <p>収集した情報は以下の目的で使用されます:</p>
            <ul>
                <li>短縮URLの生成</li>
                <li>悪用された場合のIPBANや犯罪に使用された場合の資料</li>
            </ul>
        </section>

        <section>
            <h2>第三者への情報提供</h2>
            <p>当サイトは、ユーザーの同意がない限り、収集した個人情報を第三者に提供することはありません。ただし、法令に基づく場合などの例外があります。</p>
        </section>

        <section>
            <h2>プライバシーポリシーの変更</h2>
            <p>当サイトは、必要に応じてこのプライバシーポリシーを変更することがあります。変更があった場合、このページでお知らせします。</p>
        </section>

        <section>
            <h2>お問い合わせ</h2>
            <p>プライバシーに関するご質問やお問い合わせは、以下のメールアドレスまでご連絡ください: uu5007mp@gmail.com</p>
        </section>

        <!-- トップに戻るボタンを追加 -->
        <a href="https://mq.gy" class="top-button">トップに戻る</a>
    </main>

    <footer>
        <p>&copy; 2024 mq.gy - All rights reserved.</p>
    </footer>
</body>
</html>
