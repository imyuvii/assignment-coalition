<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use Gate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    public function index(): View
    {
        abort_if(Gate::denies('task_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $tasks = Task::with(['project'])->orderBy('priority', 'ASC')->get();

        return view('admin.tasks.index', compact('tasks'));
    }

    public function create(): View
    {
        abort_if(Gate::denies('task_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $projects = Project::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.tasks.create', compact('projects'));
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $task = $request->all();
        $task['priority'] = Task::max('priority') + 1;
        Task::create($task);

        return redirect()->route('admin.tasks.index');
    }

    public function edit(Task $task): View
    {
        abort_if(Gate::denies('task_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $projects = Project::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $task->load('project');

        return view('admin.tasks.edit', compact('projects', 'task'));
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $task->update($request->all());

        return redirect()->route('admin.tasks.index');
    }

    public function show(Task $task): View
    {
        abort_if(Gate::denies('task_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $task->load('project');

        return view('admin.tasks.show', compact('task'));
    }

    public function destroy(Task $task): RedirectResponse
    {
        abort_if(Gate::denies('task_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $task->delete();

        return back();
    }

    public function massDestroy(MassDestroyTaskRequest $request): Response
    {
        $tasks = Task::whereIn('id', request('ids'))->get();

        foreach ($tasks as $task) {
            $task->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function updatePriority(Request $request): Response
    {
        $ids = $request->input('ids');
        foreach ($ids as $index => $id) {
            $task = Task::where('id', $id)->first();
            if($task) {
                $task->priority = $index + 1; // Assuming lower index means higher priority
                $task->save();
            }
        }

        return response()->json(['status' => 'success']);
    }
}
