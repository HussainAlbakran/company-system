<?php

namespace App\Http\Controllers;

use App\Helpers\AuditHelper;
use App\Models\ArchitectMaterialRequest;
use App\Models\ArchitectTask;
use App\Models\ContractPayment;
use App\Models\Project;
use App\Models\ProjectReport;
use App\Models\ProjectUpdate;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProjectReportController extends Controller
{
    private function authorizeBoard(): void
    {
        if (! auth()->check() || ! auth()->user()->canViewProjectReportsBoard()) {
            abort(403, __('project_reports.abort_board'));
        }
    }

    private function authorizeSubmit(): void
    {
        if (! auth()->check() || ! auth()->user()->canSubmitProjectReports()) {
            abort(403, __('project_reports.abort_submit'));
        }
    }

    private function authorizeAccess(): void
    {
        if (! auth()->check() || ! auth()->user()->canAccessProjectReportsModule()) {
            abort(403, __('project_reports.abort_unauthorized'));
        }
    }

    /**
     * Admin-only: active (non-archived) projects as cards.
     */
    public function board(Request $request)
    {
        $this->authorizeBoard();

        $projects = Project::query()
            ->active()
            ->withCount('reports')
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = '%'.$request->search.'%';
                $query->where(function ($inner) use ($term) {
                    $inner->where('name', 'like', $term)
                        ->orWhere('project_code', 'like', $term)
                        ->orWhere('client_name', 'like', $term);
                });
            })
            ->orderByDesc('id')
            ->get();

        return view('project-reports.board', compact('projects'));
    }

    /**
     * Admin-only: archived (completed) projects, filterable by year.
     */
    public function archive(Request $request)
    {
        $this->authorizeBoard();

        $year = $request->filled('year') ? (int) $request->year : (int) date('Y');

        $availableYears = Project::query()
            ->archived()
            ->selectRaw('YEAR(COALESCE(completed_at, end_date, created_at)) as archive_year')
            ->groupBy('archive_year')
            ->orderByDesc('archive_year')
            ->pluck('archive_year')
            ->filter()
            ->map(fn ($y) => (int) $y)
            ->values();

        if ($availableYears->isEmpty()) {
            $availableYears = collect([(int) date('Y')]);
        }

        if (! $availableYears->contains($year)) {
            $year = (int) $availableYears->first();
        }

        $projects = Project::query()
            ->archived()
            ->withCount('reports')
            ->whereRaw('YEAR(COALESCE(completed_at, end_date, created_at)) = ?', [$year])
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = '%'.$request->search.'%';
                $query->where(function ($inner) use ($term) {
                    $inner->where('name', 'like', $term)
                        ->orWhere('project_code', 'like', $term)
                        ->orWhere('client_name', 'like', $term);
                });
            })
            ->orderByDesc('completed_at')
            ->orderByDesc('id')
            ->get();

        return view('project-reports.archive', compact('projects', 'year', 'availableYears'));
    }

    /**
     * Mark project as completed and move it to the previous-projects archive.
     * Requires a completion letter upload for a safe completion trail.
     */
    public function complete(Request $request, Project $project)
    {
        $this->authorizeBoard();

        if ($project->isCompleted()) {
            return redirect()
                ->route('project-reports.show', $project)
                ->with('success', __('project_reports.flash_already_completed'));
        }

        $maxKb = $this->maxUploadKilobytes();

        $validated = $request->validate([
            'completion_letter' => [
                'required',
                'file',
                'max:'.$maxKb,
                // Keep it strict: termination letters are usually PDF / Word.
                'mimes:pdf,doc,docx,txt',
            ],
        ], [
            'completion_letter.required' => __('project_reports.error_completion_letter_required'),
            'completion_letter.file' => __('project_reports.error_completion_letter_type'),
            'completion_letter.max' => __('project_reports.error_completion_letter_too_large', [
                'max' => $this->formatMegabytes($maxKb),
            ]),
        ], [
            'completion_letter' => __('project_reports.field_completion_letter'),
        ]);

        $letterFile = $validated['completion_letter'];
        $storedPath = $this->storeCompletionLetterFile($project, $letterFile);

        $this->completeProjectWithLetter($project, $storedPath);

        return redirect()
            ->route('project-reports.archive', ['year' => now()->year])
            ->with('success', __('project_reports.flash_completed'));
    }

    private function storeCompletionLetterFile(Project $project, $file): string
    {
        $directory = 'project_completion_letters/'.$project->id;
        Storage::disk('local')->makeDirectory($directory);

        $storedPath = $file->store($directory, 'local');

        if (! is_string($storedPath) || $storedPath === '' || ! Storage::disk('local')->exists($storedPath)) {
            throw new \RuntimeException(__('project_reports.error_store_failed'));
        }

        return $storedPath;
    }

    private function completeProjectWithLetter(Project $project, ?string $completionLetterPath): void
    {
        if ($project->isCompleted()) {
            return;
        }

        $project->update([
            'current_stage' => 'completed',
            'status' => 'completed',
            'completed_at' => now(),
            'completion_letter_path' => $completionLetterPath,
        ]);

        AuditHelper::log(
            'project_completed_with_letter',
            'Project',
            $project->id,
            'تم اكتمال المشروع وأرشفته ضمن المشاريع السابقة: '.$project->name.' | خطاب الإنهاء: '.(string) $completionLetterPath
        );
    }

    /**
     * Register a project report (admin / ops / eng manager / engineer).
     */
    public function create(Request $request)
    {
        $this->authorizeSubmit();

        $projects = Project::query()
            ->active()
            ->orderBy('name')
            ->get(['id', 'name', 'project_code', 'client_name', 'status']);

        $selectedProjectId = old('project_id', $request->get('project_id'));
        if ($selectedProjectId && ! $projects->contains('id', (int) $selectedProjectId)) {
            $selectedProjectId = null;
        }

        $maxUploadMb = $this->formatMegabytes($this->maxUploadKilobytes());

        return view('project-reports.create', [
            'projects' => $projects,
            'selectedProjectId' => $selectedProjectId,
            'reportTypes' => ProjectReport::TYPES,
            'maxUploadMb' => $maxUploadMb,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeSubmit();

        $maxKb = $this->maxUploadKilobytes();
        $allowedExtensions = [
            'pdf', 'xls', 'xlsx', 'csv', 'doc', 'docx', 'txt',
            'jpg', 'jpeg', 'png', 'gif', 'webp',
            'zip', 'rar', '7z',
            'dwg', 'dxf',
            'ppt', 'pptx',
        ];

        if ($request->hasFile('file') && ! $request->file('file')->isValid()) {
            $errorCode = (int) $request->file('file')->getError();
            $message = match ($errorCode) {
                UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => __('project_reports.error_file_too_large', [
                    'max' => $this->formatMegabytes($maxKb),
                ]),
                UPLOAD_ERR_PARTIAL => __('project_reports.error_file_partial'),
                UPLOAD_ERR_NO_FILE => __('project_reports.error_file_required'),
                default => __('project_reports.error_upload_failed'),
            };

            return back()
                ->withInput($request->except('file'))
                ->withErrors(['file' => $message]);
        }

        $validated = $request->validate([
            'project_id' => [
                'required',
                'integer',
                Rule::exists('projects', 'id')->where(function ($query) {
                    $query->whereNull('completed_at')
                        ->where(function ($status) {
                            $status->whereNull('status')->orWhere('status', '!=', 'completed');
                        })
                        ->where(function ($stage) {
                            $stage->whereNull('current_stage')->orWhere('current_stage', '!=', 'completed');
                        });
                }),
            ],
            'report_type' => ['required', Rule::in(array_merge(ProjectReport::TYPES, ['completion_letter']))],
            'report_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'file' => [
                'required',
                'file',
                'max:'.$maxKb,
            ],
        ], [
            'project_id.exists' => __('project_reports.error_project_completed'),
            'file.required' => __('project_reports.error_file_required'),
            'file.file' => __('project_reports.error_upload_failed'),
            'file.max' => __('project_reports.error_file_too_large', [
                'max' => $this->formatMegabytes($maxKb),
            ]),
            'file.uploaded' => __('project_reports.error_file_too_large', [
                'max' => $this->formatMegabytes($maxKb),
            ]),
        ], [
            'project_id' => __('project_reports.field_project'),
            'report_type' => __('project_reports.field_type'),
            'report_date' => __('project_reports.field_date'),
            'file' => __('project_reports.field_file'),
        ]);

        $project = Project::query()->findOrFail($validated['project_id']);

        if ($project->isCompleted()) {
            return back()
                ->withInput($request->except('file'))
                ->withErrors(['project_id' => __('project_reports.error_project_completed')]);
        }

        $isCompletionLetter = ($validated['report_type'] === 'completion_letter');

        $file = $request->file('file');
        $extension = strtolower((string) $file->getClientOriginalExtension());

        if ($extension === '' || ! in_array($extension, $allowedExtensions, true)) {
            return back()
                ->withInput($request->except('file'))
                ->withErrors([
                    'file' => __('project_reports.error_file_type', [
                        'types' => implode(', ', $allowedExtensions),
                    ]),
                ]);
        }

        if ($isCompletionLetter) {
            $allowedCompletionExtensions = ['pdf', 'doc', 'docx', 'txt'];
            if (! in_array($extension, $allowedCompletionExtensions, true)) {
                return back()
                    ->withInput($request->except('file'))
                    ->withErrors([
                        'file' => __('project_reports.error_completion_letter_type'),
                    ]);
            }
        }

        try {
            if ($isCompletionLetter) {
                $storedPath = $this->storeCompletionLetterFile($project, $file);

                // Mark project completed and archive it after storing the letter.
                DB::transaction(function () use ($project, $storedPath) {
                    $this->completeProjectWithLetter($project, $storedPath);
                });

                AuditHelper::log(
                    'project_completed_letter_uploaded',
                    'Project',
                    $project->id,
                    'completion_letter stored and project archived'
                );
            } else {
                $directory = 'project_reports/'.$project->id;
                Storage::disk('local')->makeDirectory($directory);

                $storedPath = $file->store($directory, 'local');

                if (! is_string($storedPath) || $storedPath === '' || ! Storage::disk('local')->exists($storedPath)) {
                    return back()
                        ->withInput($request->except('file'))
                        ->withErrors(['file' => __('project_reports.error_store_failed')]);
                }

                $report = DB::transaction(function () use ($validated, $project, $file, $storedPath) {
                    $report = ProjectReport::query()->create([
                        'project_id' => $project->id,
                        'uploaded_by' => auth()->id(),
                        'report_type' => $validated['report_type'],
                        'report_date' => $validated['report_date'],
                        'original_name' => $file->getClientOriginalName(),
                        'stored_path' => $storedPath,
                        'mime_type' => $file->getClientMimeType() ?: $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'notes' => $validated['notes'] ?? null,
                    ]);

                    AuditHelper::log(
                        'create',
                        'ProjectReport',
                        $report->id,
                        sprintf(
                            'route=project-reports.store | project_id=%s | report_type=%s | file=%s | uploaded_by=%s',
                            $project->id,
                            $report->report_type,
                            $report->original_name,
                            (string) auth()->id()
                        )
                    );

                    return $report;
                });
            }
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput($request->except('file'))
                ->withErrors(['file' => __('project_reports.error_store_failed')]);
        }

        if ($isCompletionLetter) {
            if (auth()->user()->canViewProjectReportsBoard()) {
                return redirect()
                    ->route('project-reports.show', $project)
                    ->with('success', __('project_reports.flash_completed'));
            }

            return redirect()
                ->route('project-reports.create')
                ->with('success', __('project_reports.flash_completed'));
        }

        if (auth()->user()->canViewProjectReportsBoard()) {
            return redirect()
                ->route('project-reports.show', $project)
                ->with('success', __('project_reports.flash_created'));
        }

        return redirect()
            ->route('project-reports.create')
            ->with('success', __('project_reports.flash_created'));
    }

    public function downloadCompletionLetter(Project $project)
    {
        $this->authorizeBoard();

        abort_unless(
            ! empty($project->completion_letter_path) && Storage::disk('local')->exists($project->completion_letter_path),
            404,
            __('project_reports.file_missing')
        );

        AuditHelper::log(
            'read',
            'Project',
            $project->id,
            sprintf(
                'route=project-reports.completion-letter | project_id=%s | by=%s',
                $project->id,
                (string) auth()->id()
            )
        );

        return Storage::disk('local')->download(
            $project->completion_letter_path,
            basename((string) $project->completion_letter_path)
        );
    }

    /**
     * Laravel file max is in kilobytes; clamp to PHP upload_max_filesize.
     */
    private function maxUploadKilobytes(): int
    {
        $phpBytes = $this->phpSizeToBytes((string) ini_get('upload_max_filesize'));
        $postBytes = $this->phpSizeToBytes((string) ini_get('post_max_size'));
        $limitBytes = min($phpBytes, $postBytes);

        // Keep a small safety margin under post_max_size for form fields.
        $limitBytes = max(1024 * 100, $limitBytes - (1024 * 100));

        $desiredBytes = 20 * 1024 * 1024; // 20 MB app target
        $effectiveBytes = min($desiredBytes, $limitBytes);

        return max(1, (int) floor($effectiveBytes / 1024));
    }

    private function phpSizeToBytes(string $value): int
    {
        $value = trim($value);
        if ($value === '') {
            return 2 * 1024 * 1024;
        }

        $unit = strtolower(substr($value, -1));
        $number = (float) $value;

        return (int) match ($unit) {
            'g' => $number * 1024 * 1024 * 1024,
            'm' => $number * 1024 * 1024,
            'k' => $number * 1024,
            default => (float) $value,
        };
    }

    private function formatMegabytes(int $kilobytes): string
    {
        $mb = $kilobytes / 1024;

        return rtrim(rtrim(number_format($mb, 1, '.', ''), '0'), '.');
    }

    /**
     * Project report tables by type + payments, designs, purchases, maintenance.
     * Board/archive viewers only (الإدارة العليا + مدير النظام).
     */
    public function show(Project $project)
    {
        $this->authorizeBoard();

        $project->load(['salesContract:id,project_id,contract_no,client_name,project_value']);

        $reports = ProjectReport::query()
            ->with('uploader:id,name,email')
            ->where('project_id', $project->id)
            ->orderByDesc('report_date')
            ->orderByDesc('id')
            ->get()
            ->groupBy('report_type');

        $payments = ContractPayment::query()
            ->whereHas('contract', function ($query) use ($project) {
                $query->where(function ($inner) use ($project) {
                    $inner->where('project_id', $project->id);
                    if ($project->sales_contract_id) {
                        $inner->orWhere('id', $project->sales_contract_id);
                    }
                });
            })
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->get();

        $designFiles = $this->collectDesignFiles($project);

        $purchases = Purchase::query()
            ->with(['creator:id,name', 'architectMaterialRequest:id,status'])
            ->where('project_id', $project->id)
            ->whereNotIn('type', ['repair', 'general_maintenance'])
            ->orderByDesc('purchase_date')
            ->orderByDesc('id')
            ->get();

        $maintenanceItems = Purchase::query()
            ->with('creator:id,name')
            ->where('project_id', $project->id)
            ->whereIn('type', ['repair', 'general_maintenance'])
            ->orderByDesc('purchase_date')
            ->orderByDesc('id')
            ->get();

        $materialRequests = ArchitectMaterialRequest::query()
            ->with(['creator:id,name', 'items'])
            ->where('project_id', $project->id)
            ->orderByDesc('id')
            ->get();

        return view('project-reports.show', [
            'project' => $project,
            'projectReports' => $reports->get(ProjectReport::TYPE_PROJECT, collect()),
            'weeklyReports' => $reports->get(ProjectReport::TYPE_WEEKLY, collect()),
            'financialReports' => $reports->get(ProjectReport::TYPE_FINANCIAL_DISTRESS, collect()),
            'accidentReports' => $reports->get(ProjectReport::TYPE_SITE_ACCIDENT, collect()),
            'delayReports' => $reports->get(ProjectReport::TYPE_DELAY, collect()),
            'payments' => $payments,
            'designFiles' => $designFiles,
            'purchases' => $purchases,
            'maintenanceItems' => $maintenanceItems,
            'materialRequests' => $materialRequests,
        ]);
    }

    /**
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    private function collectDesignFiles(Project $project)
    {
        $files = collect();

        if (! empty($project->project_pdf) && Storage::disk('public')->exists($project->project_pdf)) {
            $files->push([
                'label' => __('project_reports.design_project_pdf'),
                'name' => basename($project->project_pdf),
                'url' => asset('storage/'.$project->project_pdf),
                'source' => 'project_pdf',
                'date' => optional($project->updated_at)->timezone(config('app.timezone')),
            ]);
        }

        $architectTask = ArchitectTask::query()
            ->where('project_id', $project->id)
            ->first();

        if ($architectTask) {
            if (! empty($architectTask->drawing_file) && Storage::disk('public')->exists($architectTask->drawing_file)) {
                $files->push([
                    'label' => __('project_reports.design_drawing'),
                    'name' => basename($architectTask->drawing_file),
                    'url' => asset('storage/'.$architectTask->drawing_file),
                    'source' => 'architect_drawing',
                    'date' => optional($architectTask->updated_at)->timezone(config('app.timezone')),
                ]);
            }

            if (! empty($architectTask->planning_file) && Storage::disk('public')->exists($architectTask->planning_file)) {
                $files->push([
                    'label' => __('project_reports.design_planning'),
                    'name' => basename($architectTask->planning_file),
                    'url' => asset('storage/'.$architectTask->planning_file),
                    'source' => 'architect_planning',
                    'date' => optional($architectTask->updated_at)->timezone(config('app.timezone')),
                ]);
            }
        }

        $updates = ProjectUpdate::query()
            ->where('project_id', $project->id)
            ->whereNotNull('attachment')
            ->where('attachment', '!=', '')
            ->orderByDesc('id')
            ->get(['id', 'title', 'attachment', 'created_at']);

        foreach ($updates as $update) {
            if (! Storage::disk('public')->exists($update->attachment)) {
                continue;
            }

            $files->push([
                'label' => $update->title ?: __('project_reports.design_update'),
                'name' => basename($update->attachment),
                'url' => asset('storage/'.$update->attachment),
                'source' => 'project_update',
                'date' => optional($update->created_at)->timezone(config('app.timezone')),
            ]);
        }

        return $files->values();
    }

    public function downloadMaterialRequestAttachment(Project $project, ArchitectMaterialRequest $materialRequest)
    {
        $this->authorizeBoard();

        abort_unless((int) $materialRequest->project_id === (int) $project->id, 404);
        abort_unless(
            $materialRequest->attachment_path && Storage::exists($materialRequest->attachment_path),
            404,
            __('project_reports.file_missing')
        );

        AuditHelper::log(
            'read',
            'ArchitectMaterialRequest',
            $materialRequest->id,
            sprintf(
                'route=project-reports.material-attachment | project_id=%s | by=%s',
                $project->id,
                (string) auth()->id()
            )
        );

        return Storage::download($materialRequest->attachment_path);
    }

    public function download(ProjectReport $projectReport)
    {
        $this->authorizeAccess();

        if (! Storage::disk('local')->exists($projectReport->stored_path)) {
            abort(404, __('project_reports.file_missing'));
        }

        AuditHelper::log(
            'read',
            'ProjectReport',
            $projectReport->id,
            sprintf(
                'route=project-reports.download | project_id=%s | file=%s | by=%s',
                $projectReport->project_id,
                $projectReport->original_name,
                (string) auth()->id()
            )
        );

        return Storage::disk('local')->download(
            $projectReport->stored_path,
            $projectReport->original_name
        );
    }

    public function destroy(ProjectReport $projectReport)
    {
        $this->authorizeBoard();

        $path = $projectReport->stored_path;
        $projectId = $projectReport->project_id;
        $reportId = $projectReport->id;

        DB::transaction(function () use ($projectReport, $path, $projectId, $reportId) {
            $projectReport->delete();

            if ($path && Storage::disk('local')->exists($path)) {
                Storage::disk('local')->delete($path);
            }

            AuditHelper::log(
                'delete',
                'ProjectReport',
                $reportId,
                sprintf(
                    'route=project-reports.destroy | project_id=%s | deleted_by=%s',
                    $projectId,
                    (string) auth()->id()
                )
            );
        });

        return redirect()
            ->route('project-reports.show', $projectId)
            ->with('success', __('project_reports.flash_deleted'));
    }
}
