<?php

namespace App\Lab\Model;

use App\Core\Model\AbstractModel;

class Movie extends AbstractModel {

    function getTable(): string {
        return 'movies';
    }
}