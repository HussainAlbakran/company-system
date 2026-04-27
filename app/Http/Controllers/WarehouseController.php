<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    // عرض الجدول
    public function show($section)
    {
        $items = DB::table('warehouse_items')
            ->where('section', $section)
            ->latest()
            ->get();

        return view('warehouse.show', [
            'sectionKey' => $section,
            'items' => $items,
        ]);
    }

    // صفحة الإدخال
    public function input($section)
    {
        return view('warehouse.input', [
            'sectionKey' => $section,
        ]);
    }

    // حفظ الجدول
    public function store(Request $request, $section)
    {
        $validated = $request->validate([
            'items' => 'nullable|array',
            'items.*.name' => 'nullable|string|max:255',
            'items.*.quantity' => 'nullable|string|max:255',
            'items.*.unit' => 'nullable|string|max:255',
            'items.*.notes' => 'nullable|string|max:1000',
        ]);

        $items = $validated['items'] ?? [];

        foreach ($items as $item) {
            $name = trim($item['name'] ?? '');
            $quantity = trim($item['quantity'] ?? '');
            $unit = trim($item['unit'] ?? '');
            $notes = trim($item['notes'] ?? '');

            if ($name === '' && $quantity === '' && $unit === '' && $notes === '') {
                continue;
            }

            DB::table('warehouse_items')->insert([
                'section' => $section,
                'name' => $name !== '' ? $name : null,
                'quantity' => $quantity !== '' ? $quantity : null,
                'unit' => $unit !== '' ? $unit : null,
                'notes' => $notes !== '' ? $notes : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()
            ->route('warehouse.section.show', $section)
            ->with('success', __('warehouse.flash_section_saved'));
    }

    // فتح صفحة التعديل
    public function edit($id)
    {
        $item = DB::table('warehouse_items')->find($id);

        if (!$item) {
            abort(404);
        }

        return view('warehouse.edit', [
            'item' => $item,
            'sectionKey' => $item->section,
        ]);
    }

    // حفظ التعديل
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'quantity' => 'nullable|string|max:255',
            'unit' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::table('warehouse_items')
            ->where('id', $id)
            ->update([
                'name' => $validated['name'],
                'quantity' => $validated['quantity'],
                'unit' => $validated['unit'],
                'notes' => $validated['notes'],
                'updated_at' => now(),
            ]);

        return back()->with('success', __('warehouse.flash_updated'));
    }

    // حذف عنصر
    public function destroy($id)
    {
        DB::table('warehouse_items')->where('id', $id)->delete();

        return back()->with('success', __('warehouse.flash_deleted'));
    }

}