<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use Gate;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ChangePasswordController extends Controller
{
    public function edit(): View
    {
        abort_if(Gate::denies('profile_password_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('auth.passwords.edit');
    }

    public function update(UpdatePasswordRequest $request): RedirectResponse
    {
        $user = auth()->user();
        if($user) {
            $user->update($request->validated());
        }
        return redirect()->route('profile.password.edit')->with('message', __('global.change_password_success'));
    }

    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        $user = auth()->user();
        if($user) {
            $user->update($request->validated());
        }

        return redirect()->route('profile.password.edit')->with('message', __('global.update_profile_success'));
    }

    public function destroy(): RedirectResponse
    {
        $user = auth()->user();
        if ($user) {
            $user->update([
                'email' => time() . '_' . $user->email,
            ]);
            $user->delete();
        }

        return redirect()->route('login')->with('message', __('global.delete_account_success'));
    }
}
