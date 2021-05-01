<?php

return [
    '~^articles/(\d+)/edit$~' => [\MyProject\Controllers\ArticlesController::class, 'edit'], // Показывает объект статьи, если строка с таким id есть в базе
    '~^articles/(\d+)/editin$~' => [\MyProject\Controllers\ArticlesController::class, 'editin'], // Добавляет новую статью в базу. Я создал этот метод
    '~^$~' => [\MyProject\Controllers\MainController::class, 'main'], // Покажет шаблон main.php и выведет все статьи
    '~^articles/(\d+)$~' => [\MyProject\Controllers\ArticlesController::class, 'view'], // Показывает статью по ее id
    '~^articles/add$~' => [\MyProject\Controllers\ArticlesController::class, 'add'], // Добавляет новую статью в базу и показывает ее объект
    '~^articles/(\d+)/delete$~' => [\MyProject\Controllers\ArticlesController::class, 'delete_article'], // Удаляет статью по ее id
    '~^users/register$~' => [\MyProject\Controllers\UsersController::class, 'signUp'], // Для регистрации нового пользователя
    '~^users/(\d+)/activate/(.+)$~' => [\MyProject\Controllers\UsersController::class, 'activate'], // Для активации пользователя после перехода из письма
    '~^users/login$~' => [\MyProject\Controllers\UsersController::class, 'login'], // Рендерит шаблон с формой авторизации
];

