<?php

namespace App\Http\Controllers;

use App\Helpers\AuditHelper;
use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\Employee;
use App\Models\EmployeeAsset; // 🔥 مهم
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AssetController extends Controller
{
    private function authorizeHR(): void
    {
        if (!auth()->check() || !auth()->user()->canManageAssets()) {
            abort(403, 'غير مصرح لك');
        }
    }

    public function index(Request $request)
    {
        $this->authorizeHR();

        $assetsQuery = Asset::with(['purchase']);

        if ($request->filled('status')) {
            $assetsQuery->where('status', $request->status);
        }

        $assets = $assetsQuery
            ->latest()
            ->paginate(15)
            ->withQueryString();

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
        $this->authorizeHR();

        return view('assets.create');
    }

    public function store(Request $request)
    {
        $this->authorizeHR();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'asset_type' => 'required|in:general,vehicle,مركبة',
            'quantity' => 'required|integer|min:1',
            'serial_number' => ['nullable', 'string', 'max:255', Rule::unique('assets', 'serial_number')],
            'purchase_date' => 'nullable|date',
            'status' => 'required|in:available,assigned,maintenance',
            'plate_number' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'vehicle_type' => 'nullable|string|max:255',
            'inspection_expiry_date' => 'nullable|date',
            'registration_number' => 'nullable|string|max:255',
            'registration_expiry_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $isVehicle = in_array($validated['asset_type'], ['vehicle', 'مركبة'], true);

        $serial = ! empty($validated['serial_number'] ?? null)
            ? $validated['serial_number']
            : $this->generateAssetSerialNumber();

        Asset::create([
            'purchase_id' => null,
            'name' => $validated['name'],
            'asset_type' => $isVehicle ? 'vehicle' : 'general',
            'quantity' => $validated['quantity'],
            'serial_number' => $serial,
            'plate_number' => $isVehicle ? ($validated['plate_number'] ?? null) : null,
            'color' => $isVehicle ? ($validated['color'] ?? null) : null,
            'vehicle_type' => $isVehicle ? ($validated['vehicle_type'] ?? null) : null,
            'inspection_expiry_date' => $isVehicle ? ($validated['inspection_expiry_date'] ?? null) : null,
            'registration_number' => $isVehicle ? ($validated['registration_number'] ?? null) : null,
            'registration_expiry_date' => $isVehicle ? ($validated['registration_expiry_date'] ?? null) : null,
            'purchase_date' => $validated['purchase_date'] ?? null,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('assets.index')
            ->with('success', __('assets.success_created'));
    }

    public function show(Asset $asset)
    {
        $this->authorizeHR();

        $asset->load([
            'purchase',
            'assetAssignments.employee',
            'currentActiveAssignment.employee',
        ]);

        // 🔥 الحل هنا: جلب العهدة بدل العلاقة القديمة
        $legacyAssignments = EmployeeAsset::with('employee')
            ->where('asset_name', $asset->name)
            ->orWhere('serial_number', $asset->serial_number)
            ->latest()
            ->get();

        $employees = Employee::orderBy('name')->get(['id', 'name', 'employee_number']);

        return view('assets.show', compact('asset', 'legacyAssignments', 'employees'));
    }

    public function assignToEmployee(Request $request, Asset $asset)
    {
        $this->authorizeHR();

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'assigned_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $hasActiveAssignment = $asset->assetAssignments()
            ->where('status', 'assigned')
            ->whereNull('returned_at')
            ->exists();

        if ($hasActiveAssignment) {
            return back()->with('error', 'هذا الأصل بعهدة موظف حاليًا ولا يمكن تسليمه حتى يتم إرجاعه');
        }

        $assignment = AssetAssignment::create([
            'asset_id' => $asset->id,
            'employee_id' => (int) $validated['employee_id'],
            'assigned_by' => auth()->id(),
            'assigned_at' => $validated['assigned_at'] ?? now(),
            'status' => 'assigned',
            'notes' => $validated['notes'] ?? null,
        ]);

        $asset->update(['status' => 'assigned']);

        AuditHelper::log(
            'assign',
            'AssetAssignment',
            $assignment->id,
            'تم تسليم الأصل "' . $asset->name . '" إلى الموظف رقم ' . $assignment->employee_id
        );

        return back()->with('success', 'تم تسليم الأصل للموظف بنجاح');
    }

    public function returnAssignment(AssetAssignment $assignment)
    {
        $this->authorizeHR();

        if ($assignment->status === 'returned' || $assignment->returned_at !== null) {
            return back()->with('error', 'تم إرجاع هذا الأصل مسبقًا');
        }

        $assignment->update([
            'status' => 'returned',
            'returned_at' => now(),
        ]);

        $asset = $assignment->asset;

        $hasAnotherActive = $asset->assetAssignments()
            ->where('status', 'assigned')
            ->whereNull('returned_at')
            ->exists();

        if (!$hasAnotherActive) {
            $asset->update(['status' => 'available']);
        }

        AuditHelper::log(
            'return',
            'AssetAssignment',
            $assignment->id,
            'تم إرجاع الأصل "' . $asset->name . '" من الموظف رقم ' . $assignment->employee_id
        );

        return back()->with('success', 'تم إرجاع الأصل بنجاح');
    }

    public function assignedWithEmployees()
    {
        $this->authorizeHR();

        $activeAssignments = AssetAssignment::with(['asset', 'employee'])
            ->where('status', 'assigned')
            ->whereNull('returned_at')
            ->latest('assigned_at')
            ->paginate(20)
            ->withQueryString();

        return view('assets.with-employees', compact('activeAssignments'));
    }

    public function registrationExpiringSoon()
    {
        $this->authorizeHR();

        $today = Carbon::today();
        $thresholdDate = Carbon::today()->addDays(60);

        $registrationAssets = Asset::query()
            ->whereIn('asset_type', ['vehicle', 'مركبة'])
            ->whereNotNull('registration_expiry_date')
            ->whereBetween('registration_expiry_date', [$today, $thresholdDate])
            ->orderBy('registration_expiry_date')
            ->paginate(20, ['*'], 'reg_page')
            ->withQueryString();

        $registrationAssets->getCollection()->transform(function (Asset $asset) use ($today) {
            $expiry = Carbon::parse($asset->registration_expiry_date)->startOfDay();
            $asset->days_remaining = (int) $today->diffInDays($expiry, false);
            $asset->expiry_alert_kind = 'registration';

            return $asset;
        });

        $inspectionAssets = Asset::query()
            ->whereIn('asset_type', ['vehicle', 'مركبة'])
            ->whereNotNull('inspection_expiry_date')
            ->whereBetween('inspection_expiry_date', [$today, $thresholdDate])
            ->orderBy('inspection_expiry_date')
            ->paginate(20, ['*'], 'insp_page')
            ->withQueryString();

        $inspectionAssets->getCollection()->transform(function (Asset $asset) use ($today) {
            $expiry = Carbon::parse($asset->inspection_expiry_date)->startOfDay();
            $asset->days_remaining = (int) $today->diffInDays($expiry, false);
            $asset->expiry_alert_kind = 'inspection';

            return $asset;
        });

        return view('assets.registration-expiring-soon', compact('registrationAssets', 'inspectionAssets'));
    }

    private function generateAssetSerialNumber(): string
    {
        do {
            $serial = 'AST-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        } while (Asset::where('serial_number', $serial)->exists());

        return $serial;
    }
}