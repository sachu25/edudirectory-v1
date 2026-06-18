<x-app-layout>
    <x-slot name="header">
        Import Data
    </x-slot>

    <div class="row">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-upload text-primary me-2"></i> Import Colleges</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">Upload an Excel or CSV file to bulk import college records. The file must follow the required format.</p>
                    
                    <form action="{{ route('imports.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label for="import_file" class="form-label fw-semibold">Select File (Excel/CSV)</label>
                            <input class="form-control" type="file" id="import_file" name="import_file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                            <div class="form-text">Maximum file size: 5MB</div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary"><i class="fas fa-file-import me-2"></i> Import Data</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100 bg-light border-0">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="fas fa-info-circle text-info me-2"></i> File Format Instructions</h6>
                    <p class="small text-muted">Please ensure your Excel or CSV file contains a header row with the following exact column names (order does not matter):</p>
                    
                    <ul class="small mb-4 text-muted">
                        <li><code>college_name</code> (Required)</li>
                        <li><code>type</code> (Required: Affiliated, Autonomous, etc.)</li>
                        <li><code>university_name</code> (Required: Must match existing University)</li>
                    </ul>
                    
                    <div class="alert alert-warning border-0 small mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i> If the <strong>university_name</strong> does not match an existing university in the system, that row will be skipped.
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
