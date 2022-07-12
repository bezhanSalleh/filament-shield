<?php

namespace BezhanSalleh\FilamentShield\Database\Factories;

use Illuminate\Support\Str;
use BezhanSalleh\FilamentShield\Models\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
* @extends \Illuminate\Database\Eloquent\Factories\Factory<\FilamentShield\Models\Setting>
    */
class SettingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model|TModel>
     */
    protected $model = Setting::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'key' => $key = Str::random(4),
            'value' => Str::random(4),
            'default' => $key,
        ];
    }
}
