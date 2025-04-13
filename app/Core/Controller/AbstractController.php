<?php

namespace App\Core\Controller;

abstract class AbstractController {

    /** @var string name of the current action of this controller */
    public string $currentAction;

    /** @var array params for the current action */
    public array $params = [];

    /** @var AbstractController controller from where the request came from */
    public AbstractController $previous;

    public function redirect(string $action, array $params = [], AbstractController $controller = null): AbstractController {
        if (is_null($controller)) { $controller = $this; }

        $controller->currentAction = $action;
        $controller->previous = $this;
        $controller->params = $params;

        $controller->$action();

        return $controller;
    }

}