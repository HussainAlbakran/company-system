<?php

namespace App\Http\Controllers;

use App\Helpers\AuditHelper;
use App\Models\Employee;
use App\Models\Factory;
use App\Models\ProductionEntry;
use App\Models\ProductionOrder;
use App\Models\ProductionSupply;
use Illuminate\Http\Request;

class FactoryManagerController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();

        $factory = Factory::where('manager_user_id', $user->id)->first();

        if (! $factory) {
            return redirect()->back()->with('error', 'لا يوجد مصنع مربوط بهذا المدير');
        }

        $employeesCount = Employee::where('factory_id', $factory->id)->count();
        $employees = Employee::where('factory_id', $factory->id)->latest()->take(10)->get();

        return view('factory-manager.dashboard', compact('factory', 'employeesCount', 'employees'));
    }

    public function storeEntry(Request $request, $order)
    {
        $productionOrder = ProductionOrder::findOrFail($order);

        $validated = $request->validate([
            'entry_date' => ['required', 'date'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'start_time' => ['nullable'],
            'end_time' => ['nullable'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $workingHours = null;

        if (!empty($validated['start_time']) && !empty($validated['end_time'])) {
            $start = strtotime($validated['start_time']);
            $end = strtotime($validated['end_time']);

            if ($start !== false && $end !== false && $end >= $start) {
                $workingHours = round(($end - $start) / 3600, 2);
            }
        }

        $entry = ProductionEntry::create([
            'production_order_id' => $productionOrder->id,
            'entry_date' => $validated['entry_date'],
            'quantity' => $validated['quantity'],
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'working_hours' => $workingHours,
            'employee_id' => $validated['employee_id'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        $productionOrder->produced_quantity = (float) ($productionOrder->produced_quantity ?? 0) + (float) $validated['quantity'];

        if ($productionOrder->produced_quantity >= $productionOrder->planned_quantity && (float) $productionOrder->planned_quantity > 0) {
            $productionOrder->status = 'completed';
        } elseif ($productionOrder->produced_quantity > 0) {
            $productionOrder->status = 'in_progress';
        } else {
            $productionOrder->status = 'pending';
        }

        $productionOrder->save();

        AuditHelper::log(
            'create',
            'ProductionEntry',
            $entry->id,
            'تم تسجيل إنتاج لأمر الإنتاج رقم: ' . ($productionOrder->order_number ?? $productionOrder->id)
        );

        return back()->with('success', 'تم تسجيل الإنتاج بنجاح');
    }

    public function storeSupply(Request $request, $order)
    {
        $productionOrder = ProductionOrder::findOrFail($order);

        $validated = $request->validate([
            'supply_date' => ['required', 'date'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'receiver_name' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $supply = ProductionSupply::create([
            'production_order_id' => $productionOrder->id,
            'supply_date' => $validated['supply_date'],
            'quantity' => $validated['quantity'],
            'receiver_name' => $validated['receiver_name'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        $productionOrder->supplied_quantity = (float) ($productionOrder->supplied_quantity ?? 0) + (float) $validated['quantity'];

        if ($productionOrder->supplied_quantity >= $productionOrder->planned_quantity && (float) $productionOrder->planned_quantity > 0) {
            $productionOrder->status = 'completed';
        } elseif ($productionOrder->supplied_quantity > 0 || $productionOrder->produced_quantity > 0) {
            $productionOrder->status = 'in_progress';
        } else {
            $productionOrder->status = 'pending';
        }

        $productionOrder->save();

        AuditHelper::log(
            'create',
            'ProductionSupply',
            $supply->id,
            'تم تسجيل توريد لأمر الإنتاج رقم: ' . ($productionOrder->order_number ?? $productionOrder->id)
        );

        return back()->with('success', 'تم تسجيل التوريد بنجاح');
    }
}