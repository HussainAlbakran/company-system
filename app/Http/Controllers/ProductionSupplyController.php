<?php

namespace App\Http\Controllers;

use App\Models\ProductionOrder;
use App\Models\ProductionSupply;
use App\Services\ProductionOrderCalculatorService;
use Illuminate\Http\Request;

class ProductionSupplyController extends Controller
{
    private function wantsJson(Request $request): bool
    {
        return $request->expectsJson()
            || $request->wantsJson()
            || $request->ajax();
    }

    public function index()
    {
        $perPage = (int) request()->integer('per_page', 20);
        $perPage = max(1, min($perPage, 100));

        $supplies = ProductionSupply::with('order')->latest()->paginate($perPage);

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
            if ($this->wantsJson($request)) {
                return response()->json([
                    'message' => __('factory.error_supply_exceeds_produced'),
                ], 422);
            }

            return back()
                ->withInput()
                ->with('error', __('factory.error_supply_exceeds_produced'));
        }

        $supply = ProductionSupply::create($validated);

        $calculator->recalculate($order);

        if ($this->wantsJson($request)) {
            return response()->json([
                'message' => __('factory.flash_supply_saved'),
                'data' => $supply->load('order'),
            ], 201);
        }

        return redirect()
            ->route('production-orders.show', $order->id)
            ->with('success', __('factory.flash_supply_saved'));
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
            if ($this->wantsJson($request)) {
                return response()->json([
                    'message' => __('factory.error_total_supply_exceeds'),
                ], 422);
            }

            return back()
                ->withInput()
                ->with('error', __('factory.error_total_supply_exceeds'));
        }

        $supply->update($validated);

        $calculator->recalculate($order);

        if ($this->wantsJson($request)) {
            return response()->json([
                'message' => __('factory.flash_supply_updated'),
                'data' => $supply->fresh()->load('order'),
            ]);
        }

        return redirect()
            ->route('production-orders.show', $order->id)
            ->with('success', __('factory.flash_supply_updated'));
    }

    public function destroy(Request $request, $id, ProductionOrderCalculatorService $calculator)
    {
        $supply = ProductionSupply::findOrFail($id);
        $order = $supply->order;

        $supply->delete();

        $calculator->recalculate($order);

        if ($this->wantsJson($request)) {
            return response()->json([
                'message' => __('factory.flash_supply_deleted'),
            ]);
        }

        return redirect()
            ->route('production-orders.show', $order->id)
            ->with('success', __('factory.flash_supply_deleted'));
    }
}