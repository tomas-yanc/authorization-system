<?php

namespace MyProject\Controllers;

use MyProject\Models\Users\User;
use MyProject\Models\Articles\Article;
use MyProject\View\View;

class ArticlesController extends AbstractController
{

// Новый метод для вставки по active record

    public function add(): void 
{
    $author = User::getById(1);

    $article = new Article();
    $article->setAuthor($author);
    $article->setName('Новое название статьи');
    $article->setText('Новый текст статьи');

    $article->save(); // save() запускает insert или update, а там есть подключение и запрос к базе

    var_dump($article);

}

    public function view(int $articleId): void
    {
        $article = Article::getById($articleId);

        if ($article === null) {
            $this->view->renderHtml('errors/404.php', [], 404);
            return;
        }

        $this->view->renderHtml('articles/view.php', [
            'article' => $article // Выводим в шаблоне то, что пришло по запросу к базе
        ]);
    }

    public function edit(int $articleId): void // Получаем результат запроса
    {
        $article = Article::getById($articleId); // getById это подключение и запрос к базе
        if ($article === null) 
        {
            $this->view->renderHtml('errors/404.php', [], 404);
            return;
        }

// Получили строку в запросе и меняем значения свойств

        $article->setName('Название статьи'); // Для этого еще добавляем сеттер в модель
        $article->setText('Текст статьи');
       
        $article->save(); // save() запускает рефлексию и делает из нашего объекта массив
        
        var_dump($article);

        // phpinfo();

    }

// Мой код создания третьей статьи- домашка в уроке по обновлению

    public function editin(int $articleId): void
    {
        $articlein = new Article();

        // $articlein->Name = 'Название 3 статьи';
        // $articlein->Text = 'Текст 3 статьи';
        // $articlein->authorId = 3;
        // $articlein->createdAt = '07.09.2020';

        $articlein->setName('Название 3 статьи');
        $articlein->setText('Текст 3 статьи');
        $articlein->setauthorId('1');
        $articlein->setcreatedAt('11.09.2020');

        //$articlein->setId('3');

        $articlein->save(); 

        var_dump($articlein);

        //$eventData->articlein-> для использования phpDoc
    }

    public function delete_article(int $articleId): void
    {  
        // Отправка письма здесь просто для тестирования

        mail('tomas.yanc@gmail.com', 'Тема test письма', 'Текст test письма', 'From: tomastest.ops@gmail.com');

        $darticle = Article::getById($articleId); // Получаем результат запроса (объект)

                if ($darticle === null)
                {
                    $this->view->renderHtml('errors/404.php', [], 404);
                    echo 'Такой статьи нет в базе';
                    return;
                }

                else {
                    echo 'Статья успешно удалена';
                    echo '<br><br>';
                }

        $darticle->delete();

        //var_dump($darticle);
    }

}
