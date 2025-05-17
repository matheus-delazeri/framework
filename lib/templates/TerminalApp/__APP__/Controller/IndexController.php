<?php

namespace App\__APP__\Controller;

class IndexController extends \App\Core\Controller\TerminalController {

   public function index(): void {
       $view = new \App\__APP__\View\Menu();
       $view->render($this);
   }
}