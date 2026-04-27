<?php

namespace App\Http\Controllers;

use App\Helpers\AuditHelper;
use App\Models\InstallationFactoryRequest;
use App\Models\InstallationFactoryRequestItem;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstallationFactoryRequestController extends Controller
{
    public function create(Project $project): RedirectResponse|View
    {
        $this->abortUnlessCanManageInstallations();

        $this->ensureInstallationProject($project);

        $existingDraft = InstallationFactoryRequest::query()
            ->where('project_id', $project->id)
            ->where('created_by', auth()->id())
            ->where('status', InstallationFactoryRequest::STATUS_DRAFT)
            ->first();

        if ($existingDraft) {
            return redirect()
                ->route('installations.factory-requests.edit', [$project, $existingDraft])
                ->with('success', __('factory.flash_install_draft_redirect'));
        }

        return view('installations.factory-requests.create', [
            'project' => $project,
            'installationRequest' => null,
            'items' => collect([(object) [
                'item_name' => '',
                'description' => null,
                'quantity' => 1,
                'unit' => '',
                'reason' => null,
                'notes' => null,
            ]]),
        ]);
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        $this->abortUnlessCanManageInstallations();

        $this->ensureInstallationProject($project);

        $action = $request->input('action', 'draft');

        $validated = $this->validateRequest($request, $action === 'submit');

        $installationRequest = InstallationFactoryRequest::create([
            'project_id' => $project->id,
            'created_by' => auth()->id(),
            'status' => $action === 'submit'
                ? InstallationFactoryRequest::STATUS_SUBMITTED
                : InstallationFactoryRequest::STATUS_DRAFT,
            'notes' => $validated['notes'] ?? null,
            'submitted_at' => $action === 'submit' ? now() : null,
        ]);

        $this->syncItems($installationRequest, $validated['items'] ?? []);

        $projectName = $project->name;

        if ($action === 'submit') {
            AuditHelper::log(
                'request_submitted_to_factory',
                InstallationFactoryRequest::class,
                $installationRequest->id,
                "تم إرسال طلب التركيبات إلى المصنع لمشروع: {$projectName}",
            );
        } else {
            AuditHelper::log(
                'request_created_draft',
                InstallationFactoryRequest::class,
                $installationRequest->id,
                "تم إنشاء طلب من التركيبات للمصنع لمشروع: {$projectName}",
            );
        }

        $message = $action === 'submit'
            ? __('factory.flash_install_submitted')
            : __('factory.flash_install_draft_saved');

        return redirect()
            ->route('installations.show', $project)
            ->with('success', $message);
    }

    public function edit(Project $project, InstallationFactoryRequest $installationFactoryRequest): View
    {
        $this->abortUnlessCanManageInstallations();

        $this->ensureInstallationProject($project);

        $this->authorizeOwnRequest($installationFactoryRequest);

        if ($installationFactoryRequest->project_id !== $project->id) {
            abort(404);
        }

        if ($installationFactoryRequest->status !== InstallationFactoryRequest::STATUS_DRAFT) {
            abort(403, __('factory.abort_install_cannot_edit_submitted'));
        }

        $installationFactoryRequest->load('items');

        return view('installations.factory-requests.edit', [
            'project' => $project,
            'installationRequest' => $installationFactoryRequest,
            'items' => $installationFactoryRequest->items,
        ]);
    }

    public function update(Request $request, Project $project, InstallationFactoryRequest $installationFactoryRequest): RedirectResponse
    {
        $this->abortUnlessCanManageInstallations();

        $this->ensureInstallationProject($project);

        $this->authorizeOwnRequest($installationFactoryRequest);

        if ($installationFactoryRequest->project_id !== $project->id) {
            abort(404);
        }

        if ($installationFactoryRequest->status !== InstallationFactoryRequest::STATUS_DRAFT) {
            abort(403, __('factory.abort_install_cannot_edit_submitted'));
        }

        $action = $request->input('action', 'draft');

        $validated = $this->validateRequest($request, $action === 'submit');

        $projectName = $project->name;

        $installationFactoryRequest->update([
            'notes' => $validated['notes'] ?? null,
            'status' => $action === 'submit'
                ? InstallationFactoryRequest::STATUS_SUBMITTED
                : InstallationFactoryRequest::STATUS_DRAFT,
            'submitted_at' => $action === 'submit' ? now() : null,
        ]);

        $this->syncItems($installationFactoryRequest, $validated['items'] ?? []);

        AuditHelper::log(
            'request_updated',
            InstallationFactoryRequest::class,
            $installationFactoryRequest->id,
            "تم تحديث طلب التركيبات للمصنع لمشروع: {$projectName}" . ($action === 'submit' ? ' (وإرساله إلى المصنع)' : ''),
        );

        if ($action === 'submit') {
            AuditHelper::log(
                'request_submitted_to_factory',
                InstallationFactoryRequest::class,
                $installationFactoryRequest->id,
                "تم إرسال طلب التركيبات إلى المصنع لمشروع: {$projectName}",
            );
        }

        $message = $action === 'submit'
            ? __('factory.flash_install_submitted')
            : __('factory.flash_install_draft_saved');

        return redirect()
            ->route('installations.show', $project)
            ->with('success', $message);
    }

    private function abortUnlessCanManageInstallations(): void
    {
        if (! auth()->user()?->canManageInstallations()) {
            abort(403, __('factory.abort_installations_forbidden'));
        }
    }

    private function ensureInstallationProject(Project $project): void
    {
        if ($project->current_stage !== 'production_installation') {
            abort(404);
        }
    }

    private function authorizeOwnRequest(InstallationFactoryRequest $installationFactoryRequest): void
    {
        $user = auth()->user();
        if ($user->isAdminLike()) {
            return;
        }

        if ((int) $installationFactoryRequest->created_by !== (int) $user->id) {
            abort(403, __('factory.abort_install_not_owner'));
        }
    }

    /**
     * @return array{notes: ?string, items: array<int, array<string, mixed>>}
     */
    private function validateRequest(Request $request, bool $submitting): array
    {
        $rules = [
            'notes' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.item_name' => 'nullable|string|max:255',
            'items.*.description' => 'nullable|string',
            'items.*.quantity' => 'nullable|integer|min:1',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.reason' => 'nullable|string',
            'items.*.notes' => 'nullable|string',
        ];

        if ($submitting) {
            $rules['items'] = 'required|array|min:1';
            $rules['items.*.item_name'] = 'required|string|max:255';
            $rules['items.*.quantity'] = 'required|integer|min:1';
            $rules['items.*.unit'] = 'required|string|max:50';
        }

        return $request->validate($rules);
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    private function syncItems(InstallationFactoryRequest $installationFactoryRequest, array $items): void
    {
        $installationFactoryRequest->items()->delete();

        foreach ($items as $row) {
            if (! is_array($row)) {
                continue;
            }

            $name = trim((string) ($row['item_name'] ?? ''));
            if ($name === '') {
                continue;
            }

            InstallationFactoryRequestItem::create([
                'request_id' => $installationFactoryRequest->id,
                'item_name' => $name,
                'description' => $row['description'] ?? null,
                'quantity' => max(1, (int) ($row['quantity'] ?? 1)),
                'unit' => trim((string) ($row['unit'] ?? '')) !== ''
                    ? trim((string) $row['unit'])
                    : __('factory.default_item_unit'),
                'reason' => $row['reason'] ?? null,
                'notes' => $row['notes'] ?? null,
            ]);
        }
    }
}
