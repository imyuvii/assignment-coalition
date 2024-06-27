<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyProjectRequest;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use Gate;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ProjectController extends Controller
{
    public function index(): View
    {
        abort_if(Gate::denies('project_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $projects = Project::all();

        return view('admin.projects.index', compact('projects'));
    }

    public function create(): View
    {
        abort_if(Gate::denies('project_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.projects.create');
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $project = Project::create($request->all());

        return redirect()->route('admin.projects.index');
    }

    public function edit(Project $project): View
    {
        abort_if(Gate::denies('project_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.projects.edit', compact('project'));
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $project->update($request->all());

        return redirect()->route('admin.projects.index');
    }

    public function show(Project $project): View
    {
        abort_if(Gate::denies('project_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.projects.show', compact('project'));
    }

    public function destroy(Project $project): RedirectResponse
    {
        abort_if(Gate::denies('project_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $project->delete();

        return back();
    }

    public function massDestroy(MassDestroyProjectRequest $request): Response
    {
        $projects = Project::whereIn('id', request('ids'))->get();

        foreach ($projects as $project) {
            $project->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
