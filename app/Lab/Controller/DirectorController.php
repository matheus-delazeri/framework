<?php

namespace App\Lab\Controller;
use App\Core\Controller\Terminal\ModelController;
use App\Core\Model\AbstractModel;
use App\Lab\Model\Director;
use App\Lab\Model\Genre;
use App\Lab\Model\Movie;

class DirectorController extends ModelController {

    public function getModel(): AbstractModel {
        return new Director();
    }
}