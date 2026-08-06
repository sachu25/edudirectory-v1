<x-app-layout>
    <x-slot name="header">
        Reports Center
    </x-slot>

    <style>
        .custom-report-tabs .nav-link {
            min-height: 44px;
            display: inline-flex;
            align-items: center;
            border-radius: 10px;
            transition: all 0.2s ease;
            white-space: normal;
            word-break: break-word;
        }
        @media (max-width: 991.98px) {
            .custom-report-tabs {
                width: 100% !important;
                flex-direction: column !important;
            }
            .custom-report-tabs .nav-item {
                width: 100% !important;
            }
            .custom-report-tabs .nav-link {
                width: 100% !important;
                justify-content: flex-start;
                padding: 10px 16px !important;
            }
        }
    </style>

    <!-- Top Report Category Navigation Pills -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center mb-4 pb-2 border-bottom gap-3">
        <ul class="nav nav-pills custom-report-tabs w-100 w-lg-auto" id="reportCategoryTabs" style="gap: 10px;">
            <li class="nav-item">
                <a href="{{ route('reports.index', array_merge(request()->query(), ['report_tab' => 'institution'])) }}" 
                   class="nav-link px-4 py-2 fw-semibold {{ $activeTab === 'institution' ? 'active' : '' }}">
                    <i class="fas fa-university me-2"></i> Institution Directory Reports
                </a>
            </li>
            @if(auth()->user()->hasPermission('reports.staff.view'))
            <li class="nav-item">
                <a href="{{ route('reports.index', array_merge(request()->query(), ['report_tab' => 'staff'])) }}" 
                   class="nav-link px-4 py-2 fw-semibold {{ $activeTab === 'staff' ? 'active' : '' }}">
                    <i class="fas fa-user-check me-2"></i> Staff Activity & Performance Reports
                </a>
            </li>
            @endif
        </ul>
        <div class="text-muted small d-none d-lg-block">
            <i class="fas fa-chart-line text-primary me-1"></i> Comprehensive CRM Analytics
        </div>
    </div>

    @if($activeTab === 'institution')
        <!-- ==================== TAB 1: INSTITUTION DIRECTORY REPORTS ==================== -->
        <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-filter text-primary me-2"></i> Filter Institutions</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('reports.index') }}" method="GET" id="filterForm">
                    <input type="hidden" name="report_tab" value="institution">
                    <div class="row g-3 align-items-end">
                        <div class="col-xl-3 col-lg-4 col-md-6 col-12">
                            <label for="university_id" class="form-label fw-semibold text-secondary small">Affiliated University</label>
                            <select name="university_id" id="university_id" class="form-select">
                                <option value="">All Universities</option>
                                @foreach($universities as $uni)
                                    <option value="{{ $uni->id }}" {{ request('university_id') == $uni->id ? 'selected' : '' }}>
                                        {{ $uni->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xl-2 col-lg-4 col-md-6 col-12">
                            <label for="type" class="form-label fw-semibold text-secondary small">Institution Type</label>
                            <select name="type" id="type" class="form-select">
                                <option value="">All Types</option>
                                <option value="Affiliated" {{ request('type') == 'Affiliated' ? 'selected' : '' }}>Affiliated</option>
                                <option value="Autonomous" {{ request('type') == 'Autonomous' ? 'selected' : '' }}>Autonomous</option>
                                <option value="Constituent" {{ request('type') == 'Constituent' ? 'selected' : '' }}>Constituent</option>
                                <option value="Deemed" {{ request('type') == 'Deemed' ? 'selected' : '' }}>Deemed</option>
                                <option value="Other" {{ request('type') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-xl-2 col-lg-4 col-md-6 col-12">
                            <label for="state" class="form-label fw-semibold text-secondary small">State</label>
                            <select name="state" id="state" class="form-select">
                                <option value="">All States</option>
                                @foreach($states as $st)
                                    <option value="{{ $st }}" {{ request('state') == $st ? 'selected' : '' }}>{{ $st }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xl-2 col-lg-4 col-md-6 col-12">
                            <label for="status" class="form-label fw-semibold text-secondary small">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">All</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-xl-2 col-lg-4 col-md-6 col-12">
                            <label for="fdp_client" class="form-label fw-semibold text-secondary small">FDP Client?</label>
                            <select name="fdp_client" id="fdp_client" class="form-select">
                                <option value="">All</option>
                                <option value="Yes" {{ request('fdp_client') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="No" {{ request('fdp_client') == 'No' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                        <div class="col-xl-1 col-lg-4 col-md-6 col-12">
                            <button type="submit" class="btn btn-primary w-100 py-2"><i class="fas fa-search me-1"></i> Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header bg-white py-3 border-0 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <h6 class="mb-0 fw-bold text-dark">Institution Directory Results <span class="badge bg-secondary ms-2">{{ $colleges->total() }} Records</span></h6>
                <div class="d-flex flex-wrap gap-2 w-100 w-md-auto">
                    @if(auth()->user()->hasPermission('reports.export'))
                    <a href="{{ route('reports.export.excel', request()->query()) }}" class="btn btn-sm btn-success px-3">
                        <i class="fas fa-file-excel me-1"></i> Excel
                    </a>
                    <a href="{{ route('reports.export.csv', request()->query()) }}" class="btn btn-sm btn-info text-white px-3">
                        <i class="fas fa-file-csv me-1"></i> CSV
                    </a>
                    <a href="{{ route('reports.export.pdf', request()->query()) }}" class="btn btn-sm btn-danger px-3" target="_blank">
                        <i class="fas fa-file-pdf me-1"></i> PDF
                    </a>
                    @endif
                    <button onclick="window.print()" class="btn btn-sm btn-secondary px-3">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-secondary" style="font-size: 0.85rem; text-uppercase;">
                            <tr>
                                <th class="ps-3 py-3">Code</th>
                                <th class="py-3">Institution Name</th>
                                <th class="py-3">Affiliated University</th>
                                <th class="py-3">Type</th>
                                <th class="py-3">FDP Client?</th>
                                <th class="py-3">Official Email</th>
                                <th class="py-3">Website</th>
                                <th class="py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($colleges as $college)
                            <tr>
                                <td class="ps-3 text-muted">{{ $college->code ?? 'N/A' }}</td>
                                <td class="fw-bold text-dark">{{ $college->name }}</td>
                                <td>{{ $college->university ? $college->university->name : 'N/A' }}</td>
                                <td><span class="badge bg-light text-dark border px-2 py-1">{{ $college->type }}</span></td>
                                <td>
                                    <span class="badge {{ $college->fdp_client == 'Yes' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $college->fdp_client ?? 'No' }}
                                    </span>
                                </td>
                                <td class="text-muted">{{ $college->official_email ?? '-' }}</td>
                                <td>
                                    @if($college->website)
                                        <a href="{{ str_starts_with($college->website, 'http') ? $college->website : 'http://' . $college->website }}" target="_blank" class="text-decoration-none">
                                            <i class="fas fa-external-link-alt me-1" style="font-size: 0.8rem;"></i>{{ $college->website }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $college->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                        {{ ucfirst($college->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">No records found matching criteria.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($colleges->hasPages())
            <div class="card-footer bg-white border-0 pt-3">
                {{ $colleges->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>

    @else
        <!-- ==================== TAB 2: STAFF ACTIVITY & PERFORMANCE REPORTS ==================== -->
        <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-filter text-primary me-2"></i> Filter Staff Activity Reports</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('reports.index') }}" method="GET" id="staffFilterForm">
                    <input type="hidden" name="report_tab" value="staff">
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-4 col-md-6 col-12">
                            <label for="staff_id" class="form-label fw-semibold text-secondary small">Staff Member</label>
                            <select name="staff_id" id="staff_id" class="form-select">
                                <option value="">All Staff Members</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('staff_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-6 col-6">
                            <label for="start_date" class="form-label fw-semibold text-secondary small">Start Date</label>
                            <input type="date" class="form-control" name="start_date" id="start_date" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-lg-3 col-md-6 col-6">
                            <label for="end_date" class="form-label fw-semibold text-secondary small">End Date</label>
                            <input type="date" class="form-control" name="end_date" id="end_date" value="{{ request('end_date') }}">
                        </div>
                        <div class="col-lg-2 col-md-6 col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1 py-2"><i class="fas fa-search me-1"></i> Apply Filter</button>
                            <a href="{{ route('reports.index', ['report_tab' => 'staff']) }}" class="btn btn-light border py-2"><i class="fas fa-undo"></i></a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Staff Performance Summary Section -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-header bg-white py-3 border-0 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <div>
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-users-cog text-primary me-2"></i> Staff Activity Performance Summary</h6>
                    <small class="text-muted">Aggregated counts of entity creations and interaction logs per staff member</small>
                </div>
                <div class="d-flex flex-wrap gap-2 w-100 w-md-auto">
                    @if(auth()->user()->hasPermission('reports.export'))
                    <a href="{{ route('reports.export.staff-excel', request()->query()) }}" class="btn btn-sm btn-success px-3">
                        <i class="fas fa-file-excel me-1"></i> Export Excel
                    </a>
                    <a href="{{ route('reports.export.staff-csv', request()->query()) }}" class="btn btn-sm btn-info text-white px-3">
                        <i class="fas fa-file-csv me-1"></i> Export CSV
                    </a>
                    <a href="{{ route('reports.export.staff-pdf', request()->query()) }}" class="btn btn-sm btn-danger px-3" target="_blank">
                        <i class="fas fa-file-pdf me-1"></i> Export PDF
                    </a>
                    @endif
                    <button onclick="window.print()" class="btn btn-sm btn-secondary px-3">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-secondary" style="font-size: 0.82rem; text-uppercase; letter-spacing: 0.5px;">
                            <tr>
                                <th class="ps-3 py-3">Staff Member</th>
                                <th class="py-3 text-center">Colleges Added</th>
                                <th class="py-3 text-center">Contacts Added</th>
                                <th class="py-3 text-center">Corporate Clients</th>
                                <th class="py-3 text-center">Academic Logs</th>
                                <th class="py-3 text-center">Non-Academic Logs</th>
                                <th class="py-3 text-center fw-bold text-dark">Total Interactions</th>
                                <th class="py-3 text-center">Pending Follow-ups</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($staffData as $row)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-bold text-dark">{{ $row->staff_name }}</div>
                                        <div class="text-muted small">{{ $row->staff_email }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary-subtle text-primary border px-3 py-2 font-mono" style="font-size: 0.85rem;">
                                            {{ $row->colleges_added }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info-subtle text-info border px-3 py-2 font-mono" style="font-size: 0.85rem;">
                                            {{ $row->contacts_added }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary-subtle text-dark border px-3 py-2 font-mono" style="font-size: 0.85rem;">
                                            {{ $row->clients_added }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border px-3 py-2 font-mono" style="font-size: 0.85rem;">
                                            {{ $row->academic_interactions }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border px-3 py-2 font-mono" style="font-size: 0.85rem;">
                                            {{ $row->non_academic_interactions }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success text-white px-3 py-2 font-mono" style="font-size: 0.9rem;">
                                            {{ $row->total_interactions }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning text-dark px-3 py-2 font-mono" style="font-size: 0.85rem;">
                                            {{ $row->pending_followups }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">No staff activity data found for selected period.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Detailed Staff Touchpoint Audit Feed -->
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-history text-secondary me-2"></i> Detailed Staff Touchpoint Audit Feed</h6>
                <small class="text-muted">Recent interaction logs created by staff members</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-secondary" style="font-size: 0.8rem; text-uppercase;">
                            <tr>
                                <th class="ps-3 py-3">Date & Time</th>
                                <th class="py-3">Staff Member</th>
                                <th class="py-3">Category</th>
                                <th class="py-3">Institution / Client Name</th>
                                <th class="py-3">Contact Mode</th>
                                <th class="py-3">Status</th>
                                <th class="py-3">Remarks / Response</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($detailedLogs as $log)
                                <tr>
                                    <td class="ps-3 text-muted small">
                                        {{ \Carbon\Carbon::parse($log->contact_date)->format('d M Y, h:i A') }}
                                    </td>
                                    <td class="fw-semibold text-dark">{{ $log->staff_name }}</td>
                                    <td>
                                        <span class="badge {{ $log->type === 'Academic' ? 'bg-primary-subtle text-primary' : 'bg-secondary-subtle text-dark' }} border px-2 py-1">
                                            {{ $log->type }}
                                        </span>
                                    </td>
                                    <td class="fw-bold text-dark">{{ $log->target_name }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border px-2 py-1">
                                            {{ $log->contact_mode }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">
                                            {{ $log->status }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">
                                        {{ Str::limit($log->remarks ?? 'N/A', 70) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No detailed touchpoints logged yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .card, .card * {
                visibility: visible;
            }
            .card {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                border: none;
                box-shadow: none;
            }
            .card-header .btn, .card-header a, #reportCategoryTabs, #staffFilterForm, #filterForm {
                display: none !important;
            }
        }
    </style>
</x-app-layout>
