<?php

namespace App\Lab\Controller;

use App\Core\Controller\TerminalController;
use App\Lab\View\Menu;

class IndexController extends TerminalController {

   public function index(): void {
       $view = new Menu();
       $view->render($this);
   }
}