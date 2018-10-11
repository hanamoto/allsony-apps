<?php

// 試合結果を保存するディレクトリ
define("REPORT_DIR", "report");

mb_language("Japanese");
mb_internal_encoding("UTF-8");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"/>
<title>Allsony送信結果確認ページ</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<header>
  <h3>Allsony送信結果確認ページ</h3>
</header>
<?php
foreach(glob(REPORT_DIR . '/*.html') as $file) {
    if (is_file($file)) {
        // ファイルを読み込む
        $contents = file_get_contents($file);
        // title を取り出す
        if (preg_match("/<title>(.*)<\\/title>/", $contents, $matches)) {
            $title = $matches[1];
        } else {
            $title = $file;
        }

        echo "<a href=\"$file\">$title</a><br>";
    }
}
?>

</body>
</html>
