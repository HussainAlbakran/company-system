<?php

namespace App\Http\Controllers;

use App\Models\ProductionOrder;
use Illuminate\Http\Request;

class ProductionOrderController extends Controller
{
    public function index()
    {
        $perPage = (int) request()->integer('per_page', 20);
        $perPage = max(1, min($perPage, 100));
        $orders = ProductionOrder::latest()->paginate($perPage);

        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_number' => 'required|string|max:255|unique:production_orders,order_number',
            'product_name' => 'required|string|max:255',
            'planned_quantity' => 'required|numeric|min:0',
            'production_start_date' => 'nullable|date',
            'expected_end_date' => 'nullable|date',
            'daily_target' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $order = ProductionOrder::create([
            'order_number' => $validated['order_number'],
            'product_name' => $validated['product_name'],
            'planned_quantity' => $validated['planned_quantity'],
            'produced_quantity' => 0,
            'supplied_quantity' => 0,
            'production_start_date' => $validated['production_start_date'] ?? null,
            'expected_end_date' => $validated['expected_end_date'] ?? null,
            'daily_target' => $validated['daily_target'] ?? null,
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json([
            'message' => 'تم إنشاء أمر الإنتاج بنجاح',
            'data' => $order,
        ], 201);
    }

    public function show($id)
    {
        $order = ProductionOrder::with(['entries', 'supplies'])->findOrFail($id);

        return response()->json($order);
    }

    public function update(Request $request, $id)
    {
        $order = ProductionOrder::findOrFail($id);

        $validated = $request->validate([
            'product_name' => 'sometimes|string|max:255',
            'planned_quantity' => 'sometimes|numeric|min:0',
            'production_start_date' => 'nullable|date',
            'expected_end_date' => 'nullable|date',
            'daily_target' => 'nullable|numeric|min:0',
            'status' => 'sometimes|in:pending,in_progress,paused,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $order->update($validated);

        return response()->json([
            'message' => 'تم تحديث أمر الإنتاج بنجاح',
            'data' => $order->fresh(),
        ]);
    }

    public function destroy($id)
    {
        $order = ProductionOrder::findOrFail($id);
        $order->delete();

        return response()->json([
            'message' => 'تم حذف أمر الإنتاج بنجاح',
        ]);
    }
}