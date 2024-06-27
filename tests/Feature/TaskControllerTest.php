<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\TaskController;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Gate::shouldReceive('denies')->andReturn(false);
    }

    /** @test */
    public function it_displays_the_tasks_index_page()
    {
        $project = Project::factory()->create();
        $tasks = Task::factory()->count(2)->create(['project_id' => $project->id]);

        $taskController = new TaskController();
        $response = $taskController->index();

        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('admin.tasks.index', $response->name());
        $this->assertArrayHasKey('tasks', $response->getData());
    }

    /** @test */
    public function it_creates_a_new_task()
    {
        $project = Project::factory()->create();
        $taskData = Task::factory()->make(['project_id' => $project->id])->toArray();

        $request = StoreTaskRequest::create('/admin/tasks', 'POST', $taskData);

        $taskController = new TaskController();
        $response = $taskController->store($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertDatabaseHas('tasks', ['name' => $taskData['name']]);
    }

    /** @test */
    public function it_updates_a_task()
    {
        $task = Task::factory()->create();
        $updatedData = ['name' => 'Updated Task Name'];

        $request = UpdateTaskRequest::create('/admin/tasks/' . $task->id, 'PUT', $updatedData);

        $taskController = new TaskController();
        $response = $taskController->update($request, $task);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'name' => 'Updated Task Name']);
    }

    /** @test */
    public function it_deletes_a_task()
    {
        $task = Task::factory()->create();

        $taskController = new TaskController();
        $response = $taskController->destroy($task);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSoftDeleted('tasks', ['id' => $task->id]);
    }
}
