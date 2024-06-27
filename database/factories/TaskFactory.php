<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    private static int $priority = 1;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(5),
            'priority' => self::$priority++, // Increment priority for each new task
            'project_id' => Project::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
