<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Tests\database\factories;

use BezhanSalleh\FilamentShield\Tests\Fixtures\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Team>
 */
class TeamFactory extends Factory
{
    protected $model = Team::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
        ];
    }
}
