<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(2, true);
        $initials = collect(explode(' ', $name))
            ->map(fn($word) => strtoupper($word[0]))
            ->join('');

        $number = str_pad(fake()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT);

        return [
            'project_name' => $name,
            'project_code' => $initials . $number,
            'description' => fake()->sentence(10),
            'owner_id' => 2,
            'updated_by' => 2,
        ];
    }
}
