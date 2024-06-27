<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('task_edit');
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'project_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
