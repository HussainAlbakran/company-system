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
        $entries = ProductionEntry::with('order')->latest()->get();

        return response()->json($entries);
    }

    public function store(Request $request, ProductionOrderCalculatorService $calculator)
    {
        $validated = $request->validate([
            'production_order_id' => 'required|exists:production_orders,id',
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

        $entry = ProductionEntry::create([
            'production_order_id' => $validated['production_order_id'],
            'entry_date' => $validated['entry_date'],
            'quantity' => $validated['quantity'],
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'working_hours' => $workingHours,
            'employee_id' => $validated['employee_id'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        $order = ProductionOrder::findOrFail($validated['production_order_id']);
        $calculator->recalculate($order);

        return response()->json([
            'message' => 'تم تسجيل الإنتاج بنجاح',
            'data' => $entry->load('order'),
        ], 201);
    }

    public function show($id)
    {
        $entry = ProductionEntry::with('order')->findOrFail($id);

        return response()->json($entry);
    }

    public function update(Request $request, $id, ProductionOrderCalculatorService $calculator)
    {
        $entry = ProductionEntry::findOrFail($id);

        $validated = $request->validate([
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

        return response()->json([
            'message' => 'تم تحديث سجل الإنتاج بنجاح',
            'data' => $entry->fresh()->load('order'),
        ]);
    }

    public function destroy($id, ProductionOrderCalculatorService $calculator)
    {
        $entry = ProductionEntry::findOrFail($id);
        $order = $entry->order;

        $entry->delete();

        $calculator->recalculate($order);

        return response()->json([
            'message' => 'تم حذف سجل الإنتاج بنجاح',
        ]);
    }
}