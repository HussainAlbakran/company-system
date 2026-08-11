@extends('layouts.app')

@section('page_title', __('project_reports.show_title'))
@section('page_subtitle', $project->name)

@php
    $paymentTypeLabel = static function (?string $type): string {
        return match ($type) {
            'full' => __('contracts.payment_type_full_row'),
            'installment' => __('contracts.payment_type_installment_row'),
            'government' => __('contracts.payment_government'),
            default => $type ?: '-',
        };
    };

    $reportSections = [
        ['title' => __('project_reports.section_weekly'), 'rows' => $weeklyReports ?? collect()],
        ['title' => __('project_reports.section_project'), 'rows' => $projectReports],
        ['title' => __('project_reports.section_financial'), 'rows' => $financialReports],
        ['title' => __('project_reports.section_accident'), 'rows' => $accidentReports],
        ['title' => __('project_reports.section_delay'), 'rows' => $delayReports],
    ];
@endphp

@section('content')
<div class="dashboard-stack">
    <section class="dashboard-panel">
        <div class="panel-head" style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
            <div>
                <h2 class="panel-title">{{ __('project_reports.show_title') }} — {{ $project->name }}</h2>
                <p class="panel-subtitle">{{ __('project_reports.show_subtitle') }}</p>
            </div>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                @if(auth()->user()->canViewProjectReportsBoard())
                    <a href="{{ route('project-reports.board') }}" class="btn btn-secondary btn-sm">
                        {{ __('project_reports.board_btn') }}
                    </a>
                    <a href="{{ route('project-reports.archive') }}" class="btn btn-secondary btn-sm">
                        {{ __('project_reports.archive_btn') }}
                    </a>
                @endif
                @if(auth()->user()->canSubmitProjectReports() && ! $project->isCompleted())
                    <a href="{{ route('project-reports.create', ['project_id' => $project->id]) }}" class="btn btn-primary btn-sm">
                        {{ __('project_reports.register_btn') }}
                    </a>
                @endif
            </div>
        </div>

        <div class="actions-row" style="margin-top:14px; flex-wrap:wrap;">
            <a href="#project-purchases" class="btn btn-warning btn-sm">
                {{ __('project_reports.btn_all_purchases') }}
                <span class="badge badge-gray" style="margin-inline-start:6px;">{{ ($purchases->count() ?? 0) + ($materialRequests->count() ?? 0) }}</span>
            </a>
            <a href="#project-maintenance" class="btn btn-secondary btn-sm">
                {{ __('project_reports.btn_maintenance') }}
                <span class="badge badge-gray" style="margin-inline-start:6px;">{{ $maintenanceItems->count() ?? 0 }}</span>
            </a>
            <a href="#project-payments" class="btn btn-primary btn-sm">
                {{ __('project_reports.btn_payments') }}
            </a>
            <a href="#project-designs" class="btn btn-success btn-sm">
                {{ __('project_reports.btn_designs') }}
            </a>
            <a href="#project-contract-file" class="btn btn-secondary btn-sm">
                {{ __('project_reports.btn_contract_file') }}
            </a>
        </div>

        @if(session('success'))
            <div class="alert-success" style="margin-top:12px;">{{ session('success') }}</div>
        @endif

        {{-- جدول السداد --}}
        <div id="project-payments" style="margin-top:22px;">
            <h3 style="margin:0 0 10px; font-size:18px; color:#000;">{{ __('project_reports.section_payments') }}</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('project_reports.th_number') }}</th>
                            <th>{{ __('contracts.th_payment_type') }}</th>
                            <th>{{ __('project_reports.th_amount') }}</th>
                            <th>{{ __('project_reports.th_payment_date') }}</th>
                            <th>{{ __('project_reports.th_notes') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td style="color:#000; font-weight:700;">{{ $loop->iteration }}</td>
                                <td style="color:#000;">{{ $paymentTypeLabel($payment->payment_type) }}</td>
                                <td style="color:#000; font-weight:600;">{{ number_format((float) $payment->amount, 2) }}</td>
                                <td style="color:#000;">{{ optional($payment->payment_date)->format('Y-m-d') ?? '-' }}</td>
                                <td style="color:#000;">{{ $payment->notes ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-row">{{ __('project_reports.empty_payments') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ملفات التصاميم --}}
        <div id="project-designs" style="margin-top:22px;">
            <h3 style="margin:0 0 10px; font-size:18px; color:#000;">{{ __('project_reports.section_designs') }}</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('project_reports.th_number') }}</th>
                            <th>{{ __('project_reports.th_design_label') }}</th>
                            <th>{{ __('project_reports.th_file') }}</th>
                            <th>{{ __('project_reports.th_date') }}</th>
                            <th>{{ __('project_reports.th_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($designFiles as $file)
                            <tr>
                                <td style="color:#000; font-weight:700;">{{ $loop->iteration }}</td>
                                <td style="color:#000; font-weight:600;">{{ $file['label'] }}</td>
                                <td style="color:#000; word-break:break-all;">{{ $file['name'] }}</td>
                                <td style="color:#000;">{{ optional($file['date'])->format('Y-m-d H:i') ?? '-' }}</td>
                                <td>
                                    <a href="{{ $file['url'] }}" target="_blank" rel="noopener" class="btn btn-success btn-sm">
                                        {{ __('project_reports.open_file') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-row">{{ __('project_reports.empty_designs') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ملف العقد --}}
        <div id="project-contract-file" style="margin-top:22px;">
            <h3 style="margin:0 0 10px; font-size:18px; color:#000;">{{ __('project_reports.section_contract_file') }}</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('project_reports.th_number') }}</th>
                            <th>{{ __('contracts.th_contract_no') }}</th>
                            <th>{{ __('project_reports.th_file') }}</th>
                            <th>{{ __('project_reports.th_date') }}</th>
                            <th>{{ __('project_reports.th_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(! empty($contractFile))
                            <tr>
                                <td style="color:#000; font-weight:700;">1</td>
                                <td style="color:#000; font-weight:600;">{{ $contractFile['contract_no'] }}</td>
                                <td style="color:#000; word-break:break-all;">{{ $contractFile['name'] }}</td>
                                <td style="color:#000;">{{ optional($contractFile['date'])->format('Y-m-d H:i') ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('project-reports.contract-file', $project) }}"
                                       target="_blank"
                                       rel="noopener"
                                       class="btn btn-success btn-sm">
                                        {{ __('project_reports.open_file') }}
                                    </a>
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="5" class="empty-row">{{ __('project_reports.empty_contract_file') }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        {{-- جميع طلبات المشتريات --}}
        <div id="project-purchases" style="margin-top:22px;">
            <h3 style="margin:0 0 10px; font-size:18px; color:#000;">{{ __('project_reports.section_purchases') }}</h3>
            <div style="margin:0 0 14px; font-size:14px; color:#111827;">
                <strong>{{ __('project_reports.total_all_purchases') }}:</strong>
                {{ number_format((float) ($purchases->sum('cost') + ($maintenanceItems->sum('cost') ?? 0)), 2) }}
            </div>

            <h4 style="margin:0 0 8px; font-size:15px; color:#111827;">{{ __('project_reports.subsection_material_requests') }}</h4>
            <div class="table-wrap" style="margin-bottom:16px;">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('project_reports.th_number') }}</th>
                            <th>{{ __('project_reports.th_requester') }}</th>
                            <th>{{ __('project_reports.th_status') }}</th>
                            <th>{{ __('project_reports.th_items_count') }}</th>
                            <th>{{ __('project_reports.th_date') }}</th>
                            <th>{{ __('project_reports.th_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($materialRequests as $request)
                            <tr>
                                <td style="color:#000; font-weight:700;">{{ $loop->iteration }}</td>
                                <td style="color:#000;">{{ $request->creator->name ?? '-' }}</td>
                                <td style="color:#000;">{{ $request->status ?: '-' }}</td>
                                <td style="color:#000;">{{ $request->items->count() }}</td>
                                <td style="color:#000;">{{ optional($request->created_at)->format('Y-m-d') ?? '-' }}</td>
                                <td>
                                    @if($request->attachment_path)
                                        <a href="{{ route('project-reports.material-attachment', [$project, $request]) }}" class="btn btn-success btn-sm">
                                            {{ __('project_reports.download') }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty-row">{{ __('project_reports.empty_material_requests') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <h4 style="margin:0 0 8px; font-size:15px; color:#111827;">{{ __('project_reports.subsection_purchases') }}</h4>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('project_reports.th_number') }}</th>
                            <th>{{ __('project_reports.th_title') }}</th>
                            <th>{{ __('project_reports.th_type') }}</th>
                            <th>{{ __('project_reports.th_vendor') }}</th>
                            <th>{{ __('project_reports.th_amount') }}</th>
                            <th>{{ __('project_reports.th_payment_date') }}</th>
                            <th>{{ __('project_reports.th_requester') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases as $purchase)
                            <tr>
                                <td style="color:#000; font-weight:700;">{{ $loop->iteration }}</td>
                                <td style="color:#000; font-weight:600;">{{ $purchase->title ?: '-' }}</td>
                                <td style="color:#000;">{{ $purchase->type_label }}</td>
                                <td style="color:#000;">{{ $purchase->vendor ?: '-' }}</td>
                                <td style="color:#000; font-weight:600;">{{ number_format((float) $purchase->cost, 2) }}</td>
                                <td style="color:#000;">{{ optional($purchase->purchase_date)->format('Y-m-d') ?? '-' }}</td>
                                <td style="color:#000;">{{ $purchase->creator->name ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="empty-row">{{ __('project_reports.empty_purchases') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- الصيانة --}}
        <div id="project-maintenance" style="margin-top:22px;">
            <h3 style="margin:0 0 10px; font-size:18px; color:#000;">{{ __('project_reports.section_maintenance') }}</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('project_reports.th_number') }}</th>
                            <th>{{ __('project_reports.th_title') }}</th>
                            <th>{{ __('project_reports.th_type') }}</th>
                            <th>{{ __('project_reports.th_vendor') }}</th>
                            <th>{{ __('project_reports.th_amount') }}</th>
                            <th>{{ __('project_reports.th_payment_date') }}</th>
                            <th>{{ __('project_reports.th_requester') }}</th>
                            <th>{{ __('project_reports.th_notes') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($maintenanceItems as $item)
                            <tr>
                                <td style="color:#000; font-weight:700;">{{ $loop->iteration }}</td>
                                <td style="color:#000; font-weight:600;">{{ $item->title ?: '-' }}</td>
                                <td style="color:#000;">{{ $item->type_label }}</td>
                                <td style="color:#000;">{{ $item->vendor ?: '-' }}</td>
                                <td style="color:#000; font-weight:600;">{{ number_format((float) $item->cost, 2) }}</td>
                                <td style="color:#000;">{{ optional($item->purchase_date)->format('Y-m-d') ?? '-' }}</td>
                                <td style="color:#000;">{{ $item->creator->name ?? '-' }}</td>
                                <td style="color:#000;">{{ $item->notes ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="empty-row">{{ __('project_reports.empty_maintenance') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- تقارير المشروع حسب النوع --}}
        @foreach($reportSections as $section)
            <div style="margin-top:22px;">
                <h3 style="margin:0 0 10px; font-size:18px; color:#000;">{{ $section['title'] }}</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>{{ __('project_reports.th_uploader') }}</th>
                                <th>{{ __('project_reports.th_date') }}</th>
                                <th>{{ __('project_reports.th_since') }}</th>
                                <th>{{ __('project_reports.th_file') }}</th>
                                <th>{{ __('project_reports.th_project') }}</th>
                                <th>{{ __('project_reports.th_actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($section['rows'] as $report)
                                <tr>
                                    <td style="color:#000; font-weight:600;">{{ $report->uploader->name ?? '-' }}</td>
                                    <td style="color:#000;">{{ optional($report->report_date)->format('Y-m-d') }}</td>
                                    <td style="color:#000;">
                                        {{ optional($report->created_at)->timezone(config('app.timezone'))->diffForHumans() }}
                                    </td>
                                    <td style="color:#000; word-break:break-all;">{{ $report->original_name }}</td>
                                    <td style="color:#000;">{{ $project->name }}</td>
                                    <td>
                                        <div class="actions-row">
                                            <a href="{{ route('project-reports.download', $report) }}" class="btn btn-success btn-sm">
                                                {{ __('project_reports.download') }}
                                            </a>
                                            @if(auth()->user()->canViewProjectReportsBoard())
                                                <form
                                                    action="{{ route('project-reports.destroy', $report) }}"
                                                    method="POST"
                                                    onsubmit="return confirm(@json(__('project_reports.confirm_delete')))"
                                                >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        {{ __('project_reports.delete') }}
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="empty-row">{{ __('project_reports.empty_section') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

        @if(auth()->user()->canViewProjectReportsBoard() && ! $project->isCompleted())
            <div id="project-complete" style="margin-top:28px; padding-top:18px; border-top:1px solid #e5e7eb;">
                <h3 style="margin:0 0 8px; font-size:18px; color:#000;">{{ __('project_reports.complete_title') }}</h3>
                <p style="margin:0 0 12px; color:#4b5563;">{{ __('project_reports.complete_hint') }}</p>
                <form action="{{ route('project-reports.complete', $project) }}" method="POST" enctype="multipart/form-data"
                      onsubmit="return confirm(@json(__('project_reports.confirm_complete')))">
                    @csrf
                    <div class="form-grid" style="margin-bottom:12px;">
                        <div class="form-group" style="grid-column:1 / -1;">
                            <label for="completion_letter">{{ __('project_reports.field_completion_letter') }}</label>
                            <input
                                type="file"
                                name="completion_letter"
                                id="completion_letter"
                                required
                                accept=".pdf,.doc,.docx,.txt"
                            >
                            <small class="text-muted d-block mt-1">{{ __('project_reports.field_completion_letter_hint') }}</small>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success">
                        {{ __('project_reports.complete_btn') }}
                    </button>
                </form>
            </div>
        @elseif($project->isCompleted())
            <div style="margin-top:28px; padding-top:18px; border-top:1px solid #e5e7eb;">
                <span class="badge badge-green">{{ __('project_reports.status_completed') }}</span>
                @if($project->completed_at)
                    <span style="margin-inline-start:8px; color:#4b5563;">
                        {{ optional($project->completed_at)->format('Y-m-d') }}
                    </span>
                @endif
                @if(auth()->user()->canViewProjectReportsBoard())
                    <a href="{{ route('project-reports.archive', ['year' => optional($project->completed_at)->year ?? date('Y')]) }}"
                       class="btn btn-secondary btn-sm" style="margin-inline-start:8px;">
                        {{ __('project_reports.archive_btn') }}
                    </a>
                @endif
            </div>

            @if(auth()->user()->canViewProjectReportsBoard() && ! empty($project->completion_letter_path))
                <div style="margin-top:16px;">
                    <h3 style="margin:0 0 10px; font-size:18px; color:#000;">{{ __('project_reports.section_completion_letter') }}</h3>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>{{ __('project_reports.th_file') }}</th>
                                    <th>{{ __('project_reports.th_actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="color:#000; font-weight:600; word-break:break-all;">
                                        {{ basename($project->completion_letter_path) }}
                                    </td>
                                    <td>
                                        <a href="{{ route('project-reports.completion-letter', $project) }}"
                                           class="btn btn-secondary btn-sm">
                                            {{ __('project_reports.print_completion_letter') }}
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @endif
    </section>
</div>
@endsection
