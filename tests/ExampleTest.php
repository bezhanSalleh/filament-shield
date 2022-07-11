<?php

use Illuminate\Support\Facades\Schema;

it('can test', function () {
    expect(true)->toBeTrue();
});

it('can check if table exists', function () {
    Schema::connection('testing')->create('users', function ($table) {
        $table->bigIncrements('id');
    });

    expect(Schema::hasTable('users'))->toBeTrue();
});
