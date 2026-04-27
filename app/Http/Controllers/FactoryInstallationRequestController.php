<?php

namespace App\Http\Controllers;

use App\Helpers\AuditHelper;
use App\Models\InstallationFactoryRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FactoryInstallationRequestController extends Controller
{
    public function index(): View
    {
        $this->abortUnlessCanManageProduction();

        $requests = InstallationFactoryRequest::query()
            ->with(['project', 'creator'])
            ->where('status', '!=', InstallationFactoryRequest::STATUS_DRAFT)
            ->latest()
            ->paginate(20);

        return view('factory.installation-requests.index', compact('requests'));
    }

    public function show(InstallationFactoryRequest $installationFactoryRequest): View
    {
        $this->abortUnlessCanManageProduction();

        if ($installationFactoryRequest->status === InstallationFactoryRequest::STATUS_DRAFT) {
            abort(404);
        }

        $installationFactoryRequest->load(['project', 'creator', 'items']);

        if ($installationFactoryRequest->factory_first_opened_at === null) {
            $installationFactoryRequest->update(['factory_first_opened_at' => now()]);
            $projectName = $installationFactoryRequest->project?->name ?? '-';
            AuditHelper::log(
                'request_opened_by_factory',
                InstallationFactoryRequest::class,
                $installationFactoryRequest->id,
                "تم فتح طلب التركيبات من المصنع لمشروع: {$projectName}",
            );
        }

        return view('factory.installation-requests.show', [
            'requestModel' => $installationFactoryRequest,
        ]);
    }

    public function updateStatus(Request $request, InstallationFactoryRequest $installationFactoryRequest): RedirectResponse
    {
        $this->abortUnlessCanManageProduction();

        if ($installationFactoryRequest->status === InstallationFactoryRequest::STATUS_DRAFT) {
            abort(404);
        }

        $validated = $request->validate([
            'status' => 'required|in:received,processing,completed',
        ]);

        $newStatus = $validated['status'];
        $oldStatus = $installationFactoryRequest->status;

        if ($oldStatus === $newStatus) {
            return back()->with('success', __('factory.flash_install_no_change'));
        }

        if (! $this->isValidStatusTransition($oldStatus, $newStatus)) {
            return back()->with('error', __('factory.flash_install_transition_denied'));
        }

        $installationFactoryRequest->update(['status' => $newStatus]);

        $projectName = $installationFactoryRequest->project?->name ?? '-';
        $newLabel = __('factory.installation_status.'.$newStatus);

        AuditHelper::log(
            'request_status_changed',
            InstallationFactoryRequest::class,
            $installationFactoryRequest->id,
            "تم تغيير حالة طلب المصنع لمشروع: {$projectName} إلى: {$newLabel}",
        );

        return back()->with('success', __('factory.flash_install_status_updated'));
    }

    private function abortUnlessCanManageProduction(): void
    {
        if (! auth()->user()?->canManageProduction()) {
            abort(403, __('factory.abort_installations_forbidden'));
        }
    }

    private function isValidStatusTransition(string $old, string $new): bool
    {
        if ($old === InstallationFactoryRequest::STATUS_SUBMITTED) {
            return in_array($new, [
                InstallationFactoryRequest::STATUS_RECEIVED,
                InstallationFactoryRequest::STATUS_PROCESSING,
                InstallationFactoryRequest::STATUS_COMPLETED,
            ], true);
        }

        if ($old === InstallationFactoryRequest::STATUS_RECEIVED) {
            return in_array($new, [
                InstallationFactoryRequest::STATUS_PROCESSING,
                InstallationFactoryRequest::STATUS_COMPLETED,
            ], true);
        }

        if ($old === InstallationFactoryRequest::STATUS_PROCESSING) {
            return $new === InstallationFactoryRequest::STATUS_COMPLETED;
        }

        return false;
    }
}
