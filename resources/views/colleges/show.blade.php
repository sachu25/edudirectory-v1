<x-app-layout>
    <x-slot name="header">
        Institution Details: {{ $college->name }}
    </x-slot>

    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <div class="card h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-info-circle me-2 text-primary"></i> General Information</h6>
                    <a href="{{ route('colleges.index') }}" class="btn btn-sm btn-outline-secondary">Back to List</a>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Institution Name</div>
                        <div class="col-sm-8 fw-semibold">{{ $college->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Institution Code</div>
                        <div class="col-sm-8">{{ $college->code ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Type</div>
                        <div class="col-sm-8"><span class="badge bg-secondary">{{ $college->type ?? 'N/A' }}</span></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Category</div>
                        <div class="col-sm-8"><span class="badge bg-primary">{{ $college->is_university ? 'University' : 'Institution' }}</span></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Affiliated University</div>
                        <div class="col-sm-8">{{ $college->university ? $college->university->name : 'N/A' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">NAAC Grade</div>
                        <div class="col-sm-8">{{ $college->naac_grade ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">NIRF Ranking</div>
                        <div class="col-sm-8">{{ $college->nirf_ranking ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Established Year</div>
                        <div class="col-sm-8">{{ $college->established_year ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Remarks</div>
                        <div class="col-sm-8">{{ $college->remarks ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card mb-4">
                <div class="card-header py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-address-card me-2 text-primary"></i> Contact Details</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2"><i class="fas fa-globe text-muted me-2"></i> <a href="{{ $college->website }}" target="_blank">{{ $college->website ?? 'N/A' }}</a></p>
                    <p class="mb-2"><i class="fas fa-envelope text-muted me-2"></i> {{ $college->official_email ?? 'N/A' }}</p>
                    <p class="mb-2"><i class="fas fa-phone text-muted me-2"></i> {{ $college->office_phone ?? 'N/A' }}</p>
                    <p class="mb-0"><i class="fas fa-mobile-alt text-muted me-2"></i> {{ $college->office_mobile ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-map-marker-alt me-2 text-primary"></i> Location</h6>
                </div>
                <div class="card-body">
                    <p class="mb-1">{{ $college->address }}</p>
                    <p class="mb-1">{{ $college->district }}{{ $college->state ? ', ' . $college->state : '' }}</p>
                    <p class="mb-0">{{ $college->country }} - {{ $college->pin_code }}</p>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-chart-pie me-2 text-primary"></i> Facilities & Strengths</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3 mb-md-0 text-center">
                            <h3 class="text-primary fw-bold">{{ $college->student_strength ?? '-' }}</h3>
                            <span class="text-muted small text-uppercase">Student Strength</span>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0 text-center">
                            <h3 class="text-primary fw-bold">{{ $college->faculty_strength ?? '-' }}</h3>
                            <span class="text-muted small text-uppercase">Faculty Strength</span>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0 text-center">
                            <h3 class="{{ $college->hostel_facility ? 'text-success' : 'text-danger' }} fw-bold">
                                <i class="fas {{ $college->hostel_facility ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                            </h3>
                            <span class="text-muted small text-uppercase">Hostel Facility</span>
                        </div>
                        <div class="col-md-3 text-center">
                            <h3 class="{{ $college->placement_cell ? 'text-success' : 'text-danger' }} fw-bold">
                                <i class="fas {{ $college->placement_cell ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                            </h3>
                            <span class="text-muted small text-uppercase">Placement Cell</span>
                        </div>
                    </div>
                    
                    @if($college->courses_offered)
                    <hr>
                    <div class="mt-3">
                        <h6 class="fw-semibold text-muted mb-2 text-uppercase small">Courses Offered</h6>
                        <div>
                            @foreach(explode(',', $college->courses_offered) as $course)
                                <span class="badge bg-light text-dark border me-1 mb-1">{{ trim($course) }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-users me-2 text-primary"></i> Key Contact Persons</h6>
                    @can('contacts.view')
                    <a href="{{ route('contacts.index') }}" class="btn btn-sm btn-outline-primary">Manage Contacts</a>
                    @endcan
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Name</th>
                                    <th>Designation</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($college->contactPersons as $contact)
                                <tr>
                                    <td class="ps-4 fw-medium">{{ $contact->name }}</td>
                                    <td>{{ $contact->designation ? $contact->designation->name : 'N/A' }}</td>
                                    <td><a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a></td>
                                    <td>{{ $contact->mobile }}</td>
                                    <td>
                                        <span class="badge {{ $contact->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                            {{ ucfirst($contact->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No contact persons associated with this institution.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Interactions Section -->
            <div class="card mt-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-primary"><i class="fas fa-handshake me-2"></i> Recent Interactions</h5>
                    @can('interactions.view')
                    <a href="{{ route('interactions.index') }}" class="btn btn-sm btn-outline-primary">Manage Interactions</a>
                    @endcan
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Date</th>
                                    <th>Mode</th>
                                    <th>Contact Person</th>
                                    <th>Purposes</th>
                                    <th>Status</th>
                                    <th>Staff</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($college->interactions()->latest('contact_date')->get() as $interaction)
                                <tr>
                                    <td class="ps-4">{{ $interaction->contact_date ? $interaction->contact_date->format('M d, Y h:i A') : 'N/A' }}</td>
                                    <td>{{ $interaction->contactMode ? $interaction->contactMode->name : 'N/A' }}</td>
                                    <td>{{ $interaction->contactPerson ? $interaction->contactPerson->name : 'General' }}</td>
                                    <td>
                                        @foreach($interaction->purposes as $purpose)
                                            <span class="badge bg-secondary">{{ $purpose->name }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        @if($interaction->status)
                                            <span class="badge bg-info">{{ $interaction->status->name }}</span>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $interaction->user ? $interaction->user->name : 'N/A' }}</td>
                                </tr>
                                @if($interaction->college_response || $interaction->remarks)
                                <tr>
                                    <td colspan="6" class="bg-light ps-4 text-muted" style="font-size: 0.85rem;">
                                        @if($interaction->college_response)
                                            <strong>Response:</strong> {{ $interaction->college_response }}<br>
                                        @endif
                                        @if($interaction->remarks)
                                            <strong>Remarks:</strong> {{ $interaction->remarks }}
                                        @endif
                                    </td>
                                </tr>
                                @endif
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No interactions recorded for this institution yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>
