<?php

namespace App\Lab\Model;

use App\Core\Model\AbstractModel;

class Director extends AbstractModel {

    function getTable(): string {
        return 'directors';
    }

    public function getIdField(): string {
        return 'director_id';
    }
}