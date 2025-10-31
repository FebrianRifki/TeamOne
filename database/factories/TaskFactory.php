<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Project;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'task_name' => fake()->words(2, true),
            'description' => fake()->sentence(10),
            'project_id' => Project::inRandomOrder()->first()->id,
            'updated_by' => User::where('id', '!=', 1)->inRandomOrder()->first()->id,
            'status' => fake()->randomElement(['todo', 'in_progress', 'done']),
            'priority' => fake()->numberBetween(1, 3),
            'due_date' => fake()->dateTimeBetween('-1 week', '+1 week'),
            'assigned_to' => User::where('id', '!=', 1)->inRandomOrder()->first()->id,
        ];
    }
}
