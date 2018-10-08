<?php

// https://github.com/PHPMailer/PHPMailer/tree/5.2-stable
require './PHPMailer-5.2/PHPMailerAutoload.php';

define("MAIL_TO",       "allsony.report@gmail.com");
//define("MAIL_FROM",     "allsony.report@gmail.com");
define("MAIL_FROM",     "allsony@infostc.org");
define("MAIL_SUBJECT",  "[AllsonyReport]");

// 試合結果のイメージを保存するディレクトリ
define("MATCH_IMG_DIR", "match_img");

mb_language("Japanese");
mb_internal_encoding("UTF-8");

// HTML メールを送信する
function sendReport($message, $image_path) {
    $mail = new PHPMailer;
    $mail->addAddress(MAIL_TO); // Add a recipient
    $mail->setFrom(MAIL_FROM);
    $mail->Subject = MAIL_SUBJECT;
    $mail->isHTML(true);

    // css は html に直接埋め込む必要がある
    $css = file_get_contents('report.css');

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
    $mail->Body = $htmlMessage;
    /*
    if(!$mail->send()) {
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    }
*/
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
<link rel="stylesheet" type="text/css" href="report.css">
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