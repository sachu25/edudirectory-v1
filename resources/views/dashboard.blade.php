<x-app-layout>
    <x-slot name="header">
        Dashboard Overview
    </x-slot>

    <!-- Welcome Banner Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 p-4 position-relative overflow-hidden" style="background: linear-gradient(135deg, #0F172A 0%, #1E1B4B 100%); color: white; border-radius: 16px;">
                <!-- Decorative background circles -->
                <div class="position-absolute rounded-circle" style="width: 180px; height: 180px; background: rgba(236, 72, 153, 0.12); top: -60px; right: -60px; filter: blur(35px);"></div>
                <div class="position-absolute rounded-circle" style="width: 280px; height: 280px; background: rgba(79, 70, 229, 0.12); bottom: -120px; right: 120px; filter: blur(50px);"></div>
                
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between position-relative gap-3">
                    <div class="d-flex align-items-center">
                        <div class="me-4 d-none d-md-block">
                            <div class="p-3 bg-white bg-opacity-10 rounded-3 text-white">
                                <i class="fas fa-user-circle fa-2x"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-1 text-white">Welcome back, {{ Auth::user()->name }}!</h4>
                            <p class="mb-0 text-white-50">Here is your personalized activity hub and overall institution directory performance.</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('interactions.create') }}" class="btn btn-primary px-3 py-2 fw-semibold" style="border-radius: 10px;">
                            <i class="fas fa-plus-circle me-1"></i> Log Interaction
                        </a>
                        <a href="{{ route('colleges.create') }}" class="btn btn-outline-light px-3 py-2 fw-semibold" style="border-radius: 10px;">
                            <i class="fas fa-graduation-cap me-1"></i> Add Institution
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Workspace Navigation Tabs -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <ul class="nav nav-pills custom-dashboard-tabs" id="dashboardTabs" role="tablist" style="gap: 8px;">
            <li class="nav-item" role="presentation">
                <button class="nav-link active px-4 py-2 fw-semibold" id="my-activity-tab" data-bs-toggle="pill" data-bs-target="#my-activity-pane" type="button" role="tab" aria-controls="my-activity-pane" aria-selected="true" style="border-radius: 10px;">
                    <i class="fas fa-user-check me-2"></i> My Personal Activity
                    @if($myPendingFollowupsCount > 0)
                        <span class="badge bg-danger ms-2 rounded-pill">{{ $myPendingFollowupsCount }}</span>
                    @endif
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link px-4 py-2 fw-semibold" id="system-overview-tab" data-bs-toggle="pill" data-bs-target="#system-overview-pane" type="button" role="tab" aria-controls="system-overview-pane" aria-selected="false" style="border-radius: 10px;">
                    <i class="fas fa-chart-pie me-2"></i> System Wide Overview
                </button>
            </li>
        </ul>
        <div class="text-muted small d-none d-md-block">
            <i class="fas fa-clock me-1 text-primary"></i> Last updated: {{ now()->format('d M Y, h:i A') }}
        </div>
    </div>

    <div class="tab-content" id="dashboardTabsContent">
        <!-- ==================== TAB 1: MY PERSONAL ACTIVITY ==================== -->
        <div class="tab-pane fade show active" id="my-activity-pane" role="tabpanel" aria-labelledby="my-activity-tab" tabindex="0">
            <!-- User Stat Cards -->
            <div class="row g-4 mb-4">
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card h-100 position-relative overflow-hidden border-0 shadow-sm" style="border-left: 4px solid #4F46E5 !important; border-radius: 12px;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted fw-normal mb-2 text-uppercase" style="font-size: 0.78rem; letter-spacing: 0.5px;">My Total Interactions</h6>
                                    <h3 class="mb-0 fw-bold text-dark">{{ $myTotalInteractionsCount }}</h3>
                                    <small class="text-muted">Academic & Non-Academic</small>
                                </div>
                                <div class="p-3 rounded-circle" style="background: rgba(79, 70, 229, 0.1); color: #4F46E5;">
                                    <i class="fas fa-comments fa-lg"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card h-100 position-relative overflow-hidden border-0 shadow-sm" style="border-left: 4px solid #06B6D4 !important; border-radius: 12px;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted fw-normal mb-2 text-uppercase" style="font-size: 0.78rem; letter-spacing: 0.5px;">My Institutions</h6>
                                    <h3 class="mb-0 fw-bold text-dark">{{ $myAddedCollegesCount }}</h3>
                                    <small class="text-muted">Colleges & Universities created</small>
                                </div>
                                <div class="p-3 rounded-circle" style="background: rgba(6, 182, 212, 0.1); color: #06B6D4;">
                                    <i class="fas fa-university fa-lg"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card h-100 position-relative overflow-hidden border-0 shadow-sm" style="border-left: 4px solid #8B5CF6 !important; border-radius: 12px;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted fw-normal mb-2 text-uppercase" style="font-size: 0.78rem; letter-spacing: 0.5px;">My Contacts</h6>
                                    <h3 class="mb-0 fw-bold text-dark">{{ $myAddedContactsCount }}</h3>
                                    <small class="text-muted">Contact decision-makers</small>
                                </div>
                                <div class="p-3 rounded-circle" style="background: rgba(139, 92, 246, 0.1); color: #8B5CF6;">
                                    <i class="fas fa-address-book fa-lg"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card h-100 position-relative overflow-hidden border-0 shadow-sm" style="border-left: 4px solid #EC4899 !important; border-radius: 12px;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted fw-normal mb-2 text-uppercase" style="font-size: 0.78rem; letter-spacing: 0.5px;">My Corporate Clients</h6>
                                    <h3 class="mb-0 fw-bold text-dark">{{ $myAddedClientsCount }}</h3>
                                    <small class="text-muted">Non-Academic Partners</small>
                                </div>
                                <div class="p-3 rounded-circle" style="background: rgba(236, 72, 153, 0.1); color: #EC4899;">
                                    <i class="fas fa-building fa-lg"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card h-100 position-relative overflow-hidden border-0 shadow-sm" style="border-left: 4px solid #F59E0B !important; border-radius: 12px;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted fw-normal mb-2 text-uppercase" style="font-size: 0.78rem; letter-spacing: 0.5px;">My Upcoming Follow-ups</h6>
                                    <h3 class="mb-0 fw-bold text-dark">{{ $myPendingFollowupsCount }}</h3>
                                    <small class="text-warning font-semibold">Scheduled Action Items</small>
                                </div>
                                <div class="p-3 rounded-circle" style="background: rgba(245, 158, 11, 0.1); color: #F59E0B;">
                                    <i class="fas fa-calendar-check fa-lg"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card h-100 position-relative overflow-hidden border-0 shadow-sm" style="border-left: 4px solid #10B981 !important; border-radius: 12px;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted fw-normal mb-2 text-uppercase" style="font-size: 0.78rem; letter-spacing: 0.5px;">My Monthly Actions</h6>
                                    <h3 class="mb-0 fw-bold text-dark">{{ $myMonthlyAuditCount }}</h3>
                                    <small class="text-muted">Total activities logged</small>
                                </div>
                                <div class="p-3 rounded-circle" style="background: rgba(16, 185, 129, 0.1); color: #10B981;">
                                    <i class="fas fa-history fa-lg"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Two Column Row: Upcoming Follow-ups & My Recent Interactions -->
            <div class="row g-4 mb-4">
                <!-- Upcoming Follow-ups Timeline Widget -->
                <div class="col-12 col-lg-6">
                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 12px;">
                        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-clock text-warning me-2"></i>My Scheduled Follow-ups</h6>
                                <small class="text-muted">Upcoming call backs & meetings</small>
                            </div>
                            <span class="badge bg-warning bg-opacity-10 text-dark fw-semibold px-3 py-2 rounded-pill">
                                {{ $myPendingFollowupsCount }} Pending
                            </span>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @forelse($myUpcomingFollowups as $followup)
                                    <div class="list-group-item p-3 border-light border-0 border-bottom">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <span class="badge {{ $followup->type_badge }} border px-2 py-1" style="font-size: 0.75rem;">
                                                <i class="{{ $followup->icon }} me-1"></i> {{ $followup->type }}
                                            </span>
                                            <span class="badge bg-warning text-dark font-mono">
                                                <i class="fas fa-calendar me-1"></i> {{ \Carbon\Carbon::parse($followup->next_followup_date)->format('M d, Y') }}
                                            </span>
                                        </div>
                                        <h6 class="mb-1 text-dark fw-bold">{{ $followup->title }}</h6>
                                        <div class="text-muted small mb-2">
                                            {{ $followup->subtitle }}
                                        </div>
                                        @if($followup->remarks)
                                            <p class="mb-2 text-secondary small bg-light p-2 rounded" style="font-style: italic;">
                                                "{{ Str::limit($followup->remarks, 80) }}"
                                            </p>
                                        @endif
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-info text-dark">{{ $followup->status }}</span>
                                            <a href="{{ $followup->view_url }}" class="btn btn-sm btn-outline-primary px-3 rounded-pill">
                                                View Details <i class="fas fa-arrow-right ms-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5">
                                        <div class="mb-3 text-muted opacity-50">
                                            <i class="fas fa-calendar-check fa-3x"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark">No Pending Follow-ups</h6>
                                        <p class="text-muted small mb-0">You're all caught up! No scheduled follow-ups found.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- My Recent Interactions Table -->
                <div class="col-12 col-lg-6">
                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 12px;">
                        <div class="card-header bg-white py-3 border-0 d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <div>
                                <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-history text-primary me-2"></i>My Recent Interactions</h6>
                                <small class="text-muted">Latest touchpoints logged by you</small>
                            </div>
                            <ul class="nav nav-pills nav-fill bg-light p-1 rounded-pill" id="recentInteractionsTabs" role="tablist" style="font-size: 0.75rem;">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active px-3 py-1 rounded-pill fw-semibold" id="recent-academic-tab" data-bs-toggle="pill" data-bs-target="#recent-academic-pane" type="button" role="tab">Academic</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link px-3 py-1 rounded-pill fw-semibold" id="recent-non-academic-tab" data-bs-toggle="pill" data-bs-target="#recent-non-academic-pane" type="button" role="tab">Non-Academic</button>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body p-0">
                            <div class="tab-content" id="recentInteractionsTabContent">
                                <!-- Academic Sub-tab -->
                                <div class="tab-pane fade show active" id="recent-academic-pane" role="tabpanel" tabindex="0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light text-secondary" style="font-size: 0.8rem; text-uppercase; letter-spacing: 0.5px;">
                                                <tr>
                                                    <th class="ps-3 py-3">Institution</th>
                                                    <th class="py-3">Contact Mode</th>
                                                    <th class="py-3">Status</th>
                                                    <th class="py-3 text-end pe-3">Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($myRecentInteractions as $interaction)
                                                    <tr>
                                                        <td class="ps-3">
                                                            <div class="fw-bold text-dark" style="font-size: 0.9rem;">{{ $interaction->college->name ?? 'N/A' }}</div>
                                                            <div class="text-muted small">{{ $interaction->contactPerson->name ?? 'N/A' }}</div>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-light text-dark border px-2 py-1" style="border-radius: 6px;">
                                                                <i class="fas fa-phone-alt me-1 text-primary"></i> {{ $interaction->contactMode->name ?? 'Call' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="border-radius: 12px;">
                                                                {{ $interaction->status->name ?? 'Logged' }}
                                                            </span>
                                                        </td>
                                                        <td class="text-end pe-3 text-muted small">
                                                            {{ \Carbon\Carbon::parse($interaction->contact_date)->format('M d') }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center py-5 text-muted">
                                                            <i class="fas fa-inbox fa-2x mb-2 d-block opacity-50"></i>
                                                            You haven't logged any academic interactions yet.
                                                            <div class="mt-2">
                                                                <a href="{{ route('interactions.create') }}" class="btn btn-sm btn-primary px-3 rounded-pill">Log First Interaction</a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Non-Academic Sub-tab -->
                                <div class="tab-pane fade" id="recent-non-academic-pane" role="tabpanel" tabindex="0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light text-secondary" style="font-size: 0.8rem; text-uppercase; letter-spacing: 0.5px;">
                                                <tr>
                                                    <th class="ps-3 py-3">Client Company</th>
                                                    <th class="py-3">Contact Mode</th>
                                                    <th class="py-3">Status</th>
                                                    <th class="py-3 text-end pe-3">Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($myRecentNonAcademicInteractions as $naInteraction)
                                                    <tr>
                                                        <td class="ps-3">
                                                            <div class="fw-bold text-dark" style="font-size: 0.9rem;">{{ $naInteraction->client->name ?? 'N/A' }}</div>
                                                            <div class="text-muted small">{{ $naInteraction->purpose ?? 'General Touchpoint' }}</div>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-light text-dark border px-2 py-1" style="border-radius: 6px;">
                                                                <i class="fas fa-handshake me-1 text-success"></i> {{ $naInteraction->contact_mode ?? 'Meeting' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1" style="border-radius: 12px;">
                                                                {{ $naInteraction->interaction_status ?? 'Logged' }}
                                                            </span>
                                                        </td>
                                                        <td class="text-end pe-3 text-muted small">
                                                            {{ \Carbon\Carbon::parse($naInteraction->contact_date)->format('M d') }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center py-5 text-muted">
                                                            <i class="fas fa-building fa-2x mb-2 d-block opacity-50"></i>
                                                            You haven't logged any corporate client interactions yet.
                                                            <div class="mt-2">
                                                                <a href="{{ route('non-academic-interactions.create') }}" class="btn btn-sm btn-outline-primary px-3 rounded-pill">Log Corporate Touchpoint</a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== TAB 2: SYSTEM WIDE OVERVIEW ==================== -->
        <div class="tab-pane fade" id="system-overview-pane" role="tabpanel" aria-labelledby="system-overview-tab" tabindex="0">
            <div class="row g-4 mb-4">
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card h-100 position-relative overflow-hidden border-0 shadow-sm" style="border-left: 4px solid var(--primary-color) !important; border-radius: 12px;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted fw-normal mb-2 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Total Institutions</h6>
                                    <h3 class="mb-0 fw-bold text-dark">{{ $totalColleges }}</h3>
                                </div>
                                <div class="p-3 rounded-circle" style="background: rgba(79, 70, 229, 0.1); color: var(--primary-color);">
                                    <i class="fas fa-graduation-cap fa-lg"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card h-100 position-relative overflow-hidden border-0 shadow-sm" style="border-left: 4px solid var(--secondary-color) !important; border-radius: 12px;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted fw-normal mb-2 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Total Universities</h6>
                                    <h3 class="mb-0 fw-bold text-dark">{{ $totalUniversities }}</h3>
                                </div>
                                <div class="p-3 rounded-circle" style="background: rgba(236, 72, 153, 0.1); color: var(--secondary-color);">
                                    <i class="fas fa-university fa-lg"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card h-100 position-relative overflow-hidden border-0 shadow-sm" style="border-left: 4px solid #06B6D4 !important; border-radius: 12px;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted fw-normal mb-2 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Contact Persons</h6>
                                    <h3 class="mb-0 fw-bold text-dark">{{ $totalContacts }}</h3>
                                </div>
                                <div class="p-3 rounded-circle" style="background: rgba(6, 182, 212, 0.1); color: #06B6D4;">
                                    <i class="fas fa-address-book fa-lg"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card h-100 position-relative overflow-hidden border-0 shadow-sm" style="border-left: 4px solid #10B981 !important; border-radius: 12px;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted fw-normal mb-2 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Autonomous Inst.</h6>
                                    <h3 class="mb-0 fw-bold text-dark">{{ $autonomousColleges }}</h3>
                                </div>
                                <div class="p-3 rounded-circle" style="background: rgba(16, 185, 129, 0.1); color: #10B981;">
                                    <i class="fas fa-certificate fa-lg"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card h-100 position-relative overflow-hidden border-0 shadow-sm" style="border-left: 4px solid #F59E0B !important; border-radius: 12px;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted fw-normal mb-2 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Affiliated Inst.</h6>
                                    <h3 class="mb-0 fw-bold text-dark">{{ $affiliatedColleges }}</h3>
                                </div>
                                <div class="p-3 rounded-circle" style="background: rgba(245, 158, 11, 0.1); color: #F59E0B;">
                                    <i class="fas fa-link fa-lg"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card h-100 position-relative overflow-hidden border-0 shadow-sm" style="border-left: 4px solid #8B5CF6 !important; border-radius: 12px;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted fw-normal mb-2 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Inst. Added This Month</h6>
                                    <h3 class="mb-0 fw-bold text-dark">{{ $addedThisMonth }}</h3>
                                </div>
                                <div class="p-3 rounded-circle" style="background: rgba(139, 92, 246, 0.1); color: #8B5CF6;">
                                    <i class="fas fa-calendar-plus fa-lg"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card h-100 position-relative overflow-hidden border-0 shadow-sm" style="border-left: 4px solid #6366F1 !important; border-radius: 12px;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted fw-normal mb-2 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Non-Academic Clients</h6>
                                    <h3 class="mb-0 fw-bold text-dark">{{ $totalNonAcademicClients }}</h3>
                                </div>
                                <div class="p-3 rounded-circle" style="background: rgba(99, 102, 241, 0.1); color: #6366F1;">
                                    <i class="fas fa-building fa-lg"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card h-100 position-relative overflow-hidden border-0 shadow-sm" style="border-left: 4px solid #F43F5E !important; border-radius: 12px;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted fw-normal mb-2 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Non-Acad. Interactions</h6>
                                    <h3 class="mb-0 fw-bold text-dark">{{ $totalNonAcademicInteractions }}</h3>
                                </div>
                                <div class="p-3 rounded-circle" style="background: rgba(244, 63, 94, 0.1); color: #F43F5E;">
                                    <i class="fas fa-handshake fa-lg"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-12 col-lg-8">
                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 12px;">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 border-0">
                            <h6 class="mb-0 fw-bold text-dark">Recently Registered Institutions</h6>
                            <a href="{{ route('colleges.index') }}" class="btn btn-sm btn-outline-primary px-3" style="border-radius: 8px;">View All</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light text-secondary" style="font-size: 0.85rem; text-uppercase; letter-spacing: 0.5px;">
                                        <tr>
                                            <th class="ps-4 py-3">Institution Name</th>
                                            <th class="py-3">Affiliated University</th>
                                            <th class="py-3">Type</th>
                                            <th class="py-3">State</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentColleges as $college)
                                        <tr style="transition: background 0.2s;">
                                            <td class="ps-4 fw-semibold text-dark">{{ $college->name }}</td>
                                            <td>
                                                <span class="badge bg-light text-dark border py-2 px-2" style="border-radius: 6px; font-weight: 500;">
                                                    <i class="fas fa-university text-muted me-1"></i>
                                                    {{ $college->affiliated_university ?? ($college->university->name ?? 'N/A') }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $badgeClass = $college->type === 'Autonomous' ? 'bg-primary' : 'bg-secondary';
                                                @endphp
                                                <span class="badge {{ $badgeClass }} px-3 py-2" style="border-radius: 20px; font-weight: 500;">{{ $college->type }}</span>
                                            </td>
                                            <td class="text-muted">
                                                <i class="fas fa-map-marker-alt me-1 text-danger opacity-75"></i> 
                                                {{ $college->state ?? 'N/A' }}
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">No institutions added yet.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 12px;">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="mb-0 fw-bold text-dark">Institution Interaction Coverage</h6>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-center">
                            <div style="position: relative; height: 260px; width: 100%;">
                                <canvas id="interactionChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('interactionChart');
                if (!ctx) return;
                
                const data = @json($collegeInteractionStats);
                const labels = Object.keys(data);
                const values = Object.values(data);
                
                const colorsMap = {
                    'Interacted': '#10B981',
                    'Not Interacted': '#EC4899'
                };
                
                const backgroundColors = labels.map(label => colorsMap[label] || '#8B5CF6');
                const total = values.reduce((a, b) => a + b, 0);

                const chartContext = ctx.getContext('2d');

                if (total === 0) {
                    chartContext.font = '14px "Plus Jakarta Sans"';
                    chartContext.fillStyle = '#64748B';
                    chartContext.textAlign = 'center';
                    chartContext.textBaseline = 'middle';
                    chartContext.fillText('No institutions registered yet.', chartContext.canvas.width / 2, chartContext.canvas.height / 2);
                    return;
                }

                new Chart(chartContext, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: values,
                            backgroundColor: backgroundColors,
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    font: {
                                        family: 'Plus Jakarta Sans',
                                        size: 11
                                    },
                                    boxWidth: 12,
                                    padding: 15,
                                    color: '#1E293B'
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.raw || 0;
                                        const percentage = Math.round((value / total) * 100);
                                        return ` ${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        cutout: '70%'
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
