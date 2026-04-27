@extends('layouts.app')

@section('page_title', __('assets.show_title'))
@section('page_subtitle', __('assets.show_subtitle'))

@section('content')

<div class="page-card">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 class="page-title">{{ __('assets.show_title') }}</h1>
            <p style="color:#6b7280;">{{ __('assets.show_subtitle') }}</p>
        </div>

        <a href="{{ route('assets.index') }}" class="btn btn-secondary">
            {{ __('common.back') }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="page-card" style="margin-bottom:24px;">
        <div class="page-header">
            <h2 style="margin:0; font-size:22px;">{{ __('assets.section_info') }}</h2>
        </div>

        <div class="details-grid">

            <div class="detail-box">
                <strong>{{ __('assets.field_name') }}</strong>
                <div>{{ $asset->name }}</div>
            </div>

            <div class="detail-box">
                <strong>{{ __('assets.th_serial') }}</strong>
                <div>{{ $asset->serial_number }}</div>
            </div>

            <div class="detail-box">
                <strong>{{ __('assets.field_asset_type') }}</strong>
                <div>{{ in_array($asset->asset_type, ['vehicle', 'مركبة'], true) ? __('assets.asset_type_vehicle') : __('assets.asset_type_general') }}</div>
            </div>

            <div class="detail-box">
                <strong>{{ __('assets.th_quantity') }}</strong>
                <div>{{ $asset->quantity }}</div>
            </div>

            <div class="detail-box">
                <strong>{{ __('assets.th_status') }}</strong>
                <div>
                    @if($asset->status == 'available')
                        <span class="badge badge-green">{{ __('assets.status_available') }}</span>
                    @elseif($asset->status == 'assigned')
                        <span class="badge badge-blue">{{ __('assets.status_assigned') }}</span>
                    @elseif($asset->status == 'maintenance')
                        <span class="badge badge-orange">{{ __('assets.status_maintenance') }}</span>
                    @endif
                </div>
            </div>

            <div class="detail-box">
                <strong>{{ __('assets.th_purchase_date') }}</strong>
                <div>{{ $asset->purchase_date ?? '-' }}</div>
            </div>

            @if(in_array($asset->asset_type, ['vehicle', 'مركبة'], true))
                <div class="detail-box">
                    <strong>{{ __('assets.field_vehicle_type') }}</strong>
                    <div>{{ $asset->vehicle_type ?? '-' }}</div>
                </div>

                <div class="detail-box">
                    <strong>{{ __('assets.field_plate_number') }}</strong>
                    <div>{{ $asset->plate_number ?? '-' }}</div>
                </div>

                <div class="detail-box">
                    <strong>{{ __('assets.field_color') }}</strong>
                    <div>{{ $asset->color ?? '-' }}</div>
                </div>

                <div class="detail-box">
                    <strong>{{ __('assets.inspection_expiry_short') }}</strong>
                    <div>{{ $asset->inspection_expiry_date ?? '-' }}</div>
                </div>

                <div class="detail-box">
                    <strong>{{ __('assets.field_registration_number') }}</strong>
                    <div>{{ $asset->registration_number ?? '-' }}</div>
                </div>

                <div class="detail-box">
                    <strong>{{ __('assets.registration_expiry_short') }}</strong>
                    <div>{{ $asset->registration_expiry_date ?? '-' }}</div>
                </div>
            @endif

            <div class="detail-box">
                <strong>{{ __('assets.linked_purchases') }}</strong>
                <div>
                    @if($asset->purchase)
                        <span class="badge badge-blue">
                            {{ $asset->purchase->title }}
                        </span>
                    @else
                        <span class="badge badge-gray">{{ __('assets.not_linked') }}</span>
                    @endif
                </div>
            </div>

            <div class="detail-box detail-box-full">
                <strong>{{ __('assets.field_notes') }}</strong>
                <div>{{ $asset->notes ?? '-' }}</div>
            </div>

        </div>
    </div>

    <div class="page-card" style="margin-bottom:24px;">
        <div class="page-header">
            <h2 style="margin:0; font-size:22px;">تسليم لموظف</h2>
            <p style="color:#6b7280;">تسليم الأصل لموظف مع منع التكرار عند وجود عهدة نشطة</p>
        </div>

        @if($asset->currentActiveAssignment)
            <div class="detail-box" style="margin-bottom:12px;">
                <strong>العهدة الحالية</strong>
                <div style="margin-top:8px;">
                    <span class="badge badge-blue">
                        {{ optional($asset->currentActiveAssignment->employee)->name ?? '—' }}
                    </span>
                    <span style="margin-inline-start:8px;">
                        {{ optional($asset->currentActiveAssignment->assigned_at)->format('Y-m-d H:i') }}
                    </span>
                </div>
                <div class="actions-row" style="margin-top:10px;">
                    <form method="POST" action="{{ route('assets.assignments.return', $asset->currentActiveAssignment) }}">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-sm">إرجاع الأصل</button>
                    </form>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('assets.assign', $asset) }}">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label>الموظف</label>
                    <select name="employee_id" required>
                        <option value="">-- اختر الموظف --</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ (string) old('employee_id') === (string) $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }}{{ $employee->employee_number ? ' - '.$employee->employee_number : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>تاريخ التسليم</label>
                    <input type="datetime-local" name="assigned_at" value="{{ old('assigned_at', now()->format('Y-m-d\TH:i')) }}">
                </div>

                <div class="form-group form-group-full">
                    <label>{{ __('assets.field_notes') }}</label>
                    <textarea name="notes">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="actions-row" style="margin-top:10px;">
                <button type="submit" class="btn btn-primary" {{ $asset->currentActiveAssignment ? 'disabled' : '' }}>تسليم الأصل</button>
            </div>
        </form>
    </div>

    <div class="page-card">
        <div class="page-header">
            <h2 style="margin:0; font-size:22px;">{{ __('assets.section_assignments') }}</h2>
            <p style="color:#6b7280;">{{ __('assets.section_assignments_sub') }}</p>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('assets.th_number') }}</th>
                        <th>{{ __('assets.th_employee') }}</th>
                        <th>{{ __('assets.th_start_date') }}</th>
                        <th>{{ __('assets.th_end_date') }}</th>
                        <th>{{ __('assets.th_status') }}</th>
                        <th>{{ __('assets.field_notes') }}</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($asset->assetAssignments as $assignment)
                        <tr>
                            <td>{{ $assignment->id }}</td>

                            <td>
                                {{ optional($assignment->employee)->name ?? '-' }}
                            </td>

                            <td>{{ optional($assignment->assigned_at)->format('Y-m-d H:i') ?? '-' }}</td>

                            <td>{{ optional($assignment->returned_at)->format('Y-m-d H:i') ?? '-' }}</td>

                            <td>
                                @if($assignment->status === 'assigned' && $assignment->returned_at === null)
                                    <span class="badge badge-green">{{ __('assets.assignment_active') }}</span>
                                @else
                                    <span class="badge badge-gray">{{ __('assets.assignment_ended') }}</span>
                                @endif
                            </td>

                            <td>{{ $assignment->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-row">
                                {{ __('assets.assignments_empty') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

    @if(isset($legacyAssignments) && $legacyAssignments->isNotEmpty())
        <div class="page-card" style="margin-top:16px;">
            <div class="page-header">
                <h2 style="margin:0; font-size:18px;">سجل العهدة القديم</h2>
                <p style="color:#6b7280;">بيانات محفوظة بالنظام القديم لمرجع فقط</p>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('assets.th_number') }}</th>
                            <th>{{ __('assets.th_employee') }}</th>
                            <th>{{ __('assets.th_start_date') }}</th>
                            <th>{{ __('assets.th_end_date') }}</th>
                            <th>{{ __('assets.th_status') }}</th>
                            <th>{{ __('assets.field_notes') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($legacyAssignments as $legacy)
                            <tr>
                                <td>{{ $legacy->id }}</td>
                                <td>{{ optional($legacy->employee)->name ?? '-' }}</td>
                                <td>{{ $legacy->start_date }}</td>
                                <td>{{ $legacy->end_date ?? '-' }}</td>
                                <td>
                                    @if($legacy->status == 'active')
                                        <span class="badge badge-green">{{ __('assets.assignment_active') }}</span>
                                    @else
                                        <span class="badge badge-gray">{{ __('assets.assignment_ended') }}</span>
                                    @endif
                                </td>
                                <td>{{ $legacy->notes ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>

@endsection
