<?php
// picフォルダの中のjpgファイルを取得する
$files = glob("pic/*");

// 画像を表示する
foreach ($files as $file) {
    echo "<img style='max-width:50%;height:auto;' src='$file' alt='pic'>";
}
?>