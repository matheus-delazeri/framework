<?php

namespace App\Lab\Controller;
use App\Core\Controller\Terminal\ModelController;
use App\Core\Model\AbstractModel;
use App\Lab\Model\Director;

class DirectorController extends ModelController {

    public function getModel(): AbstractModel {
        return new Director();
    }
}