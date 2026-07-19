<?php

namespace App\Http\Controllers;

use App\Helpers\AuditHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\AiChat;
use App\Models\Employee;
use App\Models\Project;
use App\Models\ProjectUpdate;
use App\Models\Department;
use App\Models\Factory;
use App\Models\ProductionOrder;
use App\Models\ProductionEntry;
use App\Models\ProductionSupply;
use App\Models\CashFlowEntry;
use App\Models\Purchase;
use App\Models\SalesContract;
use App\Models\User;
use Carbon\Carbon;

class AiController extends Controller
{
    protected const AI_CHATS_PER_PAGE = 20;
    protected const EMPLOYEES_CONTEXT_LIMIT = 150;
    protected const DEPARTMENTS_CONTEXT_LIMIT = 80;
    protected const PROJECTS_CONTEXT_LIMIT = 40;
    protected const PROJECT_UPDATES_PER_PROJECT = 3;
    protected const FACTORY_CONTEXT_LIMIT = 30;

    public function index()
    {
        $user = auth()->user();
        abort_unless($user && $user->canAccessAiAssistantNavigation(), 403);

        if (! $user->isSuperAdmin()) {
            AuditHelper::log(
                'ai_opened',
                'AiChat',
                null,
                'تم فتح صفحة المساعد الذكي',
                (int) $user->id
            );
        }

        $chats = AiChat::where('user_id', auth()->id())
            ->latest()
            ->paginate(self::AI_CHATS_PER_PAGE);

        return view('Ai.index', compact('chats'));
    }

    public function ask(Request $request)
    {
        abort_unless(auth()->user() && auth()->user()->canAccessAiAssistantNavigation(), 403);

        $request->validate([
            'question' => 'required|string|max:5000',
        ]);

        $user = auth()->user();
        $question = trim($request->question);

        if (! $user) {
            return back()->with('ai_answer', __('ai.forbidden'));
        }

        $context = $this->buildContextByRole($user);

        if (empty(trim($context))) {
            return back()->with('ai_answer', __('ai.no_allowed_data'));
        }

        $systemPrompt = $this->buildSystemPrompt($user);

        if (! $user->isSuperAdmin()) {
            $qPreview = mb_substr($question, 0, 2000);
            AuditHelper::log(
                'ai_request',
                'AiChat',
                null,
                'تم إرسال سؤال إلى الذكاء الاصطناعي: «'.$qPreview.'»',
                (int) $user->id
            );
        }

        try {
            $response = Http::withToken(env('OPENAI_API_KEY'))
                ->timeout(60)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $systemPrompt,
                        ],
                        [
                            'role' => 'user',
                            'content' => "بيانات النظام المسموح بها:\n\n" . $context . "\n\nالسؤال:\n" . $question,
                        ],
                    ],
                    'temperature' => 0.2,
                ]);

            if (! $response->successful()) {
                $answer = __('ai.service_unavailable');
            } else {
                $answer = $response->json('choices.0.message.content') ?? __('ai.no_answer_found');
            }
        } catch (\Throwable $e) {
            $answer = __('ai.processing_error');
        }

        if (! $user->isSuperAdmin()) {
            $answerPreview = mb_substr($answer, 0, 2500);
            AuditHelper::log(
                'ai_response',
                'AiChat',
                null,
                'تم تسجيل رد من الذكاء الاصطناعي: «'.$answerPreview.'»',
                (int) $user->id
            );
        }

        AiChat::create([
            'user_id' => $user->id,
            'question' => $question,
            'answer' => $answer,
        ]);

        return redirect()->route('ai.page')->with('ai_answer', $answer);
    }

    public function clear()
    {
        abort_unless(auth()->user() && auth()->user()->canAccessAiAssistantNavigation(), 403);

        AiChat::where('user_id', auth()->id())->delete();

        return redirect()->route('ai.page')->with('success', __('ai.cleared_success'));
    }

    protected function buildSystemPrompt($user): string
    {
        $moduleLabels = [
            'employees' => 'الموظفين',
            'departments' => 'الأقسام',
            'assets' => 'الأصول',
            'leaves' => 'الإجازات',
            'contracts' => 'العقود',
            'engineering' => 'الهندسة والتصاميم',
            'projects' => 'المشاريع',
            'factory' => 'المصنع والإنتاج',
            'installation' => 'التركيبات',
            'purchases' => 'المشتريات',
            'warehouse' => 'المستودع',
            'cash_flow' => 'المدخول والصرف',
        ];

        $allowedAreas = [];
        foreach ($user->aiAllowedModules() as $module) {
            $allowedAreas[] = $moduleLabels[$module] ?? $module;
        }

        $allowedAreasText = implode('، ', array_unique($allowedAreas));

        return "أنت مساعد ذكي داخل نظام شركة.
أجب فقط من البيانات المعطاة لك.
لا تخترع معلومات غير موجودة.
إذا كان السؤال خارج الصلاحية أو لا توجد له بيانات في السياق، فقل بوضوح: غير مصرح لك بهذه المعلومات أو لا توجد بيانات كافية.
إذا طلب المستخدم تقريرًا، فاكتب تقريرًا منظمًا وواضحًا.
إذا طلب المستخدم ملخصًا، فاختصر بشكل تنفيذي.
إذا طلب المستخدم آخر التحديثات، فاعتمد على أحدث تحديثات المشاريع الموجودة في السياق.
إذا طلب المستخدم النص الأصلي من الملفات، فأظهر النص الموجود في extracted_text فقط.
إذا طلب المستخدم الأرقام، فاعتمد على extracted_numbers_json والبيانات الرقمية الموجودة في المشروع.
صلاحيات هذا المستخدم: {$allowedAreasText}.";
    }

    protected function buildContextByRole($user): string
    {
        $sections = [];
        $modules = $user->aiAllowedModules();

        if ($user->isAdminLike()) {
            $sections[] = $this->employeesContext();
            $sections[] = $this->departmentsContext();
            $sections[] = $this->projectsContext($user);
            $sections[] = $this->factoryContext();
            $sections[] = $this->salesContractsContext();
            $sections[] = $this->purchasesContext($user);
            $sections[] = $this->cashFlowContext();
        } else {
            if (in_array('employees', $modules, true)) {
                $sections[] = $this->employeesContext();
            }

            if (in_array('departments', $modules, true)) {
                $sections[] = $this->departmentsContext();
            }

            if (in_array('projects', $modules, true) || in_array('engineering', $modules, true) || in_array('contracts', $modules, true)) {
                $sections[] = $this->projectsContext($user);
            }

            if (in_array('factory', $modules, true) || in_array('installation', $modules, true)) {
                $sections[] = $this->factoryContext();
            }

            if (in_array('contracts', $modules, true) && ! $user->isAdminLike()) {
                $sections[] = $this->salesContractsContext();
            }

            if (in_array('purchases', $modules, true) && ! $user->isAdminLike()) {
                $sections[] = $this->purchasesContext($user);
            }

            if (in_array('cash_flow', $modules, true)) {
                $sections[] = $this->cashFlowContext();
            }
        }

        return implode("\n\n", array_filter($sections));
    }

    protected function salesContractsContext(): string
    {
        $contracts = SalesContract::with('payments')
            ->latest()
            ->limit(80)
            ->get();

        $text = "قسم عقود المبيعات:\n";

        foreach ($contracts as $contract) {
            $paid = (float) $contract->payments->sum('amount');
            $value = (float) ($contract->project_value ?? 0);
            $remaining = max(0, $value - $paid);

            $text .= "- رقم العقد: " . ($contract->contract_no ?? '-')
                . " | العميل: " . ($contract->client_name ?? '-')
                . " | المشروع: " . ($contract->project_name ?? '-')
                . " | قيمة العقد: {$value}"
                . " | المدفوع: {$paid}"
                . " | المتبقي: {$remaining}"
                . " | نوع الدفع: " . ($contract->payment_type ?? '-')
                . " | الحالة: " . ($contract->status ?? '-') . "\n";
        }

        return $text;
    }

    protected function purchasesContext(User $user): string
    {
        $query = Purchase::query()->latest();

        if ($user->isFinance()) {
            $query->whereIn('type', ['asset_purchase', 'general_maintenance', 'contract_purchase']);
        }

        $purchases = $query->limit(80)->get();

        $text = "قسم المشتريات:\n";

        foreach ($purchases as $purchase) {
            $text .= "- المعرف: " . ($purchase->id ?? '-')
                . " | النوع: " . ($purchase->type ?? '-')
                . " | التكلفة: " . ($purchase->cost ?? '-')
                . " | التاريخ: " . ($purchase->purchase_date ?? '-')
                . " | الوصف: " . ($purchase->description ?? '-') . "\n";
        }

        return $text;
    }

    protected function cashFlowContext(): string
    {
        $entries = CashFlowEntry::query()
            ->latest('entry_date')
            ->limit(120)
            ->get();

        $income = (float) $entries->where('type', CashFlowEntry::TYPE_INCOME)->sum('amount');
        $expense = (float) $entries->where('type', CashFlowEntry::TYPE_EXPENSE)->sum('amount');

        $text = "قسم المدخول والصرف:\n";
        $text .= "- إجمالي المدخول (من العينة): {$income}\n";
        $text .= "- إجمالي الصرف (من العينة): {$expense}\n";
        $text .= "- صافي العينة: " . ($income - $expense) . "\n";

        foreach ($entries->take(60) as $entry) {
            $text .= "- " . ($entry->type === CashFlowEntry::TYPE_INCOME ? 'مدخول' : 'صرف')
                . " | " . ($entry->title ?? '-')
                . " | التصنيف: " . ($entry->category ?? '-')
                . " | المبلغ: " . ($entry->amount ?? '-')
                . " | التاريخ: " . ($entry->entry_date ?? '-') . "\n";
        }

        return $text;
    }

    protected function employeesContext(): string
    {
        $employees = Employee::with('department')
            ->select([
                'id',
                'name',
                'employee_number',
                'job_title',
                'phone',
                'email',
                'status',
                'department_id',
                'residency_expiry_date',
            ])
            ->orderBy('name')
            ->limit(self::EMPLOYEES_CONTEXT_LIMIT)
            ->get();

        $today = Carbon::today();
        $text = "قسم الموظفين:\n";

        foreach ($employees as $employee) {
            $expiry = $employee->residency_expiry_date ?: '-';
            $daysRemainingText = '-';

            if (! empty($employee->residency_expiry_date)) {
                try {
                    $daysRemaining = $today->diffInDays(Carbon::parse($employee->residency_expiry_date), false);
                    $daysRemainingText = (string) $daysRemaining;
                } catch (\Throwable $e) {
                    $daysRemainingText = '-';
                }
            }

            $text .= "- الاسم: {$employee->name}"
                . " | الرقم الوظيفي: " . ($employee->employee_number ?? '-')
                . " | المسمى: " . ($employee->job_title ?? '-')
                . " | القسم: " . ($employee->department->name ?? '-')
                . " | البريد: " . ($employee->email ?? '-')
                . " | الجوال: " . ($employee->phone ?? '-')
                . " | الحالة: " . ($employee->status ?? '-')
                . " | انتهاء الإقامة: {$expiry}"
                . " | المتبقي بالأيام: {$daysRemainingText}\n";
        }

        return $text;
    }

    protected function departmentsContext(): string
    {
        $departments = Department::select(['id', 'name'])
            ->orderBy('name')
            ->limit(self::DEPARTMENTS_CONTEXT_LIMIT)
            ->get();

        $text = "قسم الأقسام:\n";

        foreach ($departments as $department) {
            $text .= "- القسم: {$department->name} | المعرف: {$department->id}\n";
        }

        return $text;
    }

    protected function projectsContext(User $user): string
    {
        $canFullFinancials = $user->canViewProjectFinancials();
        $canValueOnly = $user->canViewProjectValueOnly();

        $projects = Project::with(['department', 'responsibleEmployee'])
            ->select([
                'id',
                'department_id',
                'responsible_employee_id',
                'name',
                'status',
                'start_date',
                'end_date',
                'project_value',
                'expenses',
                'notes',
                'created_at',
            ])
            ->latest()
            ->limit(self::PROJECTS_CONTEXT_LIMIT)
            ->get();

        $projectIds = $projects->pluck('id')->all();

        $updates = ProjectUpdate::whereIn('project_id', $projectIds)
            ->select([
                'id',
                'project_id',
                'title',
                'description',
                'progress',
                'attachment',
                'processing_status',
                'extracted_text',
                'extracted_numbers_json',
                'ai_summary',
                'created_at',
            ])
            ->latest()
            ->limit(self::PROJECTS_CONTEXT_LIMIT * self::PROJECT_UPDATES_PER_PROJECT)
            ->get()
            ->groupBy('project_id');

        $today = Carbon::today();
        $text = "قسم المشاريع:\n";

        foreach ($projects as $project) {
            $daysRemainingText = '-';
            $delayStatus = 'غير محدد';

            if (! empty($project->end_date)) {
                try {
                    $daysRemaining = $today->diffInDays(Carbon::parse($project->end_date), false);
                    $daysRemainingText = (string) $daysRemaining;

                    if ($daysRemaining < 0) {
                        $delayStatus = 'متأخر';
                    } elseif ($daysRemaining <= 7) {
                        $delayStatus = 'ينتهي قريبًا';
                    } else {
                        $delayStatus = 'ضمن المدة';
                    }
                } catch (\Throwable $e) {
                    $daysRemainingText = '-';
                    $delayStatus = 'غير محدد';
                }
            }

            $text .= "====================================\n";
            $text .= "المشروع: {$project->name}\n";
            $text .= "القسم: " . ($project->department->name ?? '-') . "\n";
            $text .= "المسؤول: " . ($project->responsibleEmployee->name ?? '-') . "\n";
            $text .= "الحالة: " . ($project->status ?? '-') . "\n";
            $text .= "البداية: " . ($project->start_date ?? '-') . "\n";
            $text .= "النهاية: " . ($project->end_date ?? '-') . "\n";
            $text .= "المتبقي بالأيام: {$daysRemainingText}\n";
            $text .= "وضع المدة: {$delayStatus}\n";

            if ($canFullFinancials) {
                $remainingBudget = (float) ($project->project_value ?? 0) - (float) ($project->expenses ?? 0);
                $text .= "القيمة: " . ($project->project_value ?? 0) . "\n";
                $text .= "المصاريف: " . ($project->expenses ?? 0) . "\n";
                $text .= "المتبقي المالي: {$remainingBudget}\n";
            } elseif ($canValueOnly) {
                $text .= "القيمة: " . ($project->project_value ?? 0) . "\n";
            }

            $text .= "الملاحظات: " . ($project->notes ?? '-') . "\n";

            $projectUpdates = $updates->get($project->id, collect());

            if ($projectUpdates->count() > 0) {
                $text .= "آخر تحديثات المشروع:\n";

                foreach ($projectUpdates->take(self::PROJECT_UPDATES_PER_PROJECT) as $update) {
                    $text .= "- عنوان التحديث: " . ($update->title ?? '-') . "\n";
                    $text .= "  الوصف: " . ($update->description ?? '-') . "\n";
                    $text .= "  التقدم: " . ($update->progress ?? '-') . "%\n";
                    $text .= "  تاريخ التحديث: " . ($update->created_at ?? '-') . "\n";
                    $text .= "  حالة معالجة الملف: " . ($update->processing_status ?? '-') . "\n";

                    if (! empty($update->attachment)) {
                        $text .= "  يوجد مرفق: نعم\n";
                    } else {
                        $text .= "  يوجد مرفق: لا\n";
                    }

                    if (! empty($update->extracted_text)) {
                        $text .= "  النص المستخرج من المرفق:\n";
                        $text .= $this->limitText($update->extracted_text, 3000) . "\n";
                    }

                    if (! empty($update->extracted_numbers_json) && is_array($update->extracted_numbers_json)) {
                        $text .= "  الأرقام المستخرجة من المرفق: " . implode(', ', $update->extracted_numbers_json) . "\n";
                    }

                    if (! empty($update->ai_summary)) {
                        $text .= "  ملخص المرفق: " . $update->ai_summary . "\n";
                    }
                }
            } else {
                $text .= "آخر تحديثات المشروع: لا توجد تحديثات.\n";
            }

            $text .= "====================================\n\n";
        }

        return $text;
    }

    protected function factoryContext(): string
    {
        $factories = Factory::select(['id', 'name', 'created_at'])
            ->latest()
            ->take(self::FACTORY_CONTEXT_LIMIT)
            ->get();
        $orders = ProductionOrder::select(['id', 'product_name', 'status', 'planned_quantity'])
            ->latest()
            ->take(self::FACTORY_CONTEXT_LIMIT)
            ->get();
        $entries = ProductionEntry::select(['id', 'created_at', 'quantity', 'notes'])
            ->latest()
            ->take(self::FACTORY_CONTEXT_LIMIT)
            ->get();
        $supplies = ProductionSupply::select(['id', 'receiver_name', 'quantity', 'notes'])
            ->latest()
            ->take(self::FACTORY_CONTEXT_LIMIT)
            ->get();

        $text = "قسم المصنع والإنتاج:\n";

        if ($factories->count() > 0) {
            $text .= "أوامر المصنع:\n";
            foreach ($factories as $factory) {
                $text .= "- الطلب/السجل: " . ($factory->id ?? '-')
                    . " | الاسم: " . ($factory->name ?? '-')
                    . " | الحالة: " . ($factory->status ?? '-')
                    . " | التاريخ: " . ($factory->created_at ?? '-') . "\n";
            }
        }

        if ($orders->count() > 0) {
            $text .= "أوامر الإنتاج:\n";
            foreach ($orders as $order) {
                $text .= "- أمر إنتاج: " . ($order->id ?? '-')
                    . " | الاسم: " . ($order->product_name ?? '-')
                    . " | الحالة: " . ($order->status ?? '-')
                    . " | الكمية: " . ($order->planned_quantity ?? '-') . "\n";
            }
        }

        if ($entries->count() > 0) {
            $text .= "إدخالات الإنتاج:\n";
            foreach ($entries as $entry) {
                $text .= "- إدخال: " . ($entry->id ?? '-')
                    . " | التاريخ: " . ($entry->created_at ?? '-')
                    . " | الكمية: " . ($entry->quantity ?? '-')
                    . " | الملاحظات: " . ($entry->notes ?? '-') . "\n";
            }
        }

        if ($supplies->count() > 0) {
            $text .= "مستلزمات الإنتاج:\n";
            foreach ($supplies as $supply) {
                $text .= "- مستلزم: " . ($supply->id ?? '-')
                    . " | الاسم: " . ($supply->receiver_name ?? '-')
                    . " | الكمية: " . ($supply->quantity ?? '-')
                    . " | الملاحظات: " . ($supply->notes ?? '-') . "\n";
            }
        }

        return $text;
    }

    protected function limitText(?string $text, int $maxLength = 3000): string
    {
        if (empty($text)) {
            return '-';
        }

        $text = trim($text);

        if (mb_strlen($text) <= $maxLength) {
            return $text;
        }

        return mb_substr($text, 0, $maxLength) . ' ...';
    }
}