<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProjectUpdateController extends Controller
{
    protected function authorizeProjects(): void
    {
        if (!auth()->check() || !auth()->user()->canManageProjects()) {
            abort(403, 'غير مصرح لك بالوصول إلى تحديثات المشاريع.');
        }
    }

    public function store(Request $request, Project $project)
    {
        $this->authorizeProjects();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'progress' => ['required', 'integer', 'min:0', 'max:100'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx,txt,csv,xls,xlsx', 'max:10240'],
        ]);

        $filePath = null;
        $extractedText = null;
        $numbers = [];
        $processingStatus = 'done';

        try {
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $filePath = $file->store('project_updates', 'public');

                $fullPath = storage_path('app/public/' . $filePath);
                $extension = strtolower($file->getClientOriginalExtension());

                $readResult = $this->extractAttachmentContent($fullPath, $extension);

                $extractedText = $readResult['text'];
                $numbers = $readResult['numbers'];
                $processingStatus = $readResult['status'];
            }

            DB::beginTransaction();

            ProjectUpdate::create([
                'project_id' => $project->id,
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'progress' => $validated['progress'],
                'attachment' => $filePath,
                'created_by' => Auth::id(),

                'extracted_text' => $extractedText,
                'extracted_numbers_json' => $numbers,
                'ai_summary' => null,
                'processing_status' => $processingStatus,
            ]);

            $project->progress_percentage = $validated['progress'];
            $project->updated_by = Auth::id();
            $project->save();

            DB::commit();

            return back()->with('success', 'تم إضافة التحديث ومعالجة المرفق بنجاح');
        } catch (\Throwable $e) {
            DB::rollBack();

            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة التحديث: ' . $e->getMessage());
        }
    }

    public function destroy(Project $project, ProjectUpdate $update)
    {
        $this->authorizeProjects();

        if ($update->project_id != $project->id) {
            abort(404);
        }

        try {
            DB::beginTransaction();

            if ($update->attachment && Storage::disk('public')->exists($update->attachment)) {
                Storage::disk('public')->delete($update->attachment);
            }

            $update->delete();

            $lastUpdate = $project->updates()->latest()->first();

            $project->progress_percentage = $lastUpdate ? $lastUpdate->progress : 0;
            $project->updated_by = Auth::id();
            $project->save();

            DB::commit();

            return back()->with('success', 'تم حذف التحديث بنجاح.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'حدث خطأ أثناء حذف التحديث: ' . $e->getMessage());
        }
    }

    protected function extractAttachmentContent(string $fullPath, string $extension): array
    {
        $text = null;
        $numbers = [];
        $status = 'done';

        try {
            if (in_array($extension, ['txt', 'csv'])) {
                $text = file_get_contents($fullPath);
            } elseif (in_array($extension, ['xls', 'xlsx'])) {
                // يقرأ Excel فقط إذا كانت المكتبة مثبتة
                if (class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
                    $sheets = \Maatwebsite\Excel\Facades\Excel::toArray([], $fullPath);

                    $buffer = [];

                    foreach ($sheets as $sheet) {
                        foreach ($sheet as $row) {
                            $rowValues = [];

                            foreach ($row as $cell) {
                                if (!is_null($cell) && $cell !== '') {
                                    $rowValues[] = (string) $cell;
                                }
                            }

                            if (!empty($rowValues)) {
                                $buffer[] = implode(' | ', $rowValues);
                            }
                        }
                    }

                    $text = implode("\n", $buffer);
                } else {
                    $status = 'pending_excel_package';
                }
            } else {
                // باقي الصيغ مثل pdf/docx/jpg/png مرفوعة لكن لم نضف قارئها بعد
                $status = 'unsupported_for_text_extraction';
            }

            if (!empty($text)) {
                preg_match_all('/\d+(?:\.\d+)?/', $text, $matches);
                $numbers = $matches[0] ?? [];
            }
        } catch (\Throwable $e) {
            $status = 'failed';
            $text = null;
            $numbers = [];
        }

        return [
            'text' => $text,
            'numbers' => $numbers,
            'status' => $status,
        ];
    }
}