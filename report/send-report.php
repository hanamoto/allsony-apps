<?php

// https://github.com/PHPMailer/PHPMailer/tree/5.2-stable
require './lib/PHPMailer-5.2/PHPMailerAutoload.php';

include 'settings.php';

// 試合結果のイメージを保存するディレクトリ
define("MATCH_IMG_DIR", "match_img");

mb_language("Japanese");
mb_internal_encoding("UTF-8");

// HTML メールを送信する
function sendReport($message, $image_path) {
    $mail = new PHPMailer;

    // デバッグメッセージを有効にする場合は下記を有効にする
    //$mail->SMTPDebug = 2;

    // smtp サーバーを明示的に指定してメール送信する
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->Port = SMTP_PORT;
    $mail->SMTPSecure = 'tls';
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USERNAME;
    $mail->Password = SMTP_PASSWORD;

    $mail->addAddress(MAIL_TO, "allsony");
    $mail->setFrom(MAIL_FROM, "allsony");
    $mail->Subject = "[AllsonyReport]";
    $mail->isHTML(true);

    // css は html に直接埋め込む必要がある
    $css = file_get_contents('lib/report.css');

    // 画像が送信されていれば、メールにも添付する
    if (!empty($image_path)) {
        $mail->addAttachment($image_path);
    }

    $htmlMessage = <<< EOM
<html>
<head>
<meta charset="utf-8"/>
<title>Allsony Report</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style type="text/css">
$css
</style>
</head>
<body>
$message
</body>
</html>
EOM;
    $mail->msgHTML($htmlMessage);

    if(!$mail->send()) {
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    }
}

// 送信した内容をブラウザ画面に表示する
function displayMessage($message, $image_path) {
    echo <<< EOM
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"/>
<title>Allsony Report</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="lib/report.css">
</head>
<body>
<h2 color="red">結果を送信しました</h2>
<div>
$message
<img src="$image_path">
</div>
</body>
</html>
EOM;
}

function main() {
    $message = $_POST["mailContents"];
    $image_path = "";

    // 画像が添付されていればファイルに保存する
    if (!empty($_POST["match_name"])) {
        if (is_uploaded_file($_FILES['match_img']['tmp_name'])) {
            $uploadfile = MATCH_IMG_DIR . "/" . basename($_FILES['match_img']['name']);
            if (move_uploaded_file($_FILES['match_img']['tmp_name'], $uploadfile)) {
                $image_path = $uploadfile;
            }
        }
    }

    // メール送信
    sendReport($message, $image_path);
    // 送信した内容をブラウザ画面に表示
    displayMessage($message, $image_path);

}

// URL に version=1 が指定されていれば、phpinfo を表示する
if (!empty($_GET["version"])) {
    phpinfo();
    return;
}

main();

?>