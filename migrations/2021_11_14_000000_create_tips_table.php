<?php

use Flarum\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

return Migration::createTable(
    'tips',
    function (Blueprint $table) {
        $table->increments('id');
        $table->integer('post_id')->unsigned()->references('id')->on('posts')->onDelete('cascade');
        $table->integer('user_id')->unsigned()->nullable()->references('id')->on('users')->onDelete('set null');
        $table->char('transaction_hash', 32)->charset('binary')->unique();
        $table->char('from', 20)->charset('binary');
        $table->char('to', 20)->charset('binary');
        $table->decimal('value', 65, 30)->unsigned();
        $table->decimal('block_id', 65);
        $table->boolean('is_confirmed');
        $table->dateTime('created_at');
    }
);
