<x-app-layout>
    <x-slot name="header">
        Import Data Center
    </x-slot>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold text-dark mb-1">Bulk Data Import</h5>
            <p class="text-muted mb-0 small">Streamline your database entry. Choose to upload colleges and contacts separately or use the unified single-upload file.</p>
        </div>
    </div>

    <!-- Tab navigation -->
    <ul class="nav nav-pills mb-4 bg-white p-2 rounded shadow-sm" id="importTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-semibold" id="unified-tab" data-bs-toggle="pill" data-bs-target="#unified" type="button" role="tab" aria-controls="unified" aria-selected="true">
                <i class="fas fa-file-invoice me-2 text-primary"></i>Unified Import (Institutions & Contacts)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="colleges-tab" data-bs-toggle="pill" data-bs-target="#colleges" type="button" role="tab" aria-controls="colleges" aria-selected="false">
                <i class="fas fa-university me-2 text-primary"></i>Institutions Only
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="contacts-tab" data-bs-toggle="pill" data-bs-target="#contacts" type="button" role="tab" aria-controls="contacts" aria-selected="false">
                <i class="fas fa-address-book me-2 text-primary"></i>Contacts Only
            </button>
        </li>
    </ul>

    <!-- Tab contents -->
    <div class="tab-content" id="importTabContent">
        
        <!-- Tab 1: Unified Import -->
        <div class="tab-pane fade show active" id="unified" role="tabpanel" aria-labelledby="unified-tab">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-upload text-primary me-2"></i> Upload Unified Sheet</h6>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-4">Upload a combined Excel/CSV file to import both institutions and contacts simultaneously. If an institution doesn't exist, it will be automatically created using our duplicate prevention system.</p>
                            
                            <form action="{{ route('imports.unified') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-4">
                                    <label for="unified_file" class="form-label fw-semibold small">Select Unified File (Excel/CSV)</label>
                                    <input class="form-control" type="file" id="unified_file" name="import_file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                                    <div class="form-text small text-muted">Maximum file size: 5MB</div>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <button type="submit" class="btn btn-primary px-4"><i class="fas fa-file-import me-2"></i> Start Unified Import</button>
                                    <a href="{{ route('imports.download-unified-template') }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-download me-1"></i> Download Template</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card h-100 bg-light border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3 text-dark"><i class="fas fa-info-circle text-info me-2"></i> Unified File Format</h6>
                            <p class="small text-muted">The uploaded sheet must have a header row with these exact columns:</p>
                            
                            <div class="row mb-3">
                                <div class="col-6">
                                    <span class="badge bg-primary mb-2">Institution Data</span>
                                    <ul class="small text-muted ps-3 mb-0">
                                        <li><code>college_name</code> <span class="text-danger">*</span></li>
                                        <li><code>type</code> (Affiliated/Autonomous)</li>
                                        <li><code>affiliated_university</code></li>
                                    </ul>
                                </div>
                                <div class="col-6">
                                    <span class="badge bg-secondary mb-2">Contact Data</span>
                                    <ul class="small text-muted ps-3 mb-0">
                                        <li><code>contact_name</code> <span class="text-danger">*</span></li>
                                        <li><code>designation</code> <span class="text-danger">*</span></li>
                                        <li><code>department</code></li>
                                        <li><code>mobile</code></li>
                                        <li><code>email</code></li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="alert alert-warning border-0 small mb-0">
                                <i class="fas fa-exclamation-triangle me-2 text-warning"></i> <strong>Note:</strong> Multiple contacts for the same institution will be linked correctly under one institution entry automatically.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

         <!-- Tab 2: Institutions Only -->
        <div class="tab-pane fade" id="colleges" role="tabpanel" aria-labelledby="colleges-tab">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-upload text-primary me-2"></i> Upload Institutions Sheet</h6>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-4">Upload an Excel/CSV file containing only institution records. Existing entries will be updated without duplicates.</p>
                            
                            <form action="{{ route('imports.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-4">
                                    <label for="college_file" class="form-label fw-semibold small">Select Institutions File (Excel/CSV)</label>
                                    <input class="form-control" type="file" id="college_file" name="import_file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                                    <div class="form-text small text-muted">Maximum file size: 5MB</div>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <button type="submit" class="btn btn-primary px-4"><i class="fas fa-file-import me-2"></i> Import Institutions</button>
                                    <a href="{{ route('colleges.download-template') }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-download me-1"></i> Download Template</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card h-100 bg-light border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3 text-dark"><i class="fas fa-info-circle text-primary me-2"></i> Institution Format Instructions</h6>
                            <p class="small text-muted">Please ensure your sheet contains a header row with the following column names:</p>
                                                        <ul class="small text-muted ps-3 mb-4">
                                <li><code>college_name</code> (Required)</li>
                                <li><code>type</code> (Autonomous, Affiliated, etc.)</li>
                                <li><code>affiliated_university</code> (Optional)</li>
                            </ul>
                            
                            <div class="alert alert-info border-0 small mb-0">
                                <i class="fas fa-info-circle me-2 text-info"></i> Any specified <strong>affiliated_university</strong> will automatically be linked (and created as a new university master option if it doesn't exist).
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

         <!-- Tab 3: Contacts Only -->
        <div class="tab-pane fade" id="contacts" role="tabpanel" aria-labelledby="contacts-tab">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-upload text-primary me-2"></i> Upload Contacts Sheet</h6>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-4">Upload an Excel/CSV file containing only contact person records. This requires the associated institutions to already exist in the database.</p>
                            
                            <form action="{{ route('contacts.import') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-4">
                                    <label for="contact_file" class="form-label fw-semibold small">Select Contacts File (Excel/CSV)</label>
                                    <input class="form-control" type="file" id="contact_file" name="import_file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                                    <div class="form-text small text-muted">Maximum file size: 5MB</div>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <button type="submit" class="btn btn-primary px-4"><i class="fas fa-file-import me-2"></i> Import Contacts</button>
                                    <a href="{{ route('contacts.download-template') }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-download me-1"></i> Download Template</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                 <div class="col-md-6">
                    <div class="card h-100 bg-light border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3 text-dark"><i class="fas fa-info-circle text-primary me-2"></i> Contact Format Instructions</h6>
                            <p class="small text-muted">Please ensure your sheet contains a header row with the following column names:</p>
                            
                            <ul class="small text-muted ps-3 mb-4">
                                <li><code>college_name</code> (Required: must match existing institution in the DB)</li>
                                <li><code>contact_name</code> (Required)</li>
                                <li><code>designation</code> (Required)</li>
                                <li><code>department</code> (Optional)</li>
                                <li><code>mobile</code> (Optional)</li>
                                <li><code>email</code> (Optional)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Skipped items summary -->
    @if (session('skipped_contacts'))
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white py-3 d-flex align-items-center">
                <i class="fas fa-exclamation-circle text-warning me-2 fs-5"></i>
                <h6 class="mb-0 fw-bold text-dark">Skipped Import Records Summary</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0 small">
                        <thead class="table-dark">
                            <tr>
                                <th class="ps-3" style="width: 100px;">Excel Row</th>
                                <th>Name / Reference</th>
                                <th>Reason for Skip</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (session('skipped_contacts') as $skipped)
                                <tr>
                                    <td class="fw-bold text-muted ps-3">Row {{ $skipped['row'] }}</td>
                                    <td class="fw-semibold text-dark">{{ $skipped['name'] }}</td>
                                    <td><span class="text-danger"><i class="fas fa-times-circle me-1"></i> {{ $skipped['reason'] }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
