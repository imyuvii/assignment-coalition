<?php

namespace App\Http\Requests;

use App\Models\User;
use Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('user_edit');
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        /** @var User $user */
        $user = $this->route('user');
        return [
            'name' => [
                'string',
                'required',
            ],
            'email' => [
                'required',
                'unique:users,email,' . ($user ? $user->id : 'NULL'),
            ],
            'roles.*' => [
                'integer',
            ],
            'roles' => [
                'required',
                'array',
            ],
        ];
    }
}
