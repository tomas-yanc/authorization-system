<?php

namespace PwdTask\Controllers;

class MainController extends AbstractController
{
    public function main()
    {
        $this->view->renderHtml('main/main.php', ['articles' => $articles]);
    }
}