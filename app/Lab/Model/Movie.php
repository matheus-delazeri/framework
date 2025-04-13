<?php

namespace App\Lab\Model;

use App\Core\Model\AbstractModel;

class Movie extends AbstractModel
{
    function getTable(): string {
        return 'movies';
    }

    public function getIdField(): string {
        return 'movie_id';
    }
}