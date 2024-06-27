<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\ProjectController;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Tests\TestCase;

class ProjectControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Gate::shouldReceive('denies')->andReturn(false);
    }

    /** @test */
    public function it_displays_the_projects_index_page()
    {
        $projects = Project::factory()->count(2)->create();

        $projectController = new ProjectController();
        $response = $projectController->index();

        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('admin.projects.index', $response->name());
        $this->assertArrayHasKey('projects', $response->getData());
    }

    /** @test */
    public function it_creates_a_new_project()
    {
        $projectData = Project::factory()->make()->toArray();

        $request = StoreProjectRequest::create('/admin/projects', 'POST', $projectData);

        $projectController = new ProjectController();
        $response = $projectController->store($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertDatabaseHas('projects', ['name' => $projectData['name']]);
    }

    /** @test */
    public function it_updates_a_project()
    {
        $project = Project::factory()->create();
        $updatedData = ['name' => 'Updated Project Name'];

        $request = UpdateProjectRequest::create('/admin/projects/' . $project->id, 'PUT', $updatedData);

        $projectController = new ProjectController();
        $response = $projectController->update($request, $project);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertDatabaseHas('projects', ['id' => $project->id, 'name' => 'Updated Project Name']);
    }

    /** @test */
    public function it_deletes_a_project()
    {
        $project = Project::factory()->create();

        $projectController = new ProjectController();
        $response = $projectController->destroy($project);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSoftDeleted('projects', ['id' => $project->id]);
    }
}
