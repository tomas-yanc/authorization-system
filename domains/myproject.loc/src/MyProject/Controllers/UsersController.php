<?php

namespace MyProject\Controllers;

use MyProject\View\View;
use MyProject\Models\Users\User;
use MyProject\Models\Users\UserActivationService;
use MyProject\Models\Articles\Article;
use MyProject\Models\ActivateCodes\ActivateCode;
use MyProject\Exceptions\InvalidArgumentException;
use MyProject\Services\EmailSender;
use MyProject\Models\ActiveRecordEntity;
use MyProject\Controllers\ActivateCodeController;

class UsersController
{
    /** @var View */
    private $view;
    public $fake; // Я сделал для домашки

    public function __construct()
    {
        $this->view = new View(__DIR__ . '/../../../templates');
        $this->fake = 99; // Я сделал для домашки
    }

    public function signUp() // Метод для отлова исключений и отправки данных в EmailSender
    {
        if (!empty($_POST)) {
            try {
                $user = User::signUp($_POST); // Передает данные в модель
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('users/signUp.php', ['error' => $e->getMessage()]);
                return;
            }
    
            if ($user instanceof User) {
                $code = UserActivationService::createActivationCode($user);

    // Отправляем данные в класс для отправки писем EmailSender

                EmailSender::send($user, 'Активация', 'userActivation.php', [
                    'userId' => $user->getId(), // Могу добавить вместо "$user->getId()" несуществующего пользователя в ссылку письма $this->fake Я сделал для домашки
                    'code' => $code // Данные для шаблона письма
                ]);

    // Показываем шаблон об успешной регистрации

                $this->view->renderHtml('users/signUpSuccessful.php');
                return;
            }
        }

    // Показываем шаблон формы для регистрации

        $this->view->renderHtml('users/signUp.php');
    }

/*
Метод для активации пользователя после перехода из письма
Проверяет наличие пользователя и кода в базе.
Если данные есть, то запускает метод в модели, 
а там значение в is_confiremd меняется на true.
И выводим "OK"
*/

// Аргументы приходят из адреса после проверки роута в файле index с помощью foreach

public function activate(int $userId, string $activationCode)
    {
    /*Объект*/ $user= /*Класс*/ User::getById($userId); // Запускает метод getById для класса User и получает объект (данные для объекта из базы), userId получен из адреса из письма

// Отключал проверк когда добавлял несуществующий id в ссылку для активации

$isCodeValid = UserActivationService::checkActivationCode($user, $activationCode);

$activateCode = ActivateCode::getByIdCode($userId); // Мой кодик, просто получаем объект для дальнейшего использования

// Сделали выборку по user_id в нужной нам таблице и получили объект

// Я написал для домашки по удалению кода

    if ($isCodeValid) {
        $user->activate(); // Для объекта $user мы применяем метод activate(), который находится в модели
        echo '</br></br>';
        echo 'OK!';
        echo '</br></br>';
        echo 'Логика для вывода этого текста в контроллере';
        echo '</br></br>';
        var_dump($activateCode);
        $activateCode->delete_code(); // Я написал для домашки по удалению кода
        echo '</br></br>'; // Я написал для домашки по удалению кода
        echo 'Код успешно удален'; // Я написал для домашки по удалению кода
        }
    }

}

