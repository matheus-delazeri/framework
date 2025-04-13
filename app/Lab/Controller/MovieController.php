<?php

namespace App\Lab\Controller;
use App\Core\Controller\Terminal\ModelController;
use App\Core\Model\AbstractModel;
use App\Lab\Model\Movie;

class MovieController extends ModelController {

    public function getModel(): AbstractModel {
        return new Movie();
    }
}