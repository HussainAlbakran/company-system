<?php

namespace App\Http\Controllers;

use App\Helpers\AuditHelper;
use App\Models\Employee;
use App\Models\EmployeeDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeDocumentController extends Controller
{
    protected function authorizeHR(): void
    {
        if (!auth()->check() || !auth()->user()->canManageEmployees()) {
            abort(403, 'غير مصرح لك.');
        }
    }

    public function store(Request $request, Employee $employee)
    {
        $this->authorizeHR();

        $request->validate([
            'document_type' => ['required', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'file' => ['required', 'file', 'max:10240'],
            'notes' => ['nullable', 'string'],
        ]);

        $uploadedFile = $request->file('file');
        $path = $uploadedFile->store('employee_documents', 'public');

        // 🔥 إنشاء السجل أولاً
        $document = EmployeeDocument::create([
            'employee_id'            => $employee->id,
            'document_type'          => $request->document_type,
            'title'                  => $request->title ?: $uploadedFile->getClientOriginalName(),
            'file_path'              => $path,
            'file_name'              => $uploadedFile->getClientOriginalName(),
            'file_size'              => $uploadedFile->getSize(),
            'uploaded_by'            => auth()->id(),
            'extracted_text'         => null,
            'extracted_numbers_json' => null,
            'ai_summary'             => null,
            'processing_status'      => 'processing',
        ]);

        // 🔥 قراءة Excel واستخراج البيانات
        try {
            $fullPath = storage_path('app/public/' . $path);

            $data = Excel::toArray([], $fullPath);

            $text = '';
            $numbers = [];

            foreach ($data as $sheet) {
                foreach ($sheet as $row) {
                    foreach ($row as $cell) {
                        if (!is_null($cell)) {
                            $text .= $cell . " | ";

                            if (is_numeric($cell)) {
                                $numbers[] = $cell;
                            }
                        }
                    }
                    $text .= "\n";
                }
            }

            $document->update([
                'extracted_text'         => $text,
                'extracted_numbers_json' => $numbers,
                'processing_status'      => 'done',
            ]);

        } catch (\Throwable $e) {
            $document->update([
                'processing_status' => 'failed',
            ]);
        }

        AuditHelper::log(
            'create',
            'EmployeeDocument',
            $document->id,
            'تم رفع ملف للموظف: ' . $employee->name
        );

        return redirect()
            ->route('employees.show', $employee)
            ->with('success', 'تم رفع الملف ومعالجته بنجاح');
    }

    public function open(Employee $employee, EmployeeDocument $document)
    {
        $this->authorizeHR();

        if ($document->employee_id !== $employee->id) {
            abort(404);
        }

        if (!Storage::disk('public')->exists($document->file_path)) {
            return redirect()
                ->route('employees.show', $employee)
                ->with('error', 'الملف غير موجود.');
        }

        return response()->file(storage_path('app/public/' . $document->file_path));
    }

    public function download(Employee $employee, EmployeeDocument $document)
    {
        $this->authorizeHR();

        if ($document->employee_id !== $employee->id) {
            abort(404);
        }

        if (!Storage::disk('public')->exists($document->file_path)) {
            return redirect()
                ->route('employees.show', $employee)
                ->with('error', 'الملف غير موجود.');
        }

        return Storage::disk('public')->download(
            $document->file_path,
            $document->file_name ?? basename($document->file_path)
        );
    }

    public function destroy(Employee $employee, EmployeeDocument $document)
    {
        $this->authorizeHR();

        if ($document->employee_id !== $employee->id) {
            abort(404);
        }

        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        AuditHelper::log(
            'delete',
            'EmployeeDocument',
            $document->id,
            'تم حذف ملف من الموظف: ' . $employee->name
        );

        $document->delete();

        return redirect()
            ->route('employees.show', $employee)
            ->with('success', 'تم حذف الملف بنجاح');
    }
}