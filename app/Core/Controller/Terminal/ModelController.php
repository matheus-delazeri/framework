<?php

namespace App\Core\Controller\Terminal;

use App\Core\Controller\TerminalController;
use App\Core\Model\AbstractModel;

use App\Core\View\Terminal\Model\IndexView;

abstract class ModelController extends TerminalController  {

    /**
     * Returns an instance of the model mapped for this
     * controller
     *
     * @return AbstractModel
     */
    abstract public function getModel(): AbstractModel;

    public function index(): void {
        $indexView = new IndexView($this->getModel());
        $indexView->render($this);
    }

}