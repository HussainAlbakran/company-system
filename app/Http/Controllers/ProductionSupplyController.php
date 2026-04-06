<?php

namespace App\Http\Controllers;

use App\Models\ProductionOrder;
use App\Models\ProductionSupply;
use App\Services\ProductionOrderCalculatorService;
use Illuminate\Http\Request;

class ProductionSupplyController extends Controller
{
    public function index()
    {
        $supplies = ProductionSupply::with('order')->latest()->get();

        return response()->json($supplies);
    }

    public function store(Request $request, ProductionOrderCalculatorService $calculator)
    {
        $validated = $request->validate([
            'production_order_id' => 'required|exists:production_orders,id',
            'supply_date' => 'required|date',
            'quantity' => 'required|numeric|min:0.01',
            'receiver_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $order = ProductionOrder::findOrFail($validated['production_order_id']);

        $futureSupplied = (float) $order->supplied_quantity + (float) $validated['quantity'];

        if ($futureSupplied > (float) $order->produced_quantity) {
            return response()->json([
                'message' => 'لا يمكن توريد كمية أكبر من الكمية المنتجة',
            ], 422);
        }

        $supply = ProductionSupply::create($validated);

        $calculator->recalculate($order);

        return response()->json([
            'message' => 'تم تسجيل التوريد بنجاح',
            'data' => $supply->load('order'),
        ], 201);
    }

    public function show($id)
    {
        $supply = ProductionSupply::with('order')->findOrFail($id);

        return response()->json($supply);
    }

    public function update(Request $request, $id, ProductionOrderCalculatorService $calculator)
    {
        $supply = ProductionSupply::findOrFail($id);

        $validated = $request->validate([
            'supply_date' => 'sometimes|date',
            'quantity' => 'sometimes|numeric|min:0.01',
            'receiver_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $order = $supply->order;

        $newQuantity = array_key_exists('quantity', $validated)
            ? (float) $validated['quantity']
            : (float) $supply->quantity;

        $otherSuppliesSum = (float) $order->supplies()
            ->where('id', '!=', $supply->id)
            ->sum('quantity');

        if (($otherSuppliesSum + $newQuantity) > (float) $order->produced_quantity) {
            return response()->json([
                'message' => 'لا يمكن أن يصبح إجمالي التوريد أكبر من الكمية المنتجة',
            ], 422);
        }

        $supply->update($validated);

        $calculator->recalculate($order);

        return response()->json([
            'message' => 'تم تحديث التوريد بنجاح',
            'data' => $supply->fresh()->load('order'),
        ]);
    }

    public function destroy($id, ProductionOrderCalculatorService $calculator)
    {
        $supply = ProductionSupply::findOrFail($id);
        $order = $supply->order;

        $supply->delete();

        $calculator->recalculate($order);

        return response()->json([
            'message' => 'تم حذف التوريد بنجاح',
        ]);
    }
}