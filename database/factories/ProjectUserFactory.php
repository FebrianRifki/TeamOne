<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProjectUser>
 */
class ProjectUserFactory extends Factory
{
    protected $model = ProjectUser::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::inRandomOrder()->first()->id,
            'user_id' => User::inRandomOrder()->first()->id,
            'updated_by' => 2,
        ];
    }
}
