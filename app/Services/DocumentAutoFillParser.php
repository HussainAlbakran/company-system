<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;

class DocumentAutoFillParser
{
    public function parse(string $path, string $extension, ?string $formKey = null): array
    {
        $extension = strtolower($extension);
        $formKey = $formKey ? strtolower($formKey) : null;

        if ($extension === 'pdf') {
            return $this->extractFromText($this->extractPdfText($path), $formKey);
        }

        if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            return $this->extractFromText($this->extractImageText($path), $formKey);
        }

        return $this->extractFromSpreadsheet($path, $formKey);
    }

    private function extractPdfText(string $path): string
    {
        $raw = (string) file_get_contents($path);
        $text = preg_replace('/[^\x20-\x7E\x{0600}-\x{06FF}\r\n\t]/u', ' ', $raw) ?? '';
        $text = preg_replace('/\s+/', ' ', $text) ?? '';

        return trim($text);
    }

    private function extractFromText(string $text, ?string $formKey = null): array
    {
        $text = trim($text);
        if ($text === '') {
            return [];
        }

        $rules = $this->textRulesByForm()[$formKey ?? ''] ?? $this->textRulesByForm()['default'];
        $result = [];

        foreach ($rules as $field => $aliases) {
            if (in_array($field, ['email', 'phone', 'amount', 'date', 'start_date', 'end_date', 'passport_expiry'], true)) {
                $value = $this->extractByType($text, $field, $aliases);
            } else {
                $value = $this->extractLabeledValue($text, $aliases);
            }

            if ($value !== null && $value !== '') {
                $result[$field] = $value;
            }
        }

        $result['email'] = $result['email'] ?? ($this->extractByType($text, 'email', []) ?: null);
        $result['phone'] = $result['phone'] ?? ($this->extractByType($text, 'phone', []) ?: null);

        return array_filter($result, fn ($value) => $value !== null && $value !== '');
    }

    private function extractFromSpreadsheet(string $path, ?string $formKey = null): array
    {
        $spreadsheet = IOFactory::load($path);
        $rows = $spreadsheet->getSheet(0)->toArray(null, true, true, false);

        if (empty($rows)) {
            return [];
        }

        $headers = array_map(fn ($value) => $this->normalizeHeader((string) $value), $rows[0]);
        $firstDataRow = $rows[1] ?? [];

        $targetMap = $this->sheetAliasesByForm()[$formKey ?? ''] ?? $this->sheetAliasesByForm()['default'];

        $result = [];
        foreach ($targetMap as $target => $aliases) {
            $index = $this->findHeaderIndex($headers, $aliases);
            if ($index === null || ! array_key_exists($index, $firstDataRow)) {
                continue;
            }

            $value = trim((string) $firstDataRow[$index]);
            if ($value === '') {
                continue;
            }

            $result[$target] = in_array($target, ['amount', 'quantity', 'salary', 'project_value', 'expenses', 'total_cost', 'daily_target'], true)
                ? (float) str_replace(',', '', $value)
                : $value;
        }

        return $result;
    }

    private function extractImageText(string $path): string
    {
        if (! $this->hasTesseract()) {
            throw new \RuntimeException('Tesseract OCR is not available.');
        }

        $command = [
            'tesseract',
            $path,
            'stdout',
            '-l',
            'eng+ara',
            '--psm',
            '6',
        ];

        [$exitCode, $stdout] = $this->runCommand($command);
        if ($exitCode !== 0) {
            throw new \RuntimeException('Failed to OCR image document.');
        }

        $text = preg_replace('/\s+/', ' ', (string) $stdout) ?? '';
        return trim($text);
    }

    private function hasTesseract(): bool
    {
        $probe = strtoupper(substr(PHP_OS_FAMILY, 0, 3)) === 'WIN'
            ? 'where tesseract'
            : 'command -v tesseract';

        $output = shell_exec($probe . ' 2>&1');
        return is_string($output) && trim($output) !== '';
    }

    private function runCommand(array $command): array
    {
        $escaped = implode(' ', array_map('escapeshellarg', $command));

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($escaped, $descriptors, $pipes);
        if (! is_resource($process)) {
            return [1, ''];
        }

        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]) ?: '';
        fclose($pipes[1]);
        $stderr = stream_get_contents($pipes[2]) ?: '';
        fclose($pipes[2]);

        $exitCode = proc_close($process);
        if ($exitCode !== 0 && $stderr !== '') {
            return [$exitCode, $stderr];
        }

        return [$exitCode, $stdout];
    }

    private function normalizeHeader(string $value): string
    {
        $value = mb_strtolower(trim($value));
        return str_replace([' ', '-', '.'], '_', $value);
    }

    private function extractByType(string $text, string $type, array $aliases): string|float|null
    {
        if ($type === 'email') {
            if (preg_match('/([A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,})/iu', $text, $m)) {
                return trim($m[1]);
            }
            return null;
        }

        if ($type === 'phone') {
            if (preg_match('/(?:' . $this->aliasesPattern($aliases) . ')?\s*[:\-]?\s*([+\d][\d\-\s]{6,20})/iu', $text, $m)) {
                return trim($m[1]);
            }
            return null;
        }

        if ($type === 'amount') {
            if (preg_match('/(?:' . $this->aliasesPattern($aliases) . ')?\s*[:\-]?\s*([\d,]+(?:\.\d+)?)/iu', $text, $m)) {
                return (float) str_replace(',', '', $m[1]);
            }
            return null;
        }

        if (in_array($type, ['date', 'start_date', 'end_date', 'passport_expiry'], true)) {
            if (preg_match('/(?:' . $this->aliasesPattern($aliases) . ')?\s*[:\-]?\s*(\d{4}[\/\-]\d{1,2}[\/\-]\d{1,2}|\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4})/iu', $text, $m)) {
                return trim($m[1]);
            }
            return null;
        }

        return null;
    }

    private function extractLabeledValue(string $text, array $aliases): ?string
    {
        $aliasPattern = $this->aliasesPattern($aliases);
        if ($aliasPattern === '') {
            return null;
        }

        if (preg_match('/(?:' . $aliasPattern . ')\s*[:\-]?\s*([^\r\n,;|]{2,140})/iu', $text, $m)) {
            return trim($m[1]);
        }

        return null;
    }

    private function aliasesPattern(array $aliases): string
    {
        $escaped = array_map(fn ($alias) => preg_quote((string) $alias, '/'), $aliases);
        return implode('|', array_filter($escaped));
    }

    private function textRulesByForm(): array
    {
        return [
            'employees' => [
                'name' => ['name', 'employee name', 'full name', 'الاسم', 'اسم الموظف'],
                'email' => ['email', 'mail', 'البريد', 'البريد الالكتروني'],
                'phone' => ['phone', 'mobile', 'tel', 'الجوال', 'الهاتف', 'رقم الجوال'],
                'date' => ['hire date', 'employment date', 'تاريخ التوظيف', 'تاريخ التعيين'],
                'salary' => ['salary', 'basic salary', 'الراتب', 'الراتب الاساسي'],
                'passport_number' => ['passport', 'passport no', 'passport number', 'رقم الجواز', 'الجواز'],
                'iqama_number' => ['iqama', 'residency', 'iqama no', 'رقم الإقامة', 'الاقامة', 'residence no'],
                'passport_expiry' => ['passport expiry', 'passport expiry date', 'تاريخ انتهاء الجواز', 'انتهاء الجواز'],
            ],
            'contracts' => [
                'client_name' => ['client', 'client name', 'customer', 'اسم العميل', 'العميل'],
                'project_name' => ['project', 'project name', 'اسم المشروع', 'المشروع'],
                'date' => ['contract date', 'date', 'تاريخ العقد', 'التاريخ'],
                'project_value' => ['project value', 'contract value', 'value', 'قيمة المشروع', 'قيمة العقد'],
                'main_contractor' => ['main contractor', 'contractor', 'المقاول الرئيسي', 'المقاول'],
                'contract_number' => ['contract number', 'contract no', 'رقم العقد'],
            ],
            'purchases' => [
                'vendor' => ['vendor', 'supplier', 'المورد'],
                'invoice_number' => ['invoice number', 'invoice no', 'رقم الفاتورة', 'فاتورة'],
                'date' => ['invoice date', 'purchase date', 'date', 'تاريخ الفاتورة', 'تاريخ الشراء'],
                'total_cost' => ['total cost', 'total amount', 'amount', 'التكلفة الإجمالية', 'إجمالي التكلفة'],
                'quantity' => ['quantity', 'qty', 'الكمية'],
                'category' => ['category', 'classification', 'التصنيف', 'الفئة'],
                'name' => ['item name', 'product name', 'اسم الصنف', 'اسم المنتج'],
            ],
            'projects' => [
                'name' => ['project name', 'name', 'اسم المشروع'],
                'description' => ['description', 'scope', 'الوصف'],
                'start_date' => ['start date', 'project start', 'تاريخ البداية', 'بدء المشروع'],
                'end_date' => ['end date', 'project end', 'تاريخ النهاية', 'نهاية المشروع'],
                'project_value' => ['project value', 'budget', 'قيمة المشروع', 'الميزانية'],
                'expenses' => ['expenses', 'costs', 'المصاريف', 'التكاليف'],
                'notes' => ['notes', 'remarks', 'ملاحظات'],
            ],
            'factory' => [
                'order_number' => ['order number', 'order no', 'production order', 'رقم أمر الإنتاج', 'رقم الامر'],
                'product_name' => ['product name', 'product', 'اسم المنتج', 'المنتج'],
                'quantity' => ['planned quantity', 'quantity', 'qty', 'الكمية المطلوبة', 'الكمية'],
                'start_date' => ['production start date', 'start date', 'تاريخ بداية الإنتاج', 'تاريخ البداية'],
                'end_date' => ['expected end date', 'end date', 'تاريخ النهاية المتوقع', 'تاريخ النهاية'],
                'daily_target' => ['daily target', 'daily goal', 'الهدف اليومي'],
                'notes' => ['notes', 'remarks', 'ملاحظات'],
            ],
            'warehouse' => [
                'name' => ['item name', 'name', 'اسم المادة', 'الاسم'],
                'quantity' => ['quantity', 'qty', 'الكمية'],
                'unit' => ['unit', 'uom', 'الوحدة'],
                'notes' => ['notes', 'remarks', 'ملاحظات'],
            ],
            'default' => [
                'name' => ['name', 'الاسم'],
                'email' => ['email', 'البريد'],
                'phone' => ['phone', 'الجوال', 'الهاتف'],
                'amount' => ['amount', 'المبلغ'],
                'date' => ['date', 'التاريخ'],
            ],
        ];
    }

    private function sheetAliasesByForm(): array
    {
        return [
            'employees' => [
                'name' => ['name', 'full_name', 'employee_name', 'الاسم', 'اسم_الموظف'],
                'email' => ['email', 'mail', 'البريد', 'البريد_الالكتروني'],
                'phone' => ['phone', 'mobile', 'tel', 'الجوال', 'الهاتف'],
                'date' => ['hire_date', 'employment_date', 'تاريخ_التوظيف', 'تاريخ_التعيين'],
                'salary' => ['salary', 'basic_salary', 'الراتب', 'الراتب_الاساسي'],
                'passport_number' => ['passport_number', 'passport_no', 'رقم_الجواز'],
                'iqama_number' => ['iqama_number', 'residency_number', 'رقم_الإقامة', 'رقم_الاقامة'],
                'passport_expiry' => ['passport_expiry', 'passport_expiry_date', 'تاريخ_انتهاء_الجواز'],
            ],
            'contracts' => [
                'client_name' => ['client_name', 'client', 'customer', 'اسم_العميل', 'العميل'],
                'project_name' => ['project_name', 'project', 'اسم_المشروع', 'المشروع'],
                'date' => ['contract_date', 'date', 'تاريخ_العقد', 'التاريخ'],
                'project_value' => ['project_value', 'contract_value', 'value', 'قيمة_المشروع', 'قيمة_العقد'],
                'main_contractor' => ['main_contractor', 'contractor', 'المقاول_الرئيسي', 'المقاول'],
                'contract_number' => ['contract_number', 'contract_no', 'رقم_العقد'],
            ],
            'purchases' => [
                'vendor' => ['vendor', 'supplier', 'المورد'],
                'invoice_number' => ['invoice_number', 'invoice_no', 'رقم_الفاتورة'],
                'date' => ['purchase_date', 'invoice_date', 'date', 'تاريخ_الشراء', 'تاريخ_الفاتورة'],
                'total_cost' => ['total_cost', 'total_amount', 'amount', 'التكلفة_الإجمالية', 'إجمالي_التكلفة'],
                'quantity' => ['quantity', 'qty', 'الكمية'],
                'category' => ['category', 'classification', 'التصنيف'],
                'name' => ['name', 'item_name', 'product_name', 'اسم_الصنف', 'اسم_المنتج'],
            ],
            'projects' => [
                'name' => ['project_name', 'name', 'اسم_المشروع'],
                'description' => ['description', 'scope', 'الوصف'],
                'start_date' => ['start_date', 'project_start', 'تاريخ_البداية'],
                'end_date' => ['end_date', 'project_end', 'تاريخ_النهاية'],
                'project_value' => ['project_value', 'budget', 'قيمة_المشروع', 'الميزانية'],
                'expenses' => ['expenses', 'costs', 'المصاريف', 'التكاليف'],
                'notes' => ['notes', 'remarks', 'ملاحظات'],
            ],
            'factory' => [
                'order_number' => ['order_number', 'order_no', 'production_order', 'رقم_الأمر'],
                'product_name' => ['product_name', 'product', 'اسم_المنتج'],
                'quantity' => ['planned_quantity', 'quantity', 'qty', 'الكمية_المطلوبة'],
                'start_date' => ['production_start_date', 'start_date', 'تاريخ_البداية'],
                'end_date' => ['expected_end_date', 'end_date', 'تاريخ_النهاية'],
                'daily_target' => ['daily_target', 'daily_goal', 'الهدف_اليومي'],
                'notes' => ['notes', 'remarks', 'ملاحظات'],
            ],
            'warehouse' => [
                'name' => ['name', 'item_name', 'اسم_المادة', 'الاسم'],
                'quantity' => ['quantity', 'qty', 'الكمية'],
                'unit' => ['unit', 'uom', 'الوحدة'],
                'notes' => ['notes', 'remarks', 'ملاحظات'],
            ],
            'default' => [
                'name' => ['name', 'الاسم'],
                'email' => ['email', 'البريد'],
                'phone' => ['phone', 'الجوال'],
                'amount' => ['amount', 'المبلغ'],
                'date' => ['date', 'التاريخ'],
            ],
        ];
    }

    private function findHeaderIndex(array $headers, array $aliases): ?int
    {
        foreach ($headers as $index => $header) {
            foreach ($aliases as $alias) {
                if ($header === $this->normalizeHeader($alias)) {
                    return (int) $index;
                }
            }
        }

        return null;
    }
}
