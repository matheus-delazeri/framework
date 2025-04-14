<?php

namespace App\Core\Controller\Terminal;

use App\Core\Controller\TerminalController;
use App\Core\Messager;
use App\Core\Model\AbstractModel;

use App\Core\View\Terminal\Model\Details;
use App\Core\View\Terminal\Model\EditForm;
use App\Core\View\Terminal\Model\Index;

abstract class ModelController extends TerminalController  {

    /**
     * Returns an instance of the model mapped for this
     * controller
     *
     * @return AbstractModel
     */
    abstract public function getModel(): AbstractModel;

    public function beforeRedirect(): void {
        $this->params['model'] = $this->getModel();
    }

    public function index(): void {
        $view = new Index();
        $view->render($this);
    }

    public function view(): void {
        $registerId = $this->params['id'] ?? null;
        if (empty($registerId)) {
            Messager::add('[!] No register selected for view!');
            $this->redirectReferer();
        }

        $register = $this->getModel()->load($registerId);
        if (!$register->isLoaded()) {
            Messager::add("[!] Register of '{$this->getModel()->getName()}' not found for '{$register->getIdField()} = $registerId'");
            $this->redirectReferer();
        }

        $view = new EditForm($register);
        $view->render($this);
    }

    public function details(): void {
        $view = new Details();
        $view->render($this);
    }

    public function update(): void {
        $registerId = $this->params['id'] ?? null;
        if (empty($registerId)) {
            Messager::add('[!] No register selected to update!');
            $this->redirect('view', ['id' => $registerId]);
        }

        $field = $this->params['field'] ?? null;
        if (empty($field)) {
            Messager::add('[!] No field selected to update!');
            $this->redirect('view', ['id' => $registerId]);
        }

        if (!in_array($field, array_keys($this->getModel()->getFields())) || $field == $this->getModel()->getIdField()) {
            Messager::add("[!] Field '$field' doesn't exists or is not writeable!");
            $this->redirect('view', ['id' => $registerId]);
        }

        $value = $this->params['value'] ?? null;
        if (empty($value)) {
            Messager::add('[!] No value selected to update!');
            $this->redirect('view', ['id' => $registerId]);
        }

        $register = $this->getModel()->load($registerId);
        if (!$register->isLoaded()) {
            Messager::add("[!] Register of '{$this->getModel()->getName()}' not found for '{$register->getIdField()} = $registerId'");
            $this->redirect('view', ['id' => $registerId]);
        }

        try {
            $register->addData($field, $value);
            $register->save();
            Messager::add("[OK] Successfully updated record!");
        } catch (\Exception $e) {
            Messager::add("[!] An error occurred while updating the register: {$e->getMessage()}");
        }

        $this->redirect('view', ['id' => $registerId]);
    }

     public function delete(): void {
        $registerId = $this->params['id'] ?? null;
        if (empty($registerId)) {
            Messager::add('[!] No register selected to remove!');
            $this->redirect('view', ['id' => $registerId]);
        }

        $register = $this->getModel()->load($registerId);
        if (!$register->isLoaded()) {
            Messager::add("[!] Register of '{$this->getModel()->getName()}' not found for '{$register->getIdField()} = $registerId'");
            $this->redirect('view', ['id' => $registerId]);
        }

        try {
            $register->delete();
            Messager::add("[OK] Successfully deleted record!");
            $this->redirect('index');
        } catch (\Exception $e) {
            Messager::add("[!] An error occurred while deleting the register: {$e->getMessage()}");
            $this->redirect('view', ['id' => $registerId]);
        }

    }

}