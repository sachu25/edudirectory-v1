<x-app-layout>
    <x-slot name="header">
        Reports Module
    </x-slot>

    <div class="card mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold"><i class="fas fa-filter text-primary me-2"></i> Filter Reports</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('reports.index') }}" method="GET" id="filterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="university_id" class="form-label fw-semibold">University</label>
                        <select name="university_id" id="university_id" class="form-select">
                            <option value="">All Universities</option>
                            @foreach($universities as $university)
                                <option value="{{ $university->id }}" {{ request('university_id') == $university->id ? 'selected' : '' }}>
                                    {{ $university->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="type" class="form-label fw-semibold">College Type</label>
                        <select name="type" id="type" class="form-select">
                            <option value="">All Types</option>
                            <option value="Affiliated" {{ request('type') == 'Affiliated' ? 'selected' : '' }}>Affiliated</option>
                            <option value="Autonomous" {{ request('type') == 'Autonomous' ? 'selected' : '' }}>Autonomous</option>
                            <option value="Constituent" {{ request('type') == 'Constituent' ? 'selected' : '' }}>Constituent</option>
                            <option value="Deemed" {{ request('type') == 'Deemed' ? 'selected' : '' }}>Deemed</option>
                            <option value="Other" {{ request('type') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label fw-semibold">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i> Filter</button>
                        <a href="{{ route('reports.index') }}" class="btn btn-light border"><i class="fas fa-redo me-1"></i> Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">Report Results <span class="badge bg-secondary ms-2">{{ $colleges->total() }} Records</span></h6>
            <div>
                <a href="{{ route('reports.export.excel', request()->query()) }}" class="btn btn-sm btn-success me-1">
                    <i class="fas fa-file-excel"></i> Excel
                </a>
                <a href="{{ route('reports.export.csv', request()->query()) }}" class="btn btn-sm btn-info text-white me-1">
                    <i class="fas fa-file-csv"></i> CSV
                </a>
                <a href="{{ route('reports.export.pdf', request()->query()) }}" class="btn btn-sm btn-danger me-1" target="_blank">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
                <button onclick="window.print()" class="btn btn-sm btn-secondary">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Code</th>
                            <th>College Name</th>
                            <th>University</th>
                            <th>Type</th>
                            <th>Official Email</th>
                            <th>Website</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($colleges as $college)
                        <tr>
                            <td class="ps-3">{{ $college->code ?? 'N/A' }}</td>
                            <td class="fw-medium">{{ $college->name }}</td>
                            <td>{{ $college->university ? $college->university->short_name ?? $college->university->name : 'N/A' }}</td>
                            <td>{{ $college->type }}</td>
                            <td>{{ $college->official_email ?? '-' }}</td>
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
                                @if($college->university)
                                <span class="badge {{ $college->university->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                    {{ ucfirst($college->university->status) }}
                                </span>
                                @else
                                <span class="badge bg-secondary">N/A</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No records found matching the criteria.</td>
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
            .card-header .btn, .card-header a {
                display: none !important;
            }
        }
    </style>
</x-app-layout>
