<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\EmployeeAsset; // 🔥 مهم
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index()
    {
        $assets = Asset::with(['purchase'])
            ->latest()
            ->paginate(15);

        $totalAssetsCount = Asset::count();
        $availableAssetsCount = Asset::where('status', 'available')->count();
        $assignedAssetsCount = Asset::where('status', 'assigned')->count();
        $maintenanceAssetsCount = Asset::where('status', 'maintenance')->count();

        return view('assets.index', compact(
            'assets',
            'totalAssetsCount',
            'availableAssetsCount',
            'assignedAssetsCount',
            'maintenanceAssetsCount'
        ));
    }

    public function create()
    {
        return view('assets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'purchase_date' => 'nullable|date',
            'status' => 'required|in:available,assigned,maintenance',
            'notes' => 'nullable|string',
        ]);

        Asset::create([
            'purchase_id' => null,
            'name' => $validated['name'],
            'quantity' => $validated['quantity'],
            'serial_number' => $this->generateAssetSerialNumber(),
            'purchase_date' => $validated['purchase_date'] ?? null,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('assets.index')
            ->with('success', 'تمت إضافة الأصل بنجاح');
    }

    public function show(Asset $asset)
    {
        $asset->load(['purchase']);

        // 🔥 الحل هنا: جلب العهدة بدل العلاقة القديمة
        $assignments = EmployeeAsset::with('employee')
            ->where('asset_name', $asset->name)
            ->orWhere('serial_number', $asset->serial_number)
            ->latest()
            ->get();

        return view('assets.show', compact('asset', 'assignments'));
    }

    private function generateAssetSerialNumber(): string
    {
        do {
            $serial = 'AST-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        } while (Asset::where('serial_number', $serial)->exists());

        return $serial;
    }
}