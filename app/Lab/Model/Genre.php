<?php

namespace App\Lab\Model;

use App\Core\Model\AbstractModel;

class Genre extends AbstractModel {

    function getTable(): string {
        return 'genres';
    }

    public function getIdField(): string {
        return 'genre_id';
    }
}