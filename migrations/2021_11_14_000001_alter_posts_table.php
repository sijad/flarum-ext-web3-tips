<?php

use Flarum\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

return Migration::addColumns(
    'posts',
    [
        'tips' => [
            'integer',
            'default' => 0,
            'unsigned' => true,
        ]
    ],
);
