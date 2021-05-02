<?php

namespace MyProject\Controllers;

use MyProject\Exceptions\InvalidArgumentException;
use MyProject\Models\Users\User;
use MyProject\Models\Users\UserActivationService;
use MyProject\Services\EmailSender;
use MyProject\Services\UsersAuthService;

class UsersController extends AbstractController
{
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

                try {
                EmailSender::send($user, 'Активация', 'userActivation.php',
                    ['userId' => $user->getId(), // Могу добавить вместо $user->getId() несуществующего пользователя в данные для отправки $this->fake
                    'code' => $code ]); // Данные для шаблона письма
                } catch (InvalidArgumentException $e) {
                    $this->view->renderHtml('users/noUser.php', ['noUser' => $e->getMessage()]);
                    return;
                }   

    // Показываем шаблон об успешной регистрации

                $this->view->renderHtml('users/signUpSuccessful.php');
                return;
            }
        }

    // Показываем шаблон формы для регистрации

        $this->view->renderHtml('users/signUp.php');
    }

/*
Метод для активации пользователя после перехода из письма. Проверяет наличие пользователя и кода в базе.
Если данные есть, то запускает метод в модели, а там значение в is_confiremd меняется на true. И выводим "OK. Активация прошла успешно!"
*/

// Аргументы приходят из адреса после проверки роута в файле index с помощью foreach

// Здесь мы снова получаем данные из POST запроса от пользователя, когда он переходит по ссылке из письма 

public function activate(int $userId, string $activationCode) {
    
    $user=User::getById($userId); // Запускает метод getById для класса User и получает объект (данные для объекта из базы), userId получен из адреса из письма

// Бросаю исключение на случай когда добавляю не существующего пользователя в данные для отправки письма (id которого нет в базе)

if (!$user) {
    throw new InvalidArgumentException('Пользователь не зарегистрирован!');
}

// Отключал проверку когда добавлял несуществующий id в ссылку для активации
    
    $isCodeValid = UserActivationService::checkActivationCode($user, $activationCode);

// Сделали выборку по user_id в нужной нам таблице и получили объект. Я написал для домашки по удалению кода

    if ($isCodeValid) {
        $user->activate(); // Для объекта $user мы применяем метод activate(), который находится в модели
        echo 'OK. Активация прошла успешно!';

        //var_dump($RemoveActivationCode);
        // $RemoveActivationCode->delete_code(); // Я написал для домашки по удалению кода

        echo '</br></br>';
        
        // echo 'Код успешно удален'; // Я написал для домашки по удалению кода
        }
    }

    public function login()
    {
        if (!empty($_POST)) {
            try {
                $user = User::login($_POST);
                UsersAuthService::createToken($user);
                header('Location: /');
                exit();
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('users/login.php', ['error' => $e->getMessage()]);
                return;
            }
        }
    
        $this->view->renderHtml('users/login.php');

    }

}

