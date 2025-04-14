<?php

namespace App\Core\Controller;

abstract class AbstractController {

    /** @var string|null name of the current action of this controller */
    public string|null $currentAction = null;

    /** @var string|null name of the previous action of this controller */
    public string|null $previousAction = null;

    /** @var array params for the current action */
    public array $params = [];

    /** @var AbstractController|null controller from where the request came from */
    public AbstractController|null $referer = null;

    /**
     * Hook method called before redirecting to another action
     */
    public function beforeRedirect(): void {}

    /**
     * Redirects to a specified action
     *
     * @param string $action Action method to call
     * @param array $params Parameters to pass to the action
     * @param AbstractController|null $controller Target controller (defaults to $this)
     * @return AbstractController The controller that handled the action
     */
    public function redirect(string $action, array $params = [], AbstractController $controller = null): AbstractController {
        if (is_null($controller)) {
            $controller = clone $this;
            $controller->previousAction = $controller->currentAction;
            $controller->referer = $this;
        } else {
            $controller->previousAction = $controller->currentAction;
            $controller->referer = $this;
        }

        $controller->currentAction = $action;
        $controller->params = $params;

        $controller->beforeRedirect();

        $controller->$action();

        return $controller;
    }

    /**
     * Redirects to the referer controller's action
     * Prevents infinite recursion by tracking the controller chain
     *
     * @return void
     */
    public function redirectReferer(): void {
        if (!$this->referer instanceof AbstractController) {
            if ($this->previousAction !== null) {
                $this->redirect($this->previousAction, $this->params);
            }
            return;
        }

        $referer = clone $this->referer;
        $refererAction = $referer->currentAction ?? 'index';
        $refererParams = $referer->params;

        $originalRefererChain = $referer->referer;

        if ($originalRefererChain === $this) {
            $referer->referer = null;
        }

        $referer->redirect($refererAction, $refererParams);
    }
}