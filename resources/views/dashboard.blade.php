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
                
                <div class="d-flex align-items-center position-relative">
                    <div class="me-4 d-none d-md-block">
                        <div class="p-3 bg-white bg-opacity-10 rounded-3 text-white">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-1 text-white">Welcome back, {{ Auth::user()->name }}!</h4>
                        <p class="mb-0 text-white-50">Here's a quick overview of your institution directory and latest client interactions.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 position-relative overflow-hidden" style="border-left: 4px solid var(--primary-color);">
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
            <div class="card h-100 position-relative overflow-hidden" style="border-left: 4px solid var(--secondary-color);">
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
            <div class="card h-100 position-relative overflow-hidden" style="border-left: 4px solid #06B6D4;">
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
            <div class="card h-100 position-relative overflow-hidden" style="border-left: 4px solid #10B981;">
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
            <div class="card h-100 position-relative overflow-hidden" style="border-left: 4px solid #F59E0B;">
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
            <div class="card h-100 position-relative overflow-hidden" style="border-left: 4px solid #8B5CF6;">
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
            <div class="card h-100 position-relative overflow-hidden" style="border-left: 4px solid #6366F1;">
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
            <div class="card h-100 position-relative overflow-hidden" style="border-left: 4px solid #F43F5E;">
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
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center py-3">
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
                                            {{ $college->affiliated_university ?? 'N/A' }}
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
            <div class="card h-100">
                <div class="card-header py-3">
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

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('interactionChart').getContext('2d');
                const data = @json($collegeInteractionStats);
                
                const labels = Object.keys(data);
                const values = Object.values(data);
                
                // Colors matching the dashboard's design system
                const colorsMap = {
                    'Interacted': '#10B981',        // Emerald Green
                    'Not Interacted': '#EC4899'     // Pink
                };
                
                const backgroundColors = labels.map(label => colorsMap[label] || '#8B5CF6');
                
                const total = values.reduce((a, b) => a + b, 0);

                if (total === 0) {
                    ctx.font = '14px "Plus Jakarta Sans"';
                    ctx.fillStyle = '#64748B';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText('No institutions registered yet.', ctx.canvas.width / 2, ctx.canvas.height / 2);
                    return;
                }

                new Chart(ctx, {
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
