<?php

namespace Tests\Unit;

use App\Http\Controllers\Admin\TaskController;
use App\Models\Task;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class TaskControllerUnitTest extends TestCase
{
    use DatabaseTransactions;

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_updates_task_priorities()
    {
        // Arrange: Create tasks and prepare the request
        $tasks = Task::factory()->count(3)->create();
        $ids = $tasks->pluck('id')->toArray();

        $request = Request::create('/admin/tasks/update-priority', 'POST', ['ids' => $ids]);

        // Act: Call the method
        $taskController = new TaskController();
        $response = $taskController->updatePriority($request);

        // Assert: Check if the priorities were updated correctly
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(json_encode(['status' => 'success']), $response->getContent());

        foreach ($tasks as $index => $task) {
            $task->refresh(); // Refresh the task instance to get the latest data
            $this->assertEquals($index + 1, $task->priority);
        }
    }
}
