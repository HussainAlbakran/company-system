<?php

namespace App\Http\Controllers;

use App\Helpers\AuditHelper;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserManagementController extends Controller
{
    protected function authorizeUsers(): void
    {
        if (! auth()->check() || ! auth()->user()->canManageUsers()) {
            abort(403, __('users.abort_unauthorized'));
        }
    }

    public function index()
    {
        $this->authorizeUsers();

        // Show more accounts to avoid "missing" results due to pagination.
        $users = User::latest()->paginate(60);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $this->authorizeUsers();

        return view('users.create');
    }

    public function store(Request $request)
    {
        $this->authorizeUsers();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(User::MANAGEABLE_ROLES)],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'approval_status' => 'approved',
            'is_active' => true,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return redirect()->route('users.index')->with('success', __('users.flash_created'));
    }

    public function show(User $user)
    {
        $this->authorizeUsers();

        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->authorizeUsers();

        $user->load('employee.department');

        $linkableEmployees = Employee::query()
            ->with('department:id,name')
            ->orderBy('name')
            ->where(function ($query) use ($user) {
                $query->whereNull('user_id')
                    ->orWhere('user_id', $user->id);
            })
            ->get(['id', 'name', 'employee_number', 'user_id', 'department_id']);

        return view('users.edit', [
            'user' => $user,
            'canEditRole' => auth()->user()->isAdminLike(),
            'linkableEmployees' => $linkableEmployees,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeUsers();
        $oldRole = (string) $user->role;

        $canEditRole = auth()->user()->isAdminLike();

        if (! $canEditRole && $request->has('role')) {
            $request->request->remove('role');
        }

        if ($request->input('employee_id') === '' || $request->input('employee_id') === '0') {
            $request->merge(['employee_id' => null]);
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'string', 'min:8'],
            'approval_status' => ['nullable', 'in:pending,approved,rejected,suspended'],
            'is_active' => ['nullable', 'boolean'],
        ];

        if ($canEditRole) {
            $rules['role'] = ['required', Rule::in(User::MANAGEABLE_ROLES)];
        }

        $rules['employee_id'] = ['nullable', 'integer', 'exists:employees,id'];

        $validated = $request->validate($rules);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if ($canEditRole && array_key_exists('role', $validated)) {
            $data['role'] = $validated['role'];
        }

        if (array_key_exists('approval_status', $validated)) {
            $data['approval_status'] = $validated['approval_status'];
        }

        if (array_key_exists('is_active', $validated)) {
            $data['is_active'] = $validated['is_active'];
        }

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $requestedEmployeeId = isset($validated['employee_id']) && $validated['employee_id'] !== ''
            ? (int) $validated['employee_id']
            : null;

        $previousEmployeeId = Employee::query()
            ->where('user_id', $user->id)
            ->value('id');

        DB::transaction(function () use ($user, $data, $requestedEmployeeId, $previousEmployeeId, $canEditRole, $oldRole) {
            if ($requestedEmployeeId) {
                $employee = Employee::query()
                    ->whereKey($requestedEmployeeId)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($employee->user_id !== null && (int) $employee->user_id !== (int) $user->id) {
                    throw ValidationException::withMessages([
                        'employee_id' => [__('users.error_employee_already_linked')],
                    ]);
                }
            }

            $user->update($data);

            Employee::query()
                ->where('user_id', $user->id)
                ->update(['user_id' => null]);

            if ($requestedEmployeeId) {
                Employee::query()
                    ->whereKey($requestedEmployeeId)
                    ->update(['user_id' => $user->id]);
            }

            if ($canEditRole && array_key_exists('role', $data) && $oldRole !== (string) $data['role']) {
                AuditHelper::log(
                    'role_changed',
                    'User',
                    $user->id,
                    sprintf(
                        'old_role=%s | new_role=%s | changed_by=%s',
                        $oldRole,
                        (string) $data['role'],
                        (string) auth()->id()
                    )
                );
            }

            if ((int) ($previousEmployeeId ?? 0) !== (int) ($requestedEmployeeId ?? 0)) {
                AuditHelper::log(
                    'user_employee_link_updated',
                    'User',
                    $user->id,
                    sprintf(
                        'previous_employee_id=%s | new_employee_id=%s | changed_by=%s',
                        $previousEmployeeId ?? '-',
                        $requestedEmployeeId ?? '-',
                        (string) auth()->id()
                    )
                );
            }
        });

        return redirect()->route('users.index')->with('success', __('users.flash_updated'));
    }

    public function destroy(User $user)
    {
        $this->authorizeUsers();

        if (auth()->id() === $user->id) {
            return redirect()->route('users.index')->with('error', __('users.error_cannot_delete_self'));
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', __('users.flash_deleted'));
    }

    public function suspend($id)
    {
        $this->authorizeUsers();

        $user = User::findOrFail($id);

        $user->is_active = 0;
        $user->approval_status = 'suspended';
        $user->save();

        return redirect()->back()->with('success', __('users.flash_suspended'));
    }

    public function reactivate($id)
    {
        $this->authorizeUsers();

        $user = User::findOrFail($id);

        $user->is_active = 1;
        $user->approval_status = 'approved';
        $user->approved_at = now();
        $user->approved_by = auth()->id();
        $user->save();

        return redirect()->back()->with('success', __('users.flash_reactivated'));
    }
}
