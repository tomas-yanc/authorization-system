<?php

namespace MyProject\Models\Articles;

use MyProject\Models\ActiveRecordEntity;
use MyProject\Models\Users\User;

class Article extends ActiveRecordEntity
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $text;

    /** @var string */
    protected $authorId;

    /** @var string */
    protected $createdAt;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    protected static function getTableName(): string
    {
        return 'articles';
    }
    
    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return User::getById($this->authorId);
    }

    public function setName($name) // Все set служат для изменения значений свойств
    {
        $this->name = $name;
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function setauthorId($authorId)
    {
        $this->authorId = $authorId;
    }

    public function setcreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function setAuthor(User $author): void
    {
        $this->authorId = $author->getId();
    }

}