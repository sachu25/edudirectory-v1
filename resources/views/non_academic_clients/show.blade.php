<x-app-layout>
    <x-slot name="header">
        Client Profile - {{ $client->name }}
    </x-slot>

    <div class="mb-4">
        <a href="{{ route('non-academic-clients.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Clients List
        </a>
    </div>

    <div class="row g-4">
        <!-- Client Profile Details -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="fw-bold mb-0 text-primary d-flex align-items-center">
                        <i class="fas fa-building me-2"></i> {{ $client->name }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <span class="text-muted small text-uppercase fw-semibold d-block">Industry / Sector</span>
                            <span class="fw-semibold text-dark">{{ $client->industry ?? 'N/A' }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small text-uppercase fw-semibold d-block">Website</span>
                            @if($client->website)
                                <a href="{{ str_starts_with($client->website, 'http') ? $client->website : 'http://' . $client->website }}" target="_blank" class="text-decoration-none">
                                    <i class="fas fa-external-link-alt me-1"></i>{{ $client->website }}
                                </a>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small text-uppercase fw-semibold d-block">Official Email</span>
                            <span class="text-dark">{{ $client->email ?? 'N/A' }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small text-uppercase fw-semibold d-block">Office Phone</span>
                            <span class="text-dark">{{ $client->phone ?? 'N/A' }}</span>
                        </div>
                        <div class="col-12">
                            <span class="text-muted small text-uppercase fw-semibold d-block">Address</span>
                            <p class="mb-0 text-dark" style="white-space: pre-line;">{{ $client->address ?? 'N/A' }}</p>
                        </div>
                        @if($client->remarks)
                        <div class="col-12 border-top pt-3">
                            <span class="text-muted small text-uppercase fw-semibold d-block">General Client Notes</span>
                            <p class="mb-0 text-dark" style="white-space: pre-line;">{{ $client->remarks }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Interaction History Timeline -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-history text-primary me-2"></i> Interaction Timeline</h6>
                    <a href="{{ route('non-academic-interactions.index') }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-plus me-1"></i> Log Interaction</a>
                </div>
                <div class="card-body p-4">
                    @forelse($client->interactions->sortByDesc('contact_date') as $interaction)
                        <div class="d-flex mb-4 pb-3 border-bottom">
                            <div class="me-3">
                                <span class="badge bg-primary p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 50%;">
                                    <i class="fas fa-comments fs-5"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="fw-bold mb-0 text-dark">{{ $interaction->purpose }}</h6>
                                    <span class="text-muted small"><i class="far fa-calendar-alt me-1"></i>{{ $interaction->contact_date->format('d M Y H:i') }}</span>
                                </div>
                                <div class="mb-2">
                                    <span class="badge bg-light text-dark border me-1">{{ $interaction->contact_mode }}</span>
                                    <span class="badge bg-secondary me-1">{{ $interaction->interaction_status }}</span>
                                    @if($interaction->next_followup_date)
                                        <span class="badge bg-warning text-dark me-1"><i class="fas fa-calendar-check me-1"></i>Next Follow-up: {{ \Carbon\Carbon::parse($interaction->next_followup_date)->format('d M Y') }}</span>
                                    @endif
                                    <span class="text-muted small">by {{ $interaction->employee ? $interaction->employee->name : 'N/A' }}</span>
                                </div>
                                @if($interaction->client_response)
                                    <div class="bg-light p-2 rounded mb-2 small text-dark">
                                        <strong>Response:</strong> {{ $interaction->client_response }}
                                    </div>
                                @endif
                                @if($interaction->remarks)
                                    <p class="mb-0 text-muted small"><strong>Remarks:</strong> {{ $interaction->remarks }}</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No interactions logged yet for this client.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Contact Person Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light py-3">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-user-tie text-secondary me-2"></i> Primary Contact Person</h6>
                </div>
                <div class="card-body">
                    @if($client->contact_person_name)
                        <h5 class="fw-bold mb-1">{{ $client->contact_person_name }}</h5>
                        <p class="text-muted mb-3">{{ $client->contact_person_designation ?? 'N/A' }}</p>
                        
                        <div class="d-flex flex-column gap-2 small">
                            <div>
                                <i class="fas fa-envelope text-muted me-2"></i>{{ $client->contact_person_email ?? 'N/A' }}
                            </div>
                            <div>
                                <i class="fas fa-phone text-muted me-2"></i>{{ $client->contact_person_phone ?? 'N/A' }}
                            </div>
                        </div>
                    @else
                        <p class="text-muted mb-0">No contact person registered.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
