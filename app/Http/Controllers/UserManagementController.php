<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    protected function authorizeUsers(): void
    {
        if (! auth()->check() || ! auth()->user()->canManageUsers()) {
            abort(403, 'غير مصرح لك بالوصول لإدارة المستخدمين.');
        }
    }

    public function index()
    {
        $this->authorizeUsers();

        $users = User::latest()->paginate(15);

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
            'role' => ['required', 'in:admin,hr,engineer,factory_manager,manager'],
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

        return redirect()->route('users.index')->with('success', 'تم إنشاء المستخدم بنجاح.');
    }

    public function show(User $user)
    {
        $this->authorizeUsers();

        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->authorizeUsers();

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeUsers();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:admin,hr,engineer,factory_manager,manager'],
            'password' => ['nullable', 'string', 'min:8'],
            'approval_status' => ['nullable', 'in:pending,approved,rejected,suspended'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if (array_key_exists('approval_status', $validated)) {
            $data['approval_status'] = $validated['approval_status'];
        }

        if (array_key_exists('is_active', $validated)) {
            $data['is_active'] = $validated['is_active'];
        }

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'تم تحديث المستخدم بنجاح.');
    }

    public function destroy(User $user)
    {
        $this->authorizeUsers();

        if (auth()->id() === $user->id) {
            return redirect()->route('users.index')->with('error', 'لا يمكنك حذف حسابك الحالي.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'تم حذف المستخدم بنجاح.');
    }

    public function suspend($id)
    {
        $this->authorizeUsers();

        $user = User::findOrFail($id);

        $user->is_active = 0;
        $user->approval_status = 'suspended';
        $user->save();

        return redirect()->back()->with('success', 'تم إيقاف المستخدم بنجاح');
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

        return redirect()->back()->with('success', 'تمت إعادة تفعيل المستخدم بنجاح');
    }
}