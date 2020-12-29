<?php

namespace MyProject\Models\Users;

use MyProject\Services\Db;
use MyProject\Exceptions\InvalidArgumentException;

class UserActivationService
{
    private const TABLE_NAME = 'users_activation_codes'; // Работаем с нужной нам таблицей

    // Создаем строку с добавлением кода в таблице users_activation_codes

    public static function createActivationCode(User $user): string // Получает аргументы из контроллера. Объект, данные для которого получили из Post. И который уже был добавлен в базу
    {
        // Генерируем случайную последовательность символов

        $code = bin2hex(random_bytes(16));

        // Создаем код активации для уже существующего в базе пользователя

        $db = Db::getInstance();
        $db->query(
            'INSERT INTO ' . self::TABLE_NAME . ' (user_id, code) VALUES (:user_id, :code)',
            [
                'user_id' => $user->getId(),
                'code' => $code
            ]
        );

        return $code;
    }

    // Проверяет есть ли в базе такой id и такой код. Получает аргументы из контроллера как и EmailSender

    public static function checkActivationCode(user $user, string $code): bool
    {
        $db = Db::getInstance();
        $result = $db->query(
            'SELECT * FROM ' . self::TABLE_NAME . ' WHERE user_id = :user_id AND code = :code',
            [
                'user_id' => $user->getId(),
                'code' => $code
            ]
        );
        return !empty($result); // Возвращает не пустой результат, если в базе есть и пользователь и код для него

            // Я добавил для домашки по добавлению несуществующего пользователя

    if (empty($result)) {
        throw new InvalidArgumentException('Пользователь не зарегистрирован!');
    }

    }

}