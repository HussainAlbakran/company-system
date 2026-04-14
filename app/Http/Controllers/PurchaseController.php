<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Purchase;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | General Purchases
    |--------------------------------------------------------------------------
    */

    public function generalIndex(Request $request)
    {
        $query = Purchase::with(['creator'])
            ->whereIn('type', ['asset_purchase', 'general_maintenance'])
            ->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $purchases = $query->paginate(15)->withQueryString();

        $filteredTotalsQuery = Purchase::whereIn('type', ['asset_purchase', 'general_maintenance']);

        if ($request->filled('type')) {
            $filteredTotalsQuery->where('type', $request->type);
        }

        $totalAssetPurchaseCost = (float) (clone $filteredTotalsQuery)
            ->where('type', 'asset_purchase')
            ->sum('cost');

        $totalGeneralMaintenanceCost = (float) (clone $filteredTotalsQuery)
            ->where('type', 'general_maintenance')
            ->sum('cost');

        $totalGeneralPurchasesCost = $totalAssetPurchaseCost + $totalGeneralMaintenanceCost;

        return view('general-purchases.index', compact(
            'purchases',
            'totalAssetPurchaseCost',
            'totalGeneralMaintenanceCost',
            'totalGeneralPurchasesCost'
        ));
    }

    public function generalCreate()
    {
        return view('general-purchases.create');
    }

    public function generalStore(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:asset_purchase,general_maintenance',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'nullable|integer|min:1',
            'cost' => 'required|numeric|min:0',
            'vendor' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $purchase = Purchase::create([
            'project_id' => null,
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'quantity' => $validated['quantity'] ?? 1,
            'cost' => $validated['cost'],
            'vendor' => $validated['vendor'] ?? null,
            'purchase_date' => $validated['purchase_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'created_by' => Auth::id(),
        ]);

        if ($purchase->type === 'asset_purchase') {
            Asset::create([
                'purchase_id' => $purchase->id,
                'name' => $purchase->title,
                'quantity' => $purchase->quantity ?? 1,
                'serial_number' => $this->generateAssetSerialNumber(),
                'purchase_date' => $purchase->purchase_date,
                'notes' => $purchase->notes,
                'status' => 'available',
            ]);
        }

        return redirect()
            ->route('general-purchases.index')
            ->with('success', 'تم حفظ المشتريات العامة بنجاح');
    }

    public function generalEdit($id)
    {
        $purchase = Purchase::whereIn('type', ['asset_purchase', 'general_maintenance'])
            ->findOrFail($id);

        $asset = Asset::where('purchase_id', $purchase->id)->first();

        return view('general-purchases.edit', compact('purchase', 'asset'));
    }

    public function generalUpdate(Request $request, $id)
    {
        $purchase = Purchase::whereIn('type', ['asset_purchase', 'general_maintenance'])
            ->findOrFail($id);

        $oldType = $purchase->type;

        $validated = $request->validate([
            'type' => 'required|in:asset_purchase,general_maintenance',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'nullable|integer|min:1',
            'cost' => 'required|numeric|min:0',
            'vendor' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $purchase->update([
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'quantity' => $validated['quantity'] ?? 1,
            'cost' => $validated['cost'],
            'vendor' => $validated['vendor'] ?? null,
            'purchase_date' => $validated['purchase_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        $asset = Asset::where('purchase_id', $purchase->id)->first();

        if ($purchase->type === 'asset_purchase') {
            if ($asset) {
                $asset->update([
                    'name' => $purchase->title,
                    'quantity' => $purchase->quantity ?? 1,
                    'purchase_date' => $purchase->purchase_date,
                    'notes' => $purchase->notes,
                ]);
            } else {
                Asset::create([
                    'purchase_id' => $purchase->id,
                    'name' => $purchase->title,
                    'quantity' => $purchase->quantity ?? 1,
                    'serial_number' => $this->generateAssetSerialNumber(),
                    'purchase_date' => $purchase->purchase_date,
                    'notes' => $purchase->notes,
                    'status' => 'available',
                ]);
            }
        }

        if ($oldType === 'asset_purchase' && $purchase->type === 'general_maintenance' && $asset) {
            $asset->delete();
        }

        return redirect()
            ->route('general-purchases.index')
            ->with('success', 'تم تحديث المشتريات العامة بنجاح');
    }

    public function generalDestroy($id)
    {
        $purchase = Purchase::whereIn('type', ['asset_purchase', 'general_maintenance'])
            ->findOrFail($id);

        Asset::where('purchase_id', $purchase->id)->delete();

        $purchase->delete();

        return redirect()
            ->route('general-purchases.index')
            ->with('success', 'تم حذف العملية بنجاح');
    }

    /*
    |--------------------------------------------------------------------------
    | Contract Purchases
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $query = Purchase::with(['project', 'creator'])
            ->where('type', 'contract_purchase')
            ->latest();

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $purchases = $query->paginate(15)->withQueryString();

        $projects = Project::where('current_stage', 'production_installation')
            ->latest()
            ->get();

        $totalsQuery = Purchase::where('type', 'contract_purchase');

        if ($request->filled('project_id')) {
            $totalsQuery->where('project_id', $request->project_id);
        }

        $totalContractPurchasesCost = (float) $totalsQuery->sum('cost');

        return view('purchases.index', compact(
            'purchases',
            'projects',
            'totalContractPurchasesCost'
        ));
    }

    public function create()
    {
        $projects = Project::where('current_stage', 'production_installation')
            ->latest()
            ->get();

        return view('purchases.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'nullable|integer|min:1',
            'cost' => 'required|numeric|min:0',
            'vendor' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        Purchase::create([
            'project_id' => $validated['project_id'],
            'type' => 'contract_purchase',
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'quantity' => $validated['quantity'] ?? 1,
            'cost' => $validated['cost'],
            'vendor' => $validated['vendor'] ?? null,
            'purchase_date' => $validated['purchase_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('purchases.index')
            ->with('success', 'تم حفظ مشتريات العقود بنجاح');
    }

    public function show($id)
    {
        $purchase = Purchase::with(['project', 'creator'])
            ->where('type', 'contract_purchase')
            ->findOrFail($id);

        return view('purchases.show', compact('purchase'));
    }

    public function edit($id)
    {
        $purchase = Purchase::where('type', 'contract_purchase')
            ->findOrFail($id);

        $projects = Project::where('current_stage', 'production_installation')
            ->latest()
            ->get();

        return view('purchases.edit', compact('purchase', 'projects'));
    }

    public function update(Request $request, $id)
    {
        $purchase = Purchase::where('type', 'contract_purchase')
            ->findOrFail($id);

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'nullable|integer|min:1',
            'cost' => 'required|numeric|min:0',
            'vendor' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $purchase->update([
            'project_id' => $validated['project_id'],
            'type' => 'contract_purchase',
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'quantity' => $validated['quantity'] ?? 1,
            'cost' => $validated['cost'],
            'vendor' => $validated['vendor'] ?? null,
            'purchase_date' => $validated['purchase_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('purchases.index')
            ->with('success', 'تم تحديث مشتريات العقود بنجاح');
    }

    public function destroy($id)
    {
        $purchase = Purchase::where('type', 'contract_purchase')
            ->findOrFail($id);

        $purchase->delete();

        return redirect()
            ->route('purchases.index')
            ->with('success', 'تم حذف العملية بنجاح');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    private function generateAssetSerialNumber(): string
    {
        do {
            $serial = 'AST-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        } while (Asset::where('serial_number', $serial)->exists());

        return $serial;
    }
}