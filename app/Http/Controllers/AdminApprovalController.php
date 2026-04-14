<?php

namespace App\Http\Controllers;

use App\Helpers\AuditHelper;
use App\Models\User;
use Illuminate\Http\Request;

class AdminApprovalController extends Controller
{
    protected function authorizeUsers(): void
    {
        if (!auth()->check() || !auth()->user()->canManageUsers()) {
            abort(403, 'غير مصرح لك بالوصول.');
        }
    }

    public function index()
    {
        $this->authorizeUsers();

        $pendingUsers = User::where('approval_status', 'pending')->latest()->get();
        $approvedUsers = User::where('approval_status', 'approved')->latest()->get();
        $rejectedUsers = User::where('approval_status', 'rejected')->latest()->get();
        $suspendedUsers = User::where('approval_status', 'suspended')->latest()->get();

        return view('users.approvals', compact(
            'pendingUsers',
            'approvedUsers',
            'rejectedUsers',
            'suspendedUsers'
        ));
    }

    public function approve(User $user)
    {
        $this->authorizeUsers();

        $user->update([
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'rejection_reason' => null,
            'is_active' => true,
        ]);

        AuditHelper::log(
            'approve',
            'User',
            $user->id,
            'تم اعتماد المستخدم: ' . $user->name
        );

        return back()->with('success', 'تم اعتماد المستخدم بنجاح.');
    }

    public function reject(Request $request, User $user)
    {
        $this->authorizeUsers();

        $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $user->update([
            'approval_status' => 'rejected',
            'approved_at' => null,
            'approved_by' => auth()->id(),
            'rejection_reason' => $request->rejection_reason,
            'is_active' => false,
        ]);

        AuditHelper::log(
            'reject',
            'User',
            $user->id,
            'تم رفض المستخدم: ' . $user->name
        );

        return back()->with('success', 'تم رفض المستخدم.');
    }

    public function suspend(User $user)
    {
        $this->authorizeUsers();

        if (auth()->id() === $user->id) {
            return back()->with('error', 'لا يمكنك إيقاف حسابك الحالي.');
        }

        $user->update([
            'approval_status' => 'suspended',
            'is_active' => false,
        ]);

        AuditHelper::log(
            'suspend',
            'User',
            $user->id,
            'تم إيقاف المستخدم: ' . $user->name
        );

        return back()->with('success', 'تم تعليق المستخدم.');
    }

    public function reactivate(User $user)
    {
        $this->authorizeUsers();

        $user->update([
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'rejection_reason' => null,
            'is_active' => true,
        ]);

        AuditHelper::log(
            'reactivate',
            'User',
            $user->id,
            'تمت إعادة تفعيل المستخدم: ' . $user->name
        );

        return back()->with('success', 'تمت إعادة تفعيل المستخدم.');
    }
}