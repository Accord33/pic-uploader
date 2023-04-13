<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="shortcut icon" href="https://cdn.discordapp.com/attachments/828286173700816947/976693328487792651/3_20220519125016.png">
</head>
<body>
<?php
// picフォルダの中のjpgファイルを取得する
$files = glob("pic/*");

// 画像を表示する
foreach ($files as $file) {
    echo "<img style='width:20%;height:auto;' src='$file' alt='pic'>";
}
?>
</body>
</html>