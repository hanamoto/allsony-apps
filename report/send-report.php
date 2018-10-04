<?php

define("MAIL_TO",       "allsony.report@gmail.com");
define("MAIL_FROM",     "allsony.report@gmail.com");
define("MAIL_SUBJECT",  "[AllsonyReport]");

mb_language("Japanese");
mb_internal_encoding("UTF-8");

// HTML メールを送信する
function sendReport($message) {
    $to      = MAIL_TO;
    $subject = MAIL_SUBJECT;
    $headers = "From: " . MAIL_FROM;
    $headers .= "\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8";

    $htmlMessage = <<< EOM
<html>
<head>
<meta charset="utf-8"/>
<title>Allsony Report</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
$message
</body>
</html>
EOM;
    $htmlMessage = wordwrap($htmlMessage, 70);
    mail($to, $subject, $htmlMessage, $headers);
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
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
</head>
<body>
$message
</body>
</html>
EOM;
}

function main() {
    $message = $_POST["mailContents"];

    // 送信した内容をブラウザ画面に表示
    displayMessage($message);

    // メール送信
    sendReport($message);
}

main();

?>