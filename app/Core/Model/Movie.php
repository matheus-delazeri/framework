<?php

namespace App\Core\Model;

class Movie extends AbstractModel
{
    function getTable(): string {
        return "movie";
    }

    public function getIdField(): string {
        return 'movie_id';
    }
}