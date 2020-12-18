<?php

namespace MyProject\Models;

use MyProject\Services\Db;

abstract class ActiveRecordEntity
{
    /** @var int */
    protected $id;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

// Методы для приведения названий свойств и столбцов к единому виду

    public function __set(string $name, $value)
    {
        $camelCaseName = $this->underscoreToCamelCase($name);
        $this->$camelCaseName = $value;
    }

    private function underscoreToCamelCase(string $source): string
    {
        return lcfirst(str_replace('_', '', ucwords($source, '_')));
    }

    /**
     * @return static[]
     */
    public static function findAll(): array
    {
        $db = Db::getInstance();
        return $db->query('SELECT * FROM `' . static::getTableName() . '`;', [], static::class);
    }

    /**
     * @param int $id
     * @return static|null
     */

// getById применяется для объекта, которму прописан. Для подключения к базе и выборке по id

    public static function getById(int $id): ?self
    {
        $db = Db::getInstance(); // getInstance() объект для подключения к базе
        $entities = $db->query(
            'SELECT * FROM `' . static::getTableName() . '` WHERE id=:id;', // Значение getTableName берется из той модели для какого объекта запускается getById
            [':id' => $id],
            static::class
        );
        return $entities ? $entities[0] : null;
    }

// Код написанный мной

/*
Получаем значение в этот аргумент и оно становится значением id в этом методе
А значение приходит из запроса пользователя в арагумент метода в контроллере
*/

// Эти методы просто делают выборку из таблиц и получают данные для объекта модели

    public static function getByIdCode(int $id): ?self 
    {
        $db = Db::getInstance();
        $entities = $db->query(
            'SELECT * FROM `' . static::getTableName() . '` WHERE user_id=:user_id;', // Выборка по id (я переделал на выборку по user_id). Имя таблицы берется из модели
            [':user_id' => $id],
            static::class
        );
        return $entities ? $entities[0] : null;
    }

// Мой код закончился

    abstract protected static function getTableName(): string;


    public function save(): void 
    {
        $mappedProperties = $this->mapPropertiesToDbFormat();
        if ($this->id !== null) {
            $this->update($mappedProperties);
        } else {
            $this->insert($mappedProperties);
        }
    }
    
    private function insert(array $mappedProperties): void
    {
        $filteredProperties = array_filter($mappedProperties);
    
        $columns = [];
        $paramsNames = [];
        $params2values = [];
        foreach ($filteredProperties as $columnName => $value) {
            $columns[] = '`' . $columnName. '`';
            $paramName = ':' . $columnName;
            $paramsNames[] = $paramName;
            $params2values[$paramName] = $value;
        }
    
        //var_dump($columns);
        //var_dump($paramsNames);
        //var_dump($params2values);

        $columnsViaSemicolon = implode(', ', $columns);
        $paramsNamesViaSemicolon = implode(', ', $paramsNames);
    
        $sql = 'INSERT INTO ' . static::getTableName() . ' (' . $columnsViaSemicolon . ') VALUES (' . $paramsNamesViaSemicolon . ');';
    
        //var_dump($sql);

        $db = Db::getInstance();
        $db->query($sql, $params2values, static::class);
        $this->id = $db->getLastInsertId();
        $this->refresh();
    }

    private function refresh(): void
    {
        $objectFromDb = static::getById($this->id);
        $reflector = new \ReflectionObject($objectFromDb);
        $properties = $reflector->getProperties();

        foreach ($properties as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            $this->$propertyName = $property->getValue($objectFromDb);
        }
    }

// Ниже делим массив на два, подключаемся и делаем запрос к базе

    private function update(array $mappedProperties): void
    {
        $columns2params = [];
        $params2values = [];
        $index = 1;
        foreach ($mappedProperties as $column => $value) {
            $param = ':param' . $index; // :param1
            $columns2params[] = $column . ' = ' . $param; // column1 = :param1
            $params2values[$param] = $value; // [:param1 => value1]
            $index++;
        }

       // var_dump($columns2params);
       // var_dump($params2values);

       $sql = 'UPDATE ' . static::getTableName() . ' SET ' . implode(', ', $columns2params) . ' WHERE id = ' . $this->id;

       //var_dump($sql);
      //var_dump($params2values);

      $db = Db::getInstance();
      $db->query($sql, $params2values, static::class);
    }

//Рефлексия- считываем свойства обЪекта и получаем массив

private function mapPropertiesToDbFormat(): array
{
    $reflector = new \ReflectionObject($this);
    $properties = $reflector->getProperties();

    $mappedProperties = [];
    foreach ($properties as $property) {
        $propertyName = $property->getName();
        $propertyNameAsUnderscore = $this->camelCaseToUnderscore($propertyName);
        $mappedProperties[$propertyNameAsUnderscore] = $this->$propertyName;
    }

    return $mappedProperties;
}

private function camelCaseToUnderscore(string $source): string
{
    return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $source));
}

public function delete(): void
{
    $db = Db::getInstance();
    $db->query(
        'DELETE FROM `' . static::getTableName() . '` WHERE id = :id',
        [':id' => $this->id]
    );

    // $this->id = null; ?
}

// Мой код для удаления кодов из базы

public function delete_code(): void // Все очень похоже на функционал метода getByIdCode, только объект не получаем, а удаляем
{
    $db = Db::getInstance();
    $db->query(
        'DELETE FROM `' . static::getTableName() . '` WHERE user_id = :user_id',
        [':user_id' => $this->userId] // Я поменял выборку, сделал по user_id
    );
}

// Мой код закончился

// Проверяет дубликаты в базе при регистрации пользователей

public static function findOneByColumn(string $columnName, $value): ?self
{
    $db = Db::getInstance();
    $result = $db->query(
        'SELECT * FROM `' . static::getTableName() . '` WHERE `' . $columnName . '` = :value LIMIT 1;',
        [':value' => $value],
        static::class
    );
    if ($result === []) {
        return null;
    }
    return $result[0];
}
    
}