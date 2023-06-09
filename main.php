<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title><link rel="shortcut icon" href="https://cdn.discordapp.com/attachments/828286173700816947/976693328487792651/3_20220519125016.png">
    <!--スタイルシートの指定を追加-->
    <link rel="stylesheet" href="main.css">
</head>
<body >

    <?php
    // エラー表示を設定する
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $stack = array();

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

        // アップロードされた画像ファイルの数を取得する
        $num_files = count($_FILES['image']['name']);
        // 画像ファイルを1枚ずつ処理する
        for($i = 0; $i < $num_files; $i++) {
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
            // 画像ファイルを保存する
            if (move_uploaded_file($image_temp_name, $target_dir . $image_name))
	        {
                array_push($stack, true);
            } else {
                array_push($stack, false);
            }
        }
        if (in_array(false, $stack, true)) { // 配列にfalseがあるかチェック
            echo "<script>alert('正常にアップロードされませんでした。')</script>";  // falseがある場合は"No"を返す
        } else {
            echo "<script>alert('{$num_files}枚の画像が正常にアップロードされました。')</script>"; // falseがない場合は"Yes"を返す
        }
        $stack = array();
    }
?>

    <!--タイトル以下の投稿フォームを画面中央にするためにdivでセンタリング-->
    <div class="container">
    <img src="title.png" class="image_size">
    <!-- アップロード用のフォームを作成する -->
    <form method="post" enctype="multipart/form-data">
        <label>お名前 </label><input type="text" name="name"><br>
        <label>画像の選択 </label><input type="file" name="image[]" multiple><br>
        <input type="submit" value="アップロード">
        <p>※1.画像は一度に10枚まで送信できます。</p>
        <p>※2.動画のアップロードはできません</p>
    </form>
</div>
</body>
</html>