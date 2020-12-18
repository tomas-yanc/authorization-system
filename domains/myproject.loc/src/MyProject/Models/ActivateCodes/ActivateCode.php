<?php

namespace MyProject\Models\ActivateCodes;

use MyProject\Exceptions\InvalidArgumentException;
use MyProject\Models\ActiveRecordEntity;
use MyProject\Models\Users\User;

class ActivateCode extends ActiveRecordEntity
{
    /** @var string */
    protected $userId;

    /** @var string */
    protected $code;


    /**
     * @return string
     */
    public function getUserId(): string 
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    protected static function getTableName(): string
    {
        return 'users_activation_codes'; // Задаем имя таблицы, которую будем использовать
    }
}