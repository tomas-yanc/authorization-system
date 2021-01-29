<?php
echo 'Привет!';

// Подключение к базе

$dbh = new \PDO(
    'mysql:host=localhost;dbname=my_db;',
    'root',
    'root'
);

$dbh->exec('SET NAMES UTF8');

// Выполнение запроса (добавляем в базу)

$stm = $dbh->prepare('INSERT INTO users (`email`, `name`) VALUES (:email, :name)');
$stm->bindValue('email', 'x100@webshake.ru');
$stm->bindValue('name', 'Вячеслав');
$stm->execute();

// Выборка из базы

$dbh = new \PDO('mysql:host=localhost;dbname=my_db;', 'root', 'root');
$dbh->exec('SET NAMES UTF8');
$stm = $dbh->prepare('SELECT * FROM `users`');
$stm->execute();

// Получение результата

$allUsers = $stm->fetchAll();

// var_dump($allUsers);

echo '<br><br>';
?>

<table border="1">
    <tr><td>id</td><td>Имя</td><td>Email</td></tr>
    <?php foreach ($allUsers as $user): ?>
        <tr>
            <td><?= $user['id'] ?></td>
            <td><?= $user['name'] ?></td>
            <td><?= $user['email'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<?php

echo '<br><br>';

$stm = $dbh->prepare('SELECT * FROM `users` WHERE name=:name');
$stm->bindValue('name', 'Иван');
$stm->execute();
$allUsers = $stm->fetchAll();

?>

<table border="1">
    <tr><td>id</td><td>Имя</td><td>Email</td></tr>
    <?php foreach ($allUsers as $user): ?>
        <tr>
            <td><?= $user['id'] ?></td>
            <td><?= $user['name'] ?></td>
            <td><?= $user['email'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<br><br>

