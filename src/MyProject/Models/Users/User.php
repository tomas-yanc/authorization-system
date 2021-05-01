<?php

namespace MyProject\Models\Users;

use MyProject\Models\ActiveRecordEntity;
use MyProject\Exceptions\InvalidArgumentException;
use MyProject\Services\UsersAuthService;
use MyProject\Controllers\AbstractController; // Я добавил, чтобы передать $user для получения и вывода имени поьзователя

class User extends ActiveRecordEntity
{
    /** @var string */
    protected $nickname;

    /** @var string */
    protected $email;

    /** @var int */
    protected $isConfirmed;

    /** @var string */
    protected $role;

    /** @var string */
    protected $passwordHash;

    /** @var string */
    protected $authToken;

    /** @var string */
    protected $createdAt;

    /**
     * @return string
     */

    public function getNickname(): string
    {
        return $this->nickname;
    }

    protected static function getTableName(): string
    {
        return 'users';
    }

    public function getEmail(): string // я написал гетер
    {
        return $this->email;
    }

    public function getAuthToken(): string // я написал гетер 
    {
    return $this->authToken;
    }

 /* 
 Статический метод, принимает на вход массив с данными, пришедшими от пользователя в POST запросе, 
 проверяет их и будет пытаться создать нового пользователя и сохранить его в базе
*/

 public static function signUp(array $userData): User
 {
     if (empty($userData['nickname'])) {
         throw new InvalidArgumentException('Не передан nickname');
     }
 
     if (!preg_match('/^[a-zA-Z0-9]+$/', $userData['nickname'])) {
         throw new InvalidArgumentException('Nickname может состоять только из символов латинского алфавита и цифр');
     }
 
     if (empty($userData['email'])) {
         throw new InvalidArgumentException('Не передан email');
     }
 
     if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
         throw new InvalidArgumentException('Email некорректен');
     }
 
     if (empty($userData['password'])) {
         throw new InvalidArgumentException('Не передан password');
     }
 
     if (mb_strlen($userData['password']) < 8) {
         throw new InvalidArgumentException('Пароль должен быть не менее 8 символов');
     }

     if (static::findOneByColumn('nickname', $userData['nickname']) !== null) {
        throw new InvalidArgumentException('Пользователь с таким nickname уже существует');
    }

    if (static::findOneByColumn('email', $userData['email']) !== null) {
        throw new InvalidArgumentException('Пользователь с таким email уже существует');
    }

    // Создаем объект для работы с базой

    $user = new User();
    $user->nickname = $userData['nickname'];
    $user->email = $userData['email'];
    $user->passwordHash = password_hash($userData['password'], PASSWORD_DEFAULT);
    $user->isConfirmed = false;
    $user->role = 'user';
    $user->authToken = sha1(random_bytes(100)) . sha1(random_bytes(100));
    $user->save(); // Добавили новую строку в базу или изменили существующую

    return $user; // Получаем объект из базы с его id
 }

/* 
Метод для активации пользователя после перехода из письма (для изменения значения в isConfirmed)
Он запускается не самостоятельно, а из контроллера
*/

 public function activate(): void
{
    $this->isConfirmed = true;
    $this->save();
}

public static function login(array $loginData): User
{
    if (empty($loginData['email'])) {
        throw new InvalidArgumentException('Не передан email');
    }

    if (empty($loginData['password'])) {
        throw new InvalidArgumentException('Не передан password');
    }

    $user = User::findOneByColumn('email', $loginData['email']);
    if ($user === null) {
        throw new InvalidArgumentException('Нет пользователя с таким email');
    }

    if (!password_verify($loginData['password'], $user->getPasswordHash())) {
        throw new InvalidArgumentException('Неправильный пароль');
    }

    if (!$user->isConfirmed) {
        throw new InvalidArgumentException('Пользователь не подтверждён');
    }

    $user->refreshAuthToken(); // Создался новый токен вместо старого
    $user->save(); // Записали в базу новый токен для объекта

    return $user;

    // AbstractController::getArgument($user); // Я добавил, чтобы передать $user для получения и вывода имени поьзователя

}

public function getPasswordHash(): string
{
    return $this->passwordHash;
}

private function refreshAuthToken()
{
    $this->authToken = sha1(random_bytes(100)) . sha1(random_bytes(100));
}

}