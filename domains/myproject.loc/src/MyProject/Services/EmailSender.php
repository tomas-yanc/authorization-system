<?php

namespace MyProject\Services;

use MyProject\Models\Users\User;

class EmailSender // Получаем из контроллера все данные для отправки
{
    public static function send(
        User $receiver, // Объект полностью (при отправке мы берем из него, что нам нужно)
        string $subject, // Заголовок или сообщение
        string $templateName, // Имя шаблона
        array $templateVars = [] // Здесь передаются id пользователя и код для него
    ): void

// Сверху это все аргумент. А снизу уже тело

    {
        extract($templateVars); // Делаем из массива строку

        ob_start(); // Буфер
        require __DIR__ . '/../../../templates/mail/' . $templateName;
        $body = ob_get_contents();
        ob_end_clean();

        mail($receiver->getEmail(), $subject, $body, 'Content-Type: text/html; charset=UTF-8'); // Функция отправки писем
    }
}                                                                       