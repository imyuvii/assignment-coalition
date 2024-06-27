<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        abort_if(Gate::denies('project_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:projects,id',
        ];
    }
}
