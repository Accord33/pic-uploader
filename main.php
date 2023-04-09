<?php
// エラー表示を設定する
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function imagecreatefromheic($filename) {
    if (!function_exists('libheif_version')) {
      return false;
    }

    $heif = new \LibHeif\Heif();
    $handle = fopen($filename, 'rb');
    $heif->loadFromHandle($handle);
    $image = $heif->getImageHandle(0);
    $width = $image->getWidth();
    $height = $image->getHeight();

    $gd_image = imagecreatetruecolor($width, $height);
    $row_size = $width * 4;
    $row_pointers = [];
    for ($y = 0; $y < $height; ++$y) {
      $row_pointers[] = new \LibHeif\RowReader($image, $y);
    }
    imagecopyresampled($gd_image, $row_pointers, 0, 0, 0, 0, $width, $height, $width, $height);
    return $gd_image;
  }

// アップロードされたファイルを処理する
if(isset($_FILES['image'])) {
    // 画像ファイルの保存先を指定する
    $target_dir = "./pic/";
    echo gettype($_FILES['image']['name']);

    // アップロードされた画像ファイルの数を取得する
    $num_files = count($_FILES['image']['name']);

    // 画像ファイルを1枚ずつ処理する
    for($i = 0; $i < $num_files; $i++) {
        echo $i;
        // アップロードされた画像ファイルの名前を取得する
        $image_name = $_FILES['image']['name'][$i];
        $name = $_POST['name'];

        $image_name = $name.'-'.uniqid("", true).'-'.$image_name;

        // 画像ファイルの一時ファイル名を取得する
        $image_temp_name = $_FILES['image']['tmp_name'][$i];

        // 画像ファイルの拡張子を取得する
        $image_extension = strtolower(pathinfo($image_name,PATHINFO_EXTENSION));

        // HEIC形式の場合は、JPEG形式に変換する
        if($image_extension == "heic") {
            $image = imagecreatefromheic($image_temp_name);
            imagejpeg($image, $image_temp_name . '.jpg', 90);
            $image_temp_name = $image_temp_name . '.jpg';
            $image_name = substr($image_name, 0, strrpos($image_name, ".")) . ".jpg";
        }
	echo '変換終了　アップロードを開始します';
        // 画像ファイルを保存する
        if (move_uploaded_file($image_temp_name, $target_dir . $image_name)) {
            echo "ファイルをアップロードしました。";
        } else {
            echo 'エラー';
        }
    }

}
?>
<!-- アップロード用のフォームを作成する -->
<form method="post" enctype="multipart/form-data">
    <label>名前: </label><input type="text" name="name"><br>
    <label>画像ファイル: </label><input type="file" name="image[]" multiple><br>
    <input type="submit" value="アップロード">
</form>