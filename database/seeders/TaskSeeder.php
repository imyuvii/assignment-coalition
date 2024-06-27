<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;
use App\Models\Task;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $projects = Project::all();

        foreach ($projects as $project) {
            Task::factory()->count(5)->create([
                'project_id' => $project->id,
            ]);
        }
    }
}
