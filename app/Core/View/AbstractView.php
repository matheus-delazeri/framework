<?php

namespace App\Core\View;

use App\Core\Controller\AbstractController;

abstract class AbstractView {

    /**
     * Render the current view
     *
     * @param AbstractController $controller Controller from where the view was rendered
     * @return void
     */
    abstract protected function _render(AbstractController $controller): void;

    /**
     * Method executed before the rendering of the view
     *
     * @return void
     */
    protected function beforeRender(): void { }

    public function render(AbstractController $controller): void {
        $this->beforeRender();
        $this->_render($controller);
    }
}