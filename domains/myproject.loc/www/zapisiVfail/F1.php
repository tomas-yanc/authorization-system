<?php

// Подключаемся к файлу F2 и записываем в него

$wr = 'Hello world';
$wri = fopen (__DIR__ . '/F2.php', 'w');
fputs($wri, $wr);
fclose($wri);