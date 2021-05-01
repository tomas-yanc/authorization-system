<?php

use MyProject\Exceptions\InvalidArgumentException;

try {
    spl_autoload_register(function (string $className) {
        require_once __DIR__ . '/../src/' . $className . '.php';
});

$route = $_GET['route'] ?? '';
$routes = require __DIR__ . '/../src/routes.php';

$isRouteFound = false;
foreach ($routes as $pattern => $controllerAndAction) {
    preg_match($pattern, $route, $matches); // В $matches попадает только ключ (из адреса)
    if (!empty($matches)) {
        $isRouteFound = true;
        break;
    }
}

/*
if (!$isRouteFound) {
    echo 'Страница не найдена!';
    return;
}
*/

if (!$isRouteFound) {
    throw new \MyProject\Exceptions\NotFoundException();
}

// var_dump($controllerAndAction);
// var_dump($matches);

unset ($matches[0]);

$controllerName = $controllerAndAction[0];
$actionName = $controllerAndAction[1];

$controller = new $controllerName();

// Обработка исключений когда в данные для отправки письма ппадает не существующий пользователь (id которого нет в базе)

try { 
    $controller->$actionName(...$matches); // В $matches попадает только ключ (из адреса) и передается в заданный метод указанного класса
    } catch (InvalidArgumentException $e) {
        $view = new \MyProject\View\View(__DIR__ . '/../templates/users');
        $view->renderHtml('noUser.php', ['noUser' => $e->getMessage()]);
    }

}

/*
catch (\MyProject\Exceptions\DbException $e) {
    echo $e->getMessage();
*/

// Логика для обработки исключений (ошибок), шаблоны для исключений 500.php и 404.php

catch (\MyProject\Exceptions\DbException $e) {
    $view = new \MyProject\View\View(__DIR__ . '/../templates/errors');
    $view->renderHtml('500.php', ['error' => $e->getMessage()], 500);
}
catch (\MyProject\Exceptions\NotFoundException $e) {
    $view = new \MyProject\View\View(__DIR__ . '/../templates/errors');
    $view->renderHtml('404.php', ['error' => $e->getMessage()], 404);
}

