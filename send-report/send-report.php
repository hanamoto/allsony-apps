<?php

// https://github.com/PHPMailer/PHPMailer/tree/5.2-stable
require './lib/PHPMailer-5.2/PHPMailerAutoload.php';

include 'account.php';

// 試合結果を保存するディレクトリ
define("REPORT_DIR", "report");

mb_language("Japanese");
mb_internal_encoding("UTF-8");

// HTML メールを送信する
function sendReport($message, $mail_subject, $image_path) {
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
    $mail->Subject = mb_encode_mimeheader($mail_subject, 'ISO-2022-JP');
    $mail->isHTML(true);

    // css は html に直接埋め込む必要がある
    $css = file_get_contents('style.css');

    // 画像が送信されていれば、メールにも添付する
    $image_tag = '';
    if (!empty($image_path)) {
        $mail->addEmbeddedImage(REPORT_DIR . "/" . $image_path, "image.jpg");
        $image_tag = '<img width="480" src="cid:image.jpg">';
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
<table>
<tr valign="top">
<td>$message</td>
<td>$image_tag</td>
</tr>
</table>
</body>
</html>
EOM;
    $mail->msgHTML($htmlMessage);

    if(!$mail->send()) {
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    }
}

// 結果を HTML として保存
function saveHtmlReport($message, $mail_subject, $image_path, $html_path) {
    // 画像が送信されていれば HTML にも表示させる
    $image_tag = '';
    if (!empty($image_path)) {
        $image_tag = "<img width=\"480\" src=\"$image_path\">";
    }

    $html = <<< EOM
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"/>
<title>$mail_subject</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="../style.css">
</head>
<body>
<a href="../view-report.php">試合結果確認ページに戻る</a><br>
<table>
<tr valign="top">
<td>$message</td>
<td>$image_tag</td>
</tr>
</table>
</body>
</html>
EOM;

   file_put_contents($html_path, $html);
}

function main() {
    $message = $_POST["mail_contents"];
    $mail_subject = $_POST["mail_subject"];
    $image_path = "";

    // 送信された画像イメージをファイルに保存する
    $match_name = $_POST["match_name"];
    $match_image = $_POST["match_image"];
    if (!empty($match_image)) {
        // base64デコードする
        $match_image = base64_decode($match_image);
        // まだ文字列の状態なので、画像リソース化
        $image = imagecreatefromstring($match_image);

        $image_path = $match_name . ".jpg";
        imagejpeg($image, REPORT_DIR . "/" . $image_path);
    }

    // メール送信
    sendReport($message, $mail_subject, $image_path);

    // 結果を HTML として保存
    $html_path = REPORT_DIR . "/" . $match_name . ".html";
    saveHtmlReport($message, $mail_subject, $image_path, $html_path);

    // 結果を JSON で返す
    header('Content-Type: application/json');
    $data = "success";
    echo json_encode(compact('data'));
}

// URL に version=1 が指定されていれば、phpinfo を表示する
if (!empty($_GET["version"])) {
    phpinfo();
    return;
}

main();

?>