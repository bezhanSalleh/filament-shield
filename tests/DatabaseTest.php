<?php

use BezhanSalleh\FilamentShield\Models\Setting;

use Illuminate\Support\Facades\Schema;

use function PHPUnit\Framework\assertNotEmpty;

it('can check if filament shield settings table exists', function () {
    Schema::connection('testing')->create('filament_shield_settings', function ($table) {
        $table->id();
        $table->string('key');
        $table->string('value');
        $table->string('default');
    });

    expect(Schema::hasTable('filament_shield_settings'))->toBeTrue();
});

it('can validate column names', function () {
    Schema::connection('testing')->create('filament_shield_settings', function ($table) {
        $table->id();
        $table->string('key');
        $table->string('value');
        $table->string('default');
    });
    $columnsStatus = Schema::hasColumns('filament_shield_settings', [
        'id',
        'key',
        'value',
        'default',
    ]);

    expect($columnsStatus)->toBeTrue();
});

it('can create config', function () {
    Schema::connection('testing')->create('filament_shield_settings', function ($table) {
        $table->id();
        $table->string('key');
        $table->string('value');
        $table->string('default');
    });

    Setting::factory()->create();

    config(['filament-shield' => Setting::pluck('value', 'default')]);

    expect(config()->has('filament-shield'))->toBeTrue();

    assertNotEmpty(config('filament-shield.shield'));
});
