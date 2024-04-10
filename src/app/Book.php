<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    const MIN_TITLE_LENGTH = 1;
    const MAX_TITLE_LENGTH = 255;
    const MIN_AUTHOR_LENGTH = 1;
    const MAX_AUTHOR_LENGTH = 255;

    protected $fillable = [
        'title', 'author',
    ];
}