<?php

namespace App\Http\Controllers;

use App\Models\ProductionOrder;
use Illuminate\Http\Request;

class FactoryController extends Controller
{
    public function index()
    {
        $orders = ProductionOrder::latest()->get();
        return view('factory.index', compact('orders'));
    }

    public function create()
    {
        return view('factory.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_number' => ['required'],
            'product_name' => ['required'],
            'planned_quantity' => ['required', 'numeric'],
        ]);

        ProductionOrder::create([
            'order_number' => $validated['order_number'],
            'product_name' => $validated['product_name'],
            'planned_quantity' => $validated['planned_quantity'],
            'produced_quantity' => 0,
            'supplied_quantity' => 0,
            'status' => 'pending',
        ]);

        return redirect()->route('factory.index')->with('success', 'تم إنشاء أمر الإنتاج');
    }

    public function show($id)
    {
        $order = ProductionOrder::with(['entries', 'supplies'])->findOrFail($id);
        return view('factory.show', compact('order'));
    }

    // ✅ الحل هنا
    public function edit($id)
    {
        $order = ProductionOrder::findOrFail($id);
        return view('factory.edit', compact('order'));
    }

    // ✅ مهم
    public function update(Request $request, $id)
    {
        $order = ProductionOrder::findOrFail($id);

        $validated = $request->validate([
            'order_number' => ['required'],
            'product_name' => ['required'],
            'planned_quantity' => ['required', 'numeric'],
        ]);

        $order->update($validated);

        return redirect()->route('factory.index')->with('success', 'تم التحديث');
    }

    public function destroy($id)
    {
        $order = ProductionOrder::findOrFail($id);
        $order->delete();

        return redirect()->route('factory.index')->with('success', 'تم الحذف');
    }
}