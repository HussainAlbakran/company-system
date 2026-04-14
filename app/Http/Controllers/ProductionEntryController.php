<?php

namespace App\Http\Controllers;

use App\Models\ProductionEntry;
use App\Models\ProductionOrder;
use App\Services\ProductionOrderCalculatorService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProductionEntryController extends Controller
{
    public function index()
    {
        return redirect()->route('factory.index');
    }

    public function store(Request $request, ProductionOrderCalculatorService $calculator)
    {
        $validated = $request->validate([
            'production_order_id' => 'required|exists:production_orders,id',
            'project_id' => 'nullable|exists:projects,id',
            'entry_date' => 'required|date',
            'quantity' => 'required|numeric|min:0.01',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'employee_id' => 'nullable|integer',
            'notes' => 'nullable|string',
        ]);

        $workingHours = null;

        if (!empty($validated['start_time']) && !empty($validated['end_time'])) {
            $start = Carbon::createFromFormat('H:i', $validated['start_time']);
            $end = Carbon::createFromFormat('H:i', $validated['end_time']);
            $workingHours = round($start->diffInMinutes($end) / 60, 2);
        }

        $order = ProductionOrder::findOrFail($validated['production_order_id']);

        ProductionEntry::create([
            'production_order_id' => $validated['production_order_id'],
            'project_id' => $validated['project_id'] ?? null,
            'entry_date' => $validated['entry_date'],
            'quantity' => $validated['quantity'],
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'working_hours' => $workingHours,
            'employee_id' => $validated['employee_id'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        $calculator->recalculate($order);

        return redirect()
            ->route('production-orders.show', $order->id)
            ->with('success', 'تم تسجيل الإنتاج بنجاح');
    }

    public function show($id)
    {
        $entry = ProductionEntry::with(['order', 'project'])->findOrFail($id);

        return redirect()
            ->route('production-orders.show', $entry->production_order_id);
    }

    public function update(Request $request, $id, ProductionOrderCalculatorService $calculator)
    {
        $entry = ProductionEntry::findOrFail($id);

        $validated = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'entry_date' => 'sometimes|date',
            'quantity' => 'sometimes|numeric|min:0.01',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'employee_id' => 'nullable|integer',
            'notes' => 'nullable|string',
        ]);

        $startTime = array_key_exists('start_time', $validated) ? $validated['start_time'] : $entry->start_time;
        $endTime = array_key_exists('end_time', $validated) ? $validated['end_time'] : $entry->end_time;

        $workingHours = $entry->working_hours;

        if ($startTime && $endTime) {
            $start = Carbon::createFromFormat('H:i', $startTime);
            $end = Carbon::createFromFormat('H:i', $endTime);
            $workingHours = round($start->diffInMinutes($end) / 60, 2);
        }

        $validated['working_hours'] = $workingHours;

        $entry->update($validated);

        $calculator->recalculate($entry->order);

        return redirect()
            ->route('production-orders.show', $entry->production_order_id)
            ->with('success', 'تم تحديث سجل الإنتاج بنجاح');
    }

    public function destroy($id, ProductionOrderCalculatorService $calculator)
    {
        $entry = ProductionEntry::findOrFail($id);
        $order = $entry->order;

        $entry->delete();

        $calculator->recalculate($order);

        return redirect()
            ->route('production-orders.show', $order->id)
            ->with('success', 'تم حذف سجل الإنتاج بنجاح');
    }
}