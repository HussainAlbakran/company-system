<?php

namespace App\Http\Controllers;

use App\Helpers\AuditHelper;
use App\Models\ArchitectMaterialRequest;
use App\Models\Purchase;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ArchitectMaterialRequestController extends Controller
{
    public function create(Project $project)
    {
        return $this->materialRequirements($project);
    }

    /**
     * Standalone material requirements hub (purchase materials for this project).
     * Separate URL from Designs / architect-tasks.
     */
    public function materialRequirements(Project $project)
    {
        $this->authorizeArchitect();

        $requestsQuery = ArchitectMaterialRequest::query()
            ->withCount('items')
            ->where('project_id', $project->id)
            ->latest();

        if (! auth()->user()->isAdmin()) {
            $requestsQuery->where('created_by', auth()->id());
        }

        $materialRequests = $requestsQuery->get();

        return view('architect.material-requirements.index', compact('project', 'materialRequests'));
    }

    public function store(Request $request, Project $project)
    {
        $this->authorizeArchitect();

        $validated = $request->validate([
            'notes' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,xlsx,csv|max:10240',
            'items' => 'required|array|min:1',
            'items.*.material_name' => 'required|string|max:255',
            'items.*.description' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'required|string|max:50',
            'items.*.notes' => 'nullable|string',
            'action_type' => 'required|in:draft,submit',
        ]);

        $status = $validated['action_type'] === 'submit' ? 'submitted' : 'draft';
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('architect-material-requests');
            AuditHelper::log(
                'file_uploaded',
                'ArchitectMaterialRequest',
                null,
                'module=designs | file_name=' . $request->file('attachment')->getClientOriginalName()
            );
        }

        DB::transaction(function () use ($project, $validated, $status, $attachmentPath): void {
            $materialRequest = ArchitectMaterialRequest::create([
                'project_id' => $project->id,
                'created_by' => auth()->id(),
                'status' => $status,
                'notes' => $validated['notes'] ?? null,
                'rejection_reason' => null,
                'attachment_path' => $attachmentPath,
                'submitted_at' => $status === 'submitted' ? now() : null,
                'approved_at' => null,
                'approved_by' => null,
            ]);

            foreach ($validated['items'] as $item) {
                $materialRequest->items()->create($item);
            }
        });

        return redirect()
            ->route('architect.project-material-requirements', $project)
            ->with('success', $status === 'submitted'
                ? __('architect.material_sent_purchasing')
                : __('architect.material_saved_draft'));
    }

    public function edit(Project $project, ArchitectMaterialRequest $materialRequest)
    {
        $this->authorizeArchitect();
        $this->authorizeArchitectRequestOwnership($materialRequest);

        abort_unless($materialRequest->project_id === $project->id, 404);
        abort_unless(in_array($materialRequest->status, ['draft', 'rejected'], true), 403, __('architect.abort_edit_draft_only'));

        $materialRequest->load('items');

        return view('architect.material-requests.edit', compact('project', 'materialRequest'));
    }

    public function update(Request $request, Project $project, ArchitectMaterialRequest $materialRequest)
    {
        $this->authorizeArchitect();
        $this->authorizeArchitectRequestOwnership($materialRequest);

        abort_unless($materialRequest->project_id === $project->id, 404);
        abort_unless(in_array($materialRequest->status, ['draft', 'rejected'], true), 403, __('architect.abort_edit_draft_only'));

        $validated = $request->validate([
            'notes' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,xlsx,csv|max:10240',
            'items' => 'required|array|min:1',
            'items.*.material_name' => 'required|string|max:255',
            'items.*.description' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'required|string|max:50',
            'items.*.notes' => 'nullable|string',
            'action_type' => 'required|in:draft,submit',
        ]);

        $status = $validated['action_type'] === 'submit' ? 'submitted' : 'draft';
        $attachmentPath = $materialRequest->attachment_path;

        if ($request->hasFile('attachment')) {
            if ($attachmentPath && Storage::exists($attachmentPath)) {
                Storage::delete($attachmentPath);
            }
            $attachmentPath = $request->file('attachment')->store('architect-material-requests');
            AuditHelper::log(
                'file_uploaded',
                'ArchitectMaterialRequest',
                null,
                'module=designs | file_name=' . $request->file('attachment')->getClientOriginalName()
            );
        }

        DB::transaction(function () use ($materialRequest, $validated, $status, $attachmentPath): void {
            $materialRequest->update([
                'status' => $status,
                'notes' => $validated['notes'] ?? null,
                'rejection_reason' => $status === 'submitted' ? null : $materialRequest->rejection_reason,
                'attachment_path' => $attachmentPath,
                'submitted_at' => $status === 'submitted' ? now() : null,
                'approved_at' => $status === 'submitted' ? null : $materialRequest->approved_at,
                'approved_by' => $status === 'submitted' ? null : $materialRequest->approved_by,
            ]);

            $materialRequest->items()->delete();
            foreach ($validated['items'] as $item) {
                $materialRequest->items()->create($item);
            }
        });

        return redirect()
            ->route('architect.project-material-requirements', $project)
            ->with('success', $status === 'submitted'
                ? __('architect.material_sent_purchasing')
                : __('architect.material_updated_draft'));
    }

    public function purchasesIndex()
    {
        $this->authorizePurchases();

        $requests = ArchitectMaterialRequest::query()
            ->with(['project:id,name,project_code', 'creator:id,name'])
            ->whereIn('status', ['submitted', 'approved', 'rejected', 'processing', 'completed'])
            ->latest()
            ->paginate(20);

        return view('purchases.material-requests.index', compact('requests'));
    }

    public function purchasesShow(ArchitectMaterialRequest $materialRequest)
    {
        $this->authorizePurchases();

        abort_unless($materialRequest->status !== 'draft', 404);
        $materialRequest->load(['project', 'creator', 'approver', 'items']);
        $convertedPurchasesCount = Purchase::query()
            ->where('architect_material_request_id', $materialRequest->id)
            ->count();

        return view('purchases.material-requests.show', compact('materialRequest', 'convertedPurchasesCount'));
    }

    public function updateStatus(Request $request, ArchitectMaterialRequest $materialRequest)
    {
        $this->authorizePurchases();

        $validated = $request->validate([
            'status' => 'required|in:processing,completed',
        ]);

        abort_unless(in_array($materialRequest->status, ['approved', 'processing'], true), 422);

        $materialRequest->update(['status' => $validated['status']]);

        return back()->with('success', __('purchases.material_flash_status_updated'));
    }

    public function approve(ArchitectMaterialRequest $materialRequest)
    {
        $this->authorizePurchases();
        abort_unless($materialRequest->status === 'submitted', 422);

        $materialRequest->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'rejection_reason' => null,
        ]);

        return back()->with('success', __('purchases.material_flash_approved'));
    }

    public function reject(Request $request, ArchitectMaterialRequest $materialRequest)
    {
        $this->authorizePurchases();
        abort_unless($materialRequest->status === 'submitted', 422);

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $materialRequest->update([
            'status' => 'rejected',
            'rejection_reason' => trim($validated['rejection_reason']),
            'approved_at' => null,
            'approved_by' => null,
        ]);

        return back()->with('success', __('purchases.material_flash_rejected'));
    }

    public function convertToPurchases(ArchitectMaterialRequest $materialRequest)
    {
        $this->authorizePurchases();

        abort_unless(in_array($materialRequest->status, ['approved', 'processing'], true), 422);

        $existingCount = Purchase::query()
            ->where('architect_material_request_id', $materialRequest->id)
            ->count();
        if ($existingCount > 0) {
            return back()->with('success', __('purchases.material_flash_already_converted'));
        }

        $materialRequest->loadMissing('items');
        abort_unless($materialRequest->items->isNotEmpty(), 422, __('purchases.material_convert_requires_items'));

        DB::transaction(function () use ($materialRequest): void {
            foreach ($materialRequest->items as $item) {
                Purchase::create([
                    'project_id' => $materialRequest->project_id,
                    'architect_material_request_id' => $materialRequest->id,
                    'type' => 'contract_purchase',
                    'title' => $item->material_name,
                    'description' => $item->description,
                    'quantity' => max(1, (int) ceil((float) $item->quantity)),
                    'cost' => 0,
                    'vendor' => null,
                    'purchase_date' => now()->toDateString(),
                    'notes' => trim(
                        'Generated from architect material request #' . $materialRequest->id
                        . ' | Source item #' . $item->id
                        . ' | Unit: ' . $item->unit
                        . ($item->notes ? ' | Item notes: ' . $item->notes : '')
                    ),
                    'created_by' => auth()->id(),
                ]);
            }

            $materialRequest->update([
                'status' => 'processing',
            ]);
        });

        return back()->with('success', __('purchases.material_flash_converted'));
    }

    public function downloadAttachment(ArchitectMaterialRequest $materialRequest)
    {
        $user = auth()->user();
        abort_unless($user, 403);

        $canDownload = $user->hasAnyRole(['admin', 'manager'])
            || $user->canAccessProcurementModule()
            || ($user->canAccessEngineeringModule() && ($user->isAdmin() || $materialRequest->created_by === $user->id));

        abort_unless($canDownload, 403);
        abort_unless($materialRequest->attachment_path && Storage::exists($materialRequest->attachment_path), 404);

        return Storage::download($materialRequest->attachment_path);
    }

    private function authorizeArchitect(): void
    {
        abort_unless(auth()->check() && auth()->user()->canAccessDesignsModule(), 403, __('architect.abort_module'));
    }

    private function authorizePurchases(): void
    {
        abort_unless(auth()->check() && auth()->user()->canAccessProcurementModule(), 403, __('architect.abort_module'));
    }

    private function authorizeArchitectRequestOwnership(ArchitectMaterialRequest $materialRequest): void
    {
        if (auth()->user()->isAdmin()) {
            return;
        }

        abort_unless($materialRequest->created_by === auth()->id(), 403, __('architect.abort_request_owner'));
    }
}
