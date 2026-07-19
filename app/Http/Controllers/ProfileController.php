<?php

namespace App\Http\Controllers;

use App\Helpers\AuditHelper;
use App\Http\Requests\ProfileSelfUpdateRequest;
use App\Models\Employee;
use App\Services\EmployeeSelfProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        protected EmployeeSelfProfileService $profileService
    ) {}

    public function edit(Request $request): View
    {
        $user = $request->user();
        abort_unless($user, 403);

        $employee = $this->resolveOwnEmployee($user->id);

        $profileData = $employee
            ? $this->profileService->build($employee)
            : null;

        return view('profile.edit', [
            'user' => $user,
            'employee' => $employee,
            'profileData' => $profileData,
            'canDeleteAccount' => $user->isAdminLike(),
        ]);
    }

    public function update(ProfileSelfUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user, 403);

        $validated = $request->validated();

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        $employee = $this->resolveOwnEmployee($user->id);

        if ($employee !== null) {
            $employee->name = $validated['name'];
            $employee->phone = $validated['phone'] ?? $employee->phone;
            $employee->save();
        }

        if (! $user->isSuperAdmin()) {
            AuditHelper::log(
                'profile_updated',
                'User',
                (int) $user->id,
                'تم تحديث الملف الشخصي للمستخدم',
                (int) $user->id
            );
        }

        return Redirect::route('profile.show')->with('success', __('profile.updated_success'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && $user->isAdminLike(), 403, __('profile.delete_forbidden'));

        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    protected function resolveOwnEmployee(int $userId): ?Employee
    {
        return Employee::query()
            ->with('department')
            ->where('user_id', $userId)
            ->first();
    }
}
