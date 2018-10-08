<?php

// https://github.com/PHPMailer/PHPMailer/tree/5.2-stable
require './PHPMailer-5.2/PHPMailerAutoload.php';

define("MAIL_TO",       "allsony.report@gmail.com");
//define("MAIL_FROM",     "allsony.report@gmail.com");
define("MAIL_FROM",     "allsony@infostc.org");
define("MAIL_SUBJECT",  "[AllsonyReport]");

mb_language("Japanese");
mb_internal_encoding("UTF-8");

// HTML メールを送信する
function sendReport($message) {
    $mail = new PHPMailer;
    $mail->addAddress(MAIL_TO); // Add a recipient
    $mail->setFrom(MAIL_FROM);
    $mail->Subject = MAIL_SUBJECT;
    $mail->isHTML(true);

    // css は html に直接埋め込む必要がある
    $css = file_get_contents('report.css');

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
    $mail->Body = $htmlMessage;
    $mail->send();
}

// 送信した内容をブラウザ画面に表示する
function displayMessage($message) {
    echo <<< EOM
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"/>
<title>Allsony Report</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="report.css">
</head>
<body>
<h2 color="red">結果を送信しました</h2>
<div>
$message
</div>
</body>
</html>
EOM;
}

function main() {
    $message = $_POST["mailContents"];

    // 画像が添付されていればファイルに保存する
    if ($_POST["match_name"] != "") {
        if (is_uploaded_file($_FILES['picture']['tmp_name'])) {
            if (move_uploaded_file($_FILES['picture']['tmp_name'], $uploadfile)) {
                //$_FILES['picture']['tmp_name']
            }
        }
    }

    // 送信した内容をブラウザ画面に表示
    displayMessage($message);

    // メール送信
    sendReport($message);
}

main();

?>