<?php

namespace MyProject\Services;

use MyProject\Models\Users\User;
use MyProject\Exceptions\InvalidArgumentException;

class EmailSender // Получаем из контроллера все данные для отправки
{
    public static function send(
        User $receiver, // Объект полностью (при отправке мы берем из него email пользователя)
        string $subject, // Заголовок или сообщение
        string $templateName, // Имя шаблона
        array $templateVars = [] // Здесь передаются id пользователя и код активации для него
    ): void

// Сверху это все аргумент. А снизу уже тело

    {
        extract($templateVars); // Делаем из массива строку

        if (!$code) {
            throw new InvalidArgumentException('Код активации не найден!');
        }

        ob_start(); // Буфер
        require __DIR__ . '/../../../templates/mail/' . $templateName;
        $body = ob_get_contents();
        ob_end_clean();

        mail($receiver->getEmail(), $subject, $body, 'Content-Type: text/html; charset=UTF-8'); // Функция отправки писем

    }
}                                                                       