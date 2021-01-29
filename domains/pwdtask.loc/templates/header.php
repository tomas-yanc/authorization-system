<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Клиенты</title>
    <link rel="stylesheet" href="/styles.css">
</head>
<body>

<header class="layout">
    <div class="header">
        <p>
            Список клиентов
        </p>
    </div>
    <div style="text-align: right">
        <p>
            <?= !empty($user) ? 'Привет, ' . $user->getNickname() : 'Войдите на сайт' ?>
        </p>
    </div>
    </header>
    <main>
    <div>
        <p>