<?php

if (!empty($_FILES['attachment'])) {
    $file = $_FILES['attachment'];

    $srcFileName = $file['name'];
    $newFilePath = __DIR__ . '/uploads/' . $srcFileName;

    //$myMaxSize = 200000;
    // Получаем размер файла с помощью функции
    // $myFileSize = filesize($file['tmp_name']);
    // echo $myFileSize;

    $myFileSize = $_FILES['attachment']['size'];
    echo $myFileSize;
    echo '<br><br>';

    $x = 280;
    $y = 120;

    $mySize = getimagesize($_FILES['attachment'] ['tmp_name']);
    var_dump($mySize);

    echo '<br><br>';

    $allowedExtensions = ['jpg', 'png', 'gif'];
    $extension = pathinfo($srcFileName, PATHINFO_EXTENSION);
    if (!in_array($extension, $allowedExtensions)) {
        $error = 'Загрузка файлов с таким расширением запрещена!';
    } elseif ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'Ошибка загрузки файла.';
    } elseif (file_exists($newFilePath)) {
        $error = 'Файл с таким именем уже существует';
    } elseif (!move_uploaded_file($file['tmp_name'], $newFilePath)) {
        $error = 'Ошибка при загрузке файла';
    } /*elseif ($myFileSize >= $myMaxSize) {
        $error = 'Файл слишком большого размера';
    }*/ elseif ($mySize [0] > $x || $mySize [1] > $y) {
        $error = 'Слишком большая ширина или высота файла.';
    }
      else {
        $result = 'http://myproject.loc/uploads/' . $srcFileName;
    }
}
?>
<html>
<head>
    <title>Загрузка файла</title>
</head>
<body>
<?php if (!empty($error)): ?>
    <?= $error ?>
<?php elseif (!empty($result)): ?>
    <?= $result ?>
<?php endif; ?>
<br>
<?php echo '<br><br>';?>
<form action="/upload.php" method="post" enctype="multipart/form-data">
    <input type="file" name="attachment">
    <input type="submit">
</form>
</body>
</html>

<?php
var_dump($_FILES['attachment']['error']);
?>