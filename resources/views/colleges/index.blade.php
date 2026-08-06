<x-app-layout>
    <x-slot name="header">
        Institution Master
    </x-slot>

    @if (session('import_failures'))
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-danger text-white py-3 d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-2 fs-5"></i>
                <h6 class="mb-0 fw-bold">Import Validation Failures Summary</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0 small">
                        <thead class="table-dark">
                            <tr>
                                <th class="ps-3" style="width: 100px;">Excel Row</th>
                                <th>Column/Field</th>
                                <th>Error Message</th>
                                <th>Value Entered</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (session('import_failures') as $failure)
                                <tr>
                                    <td class="fw-bold text-muted ps-3">Row {{ $failure->row() }}</td>
                                    <td class="fw-semibold text-danger">{{ $failure->attribute() }}</td>
                                    <td>{{ $failure->errors()[0] }}</td>
                                    <td><code class="bg-light p-1 rounded text-dark">{{ is_array($failure->values()[$failure->attribute()] ?? '') ? json_encode($failure->values()[$failure->attribute()]) : ($failure->values()[$failure->attribute()] ?? 'N/A') }}</code></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center py-3">
            <h6 class="mb-0 fw-bold">Manage Institutions</h6>
            <div class="d-flex gap-2">
                @can('colleges.create')
                <button class="btn btn-outline-primary btn-sm" id="btnBulkImport">
                    <i class="fas fa-file-upload"></i> Bulk Import
                </button>
                <button class="btn btn-primary btn-sm" id="createNewCollege">
                    <i class="fas fa-plus"></i> Add New Institution
                </button>
                @endcan
            </div>
        </div>
        <div class="card-body">
            <!-- Filter Options -->
            <div class="row mb-4 align-items-end g-3">
                <div class="col-xl-2 col-lg-4 col-md-6 col-12">
                    <label for="filter_type" class="form-label fw-semibold text-muted small text-uppercase">Filter by Type</label>
                    <select class="form-select select2-filter" id="filter_type">
                        <option value="">All Types</option>
                        <option value="Affiliated">Affiliated</option>
                        <option value="Autonomous">Autonomous</option>
                        <option value="Constituent">Constituent</option>
                        <option value="Deemed">Deemed</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="col-xl-2 col-lg-4 col-md-6 col-12">
                    <label for="filter_is_university" class="form-label fw-semibold text-muted small text-uppercase">Filter by Category</label>
                    <select class="form-select select2-filter" id="filter_is_university">
                        <option value="">All Categories</option>
                        <option value="yes">Universities Only</option>
                        <option value="no">Colleges Only</option>
                    </select>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 col-12">
                    <label for="filter_university_id" class="form-label fw-semibold text-muted small text-uppercase">Filter by Affiliated University</label>
                    <select class="form-select select2-filter" id="filter_university_id">
                        <option value="">All Universities</option>
                        @foreach($universities as $uni)
                            <option value="{{ $uni->id }}">{{ $uni->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 col-12">
                    <label for="filter_state" class="form-label fw-semibold text-muted small text-uppercase">Filter by State</label>
                    <select class="form-select select2-filter" id="filter_state">
                        <option value="">All States</option>
                        @foreach($states as $st)
                            <option value="{{ $st }}">{{ $st }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-lg-4 col-md-6 col-12">
                    <button class="btn btn-outline-secondary w-100 py-2" id="resetFilterBtn">
                        <i class="fas fa-undo me-1"></i> Reset
                    </button>
                </div>
            </div>
            <hr class="mb-4" style="opacity: 0.1;">

            <div class="table-responsive">
                <table class="table table-hover table-bordered data-table w-100">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Code</th>
                            <th>Institution Name</th>
                            <th>Category</th>
                            <th>Affiliated University</th>
                            <th>Type</th>
                            <th>State</th>
                            <th width="120px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bulk Import Modal -->
    <div class="modal fade" id="bulkImportModal" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-file-upload me-2"></i> Bulk Import Institutions</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6 border-end">
                            <h6 class="fw-bold mb-3">Upload File</h6>
                            <p class="text-muted small mb-4">Select an Excel or CSV file to bulk import institution records.</p>
                            
                            <form action="{{ route('imports.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-4">
                                    <label for="import_file" class="form-label fw-semibold">Select File (Excel/CSV) <span class="text-danger">*</span></label>
                                    <input class="form-control" type="file" id="import_file" name="import_file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                                    <div class="form-text small mb-3">Maximum size: 5MB</div>
                                    <a href="{{ route('colleges.download-template') }}" class="btn btn-sm btn-light border w-100 text-start py-2">
                                        <i class="fas fa-file-download text-success me-2"></i> Download Sample Excel Template
                                    </a>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary py-2"><i class="fas fa-file-import me-2"></i> Upload & Import</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3 text-info"><i class="fas fa-info-circle me-1"></i> File Format Instructions</h6>
                            <p class="small text-muted mb-2">Ensure your file has a header row with the exact column names:</p>
                            <div class="bg-light p-3 rounded border small text-muted">
                                <ul class="list-unstyled mb-0">
                                    <li><i class="fas fa-check-circle text-success me-1"></i> <code>college_name</code> <span class="text-danger">*</span></li>
                                    <li><i class="fas fa-check-circle text-success me-1"></i> <code>state</code> <span class="text-danger">*</span></li>
                                    <li><i class="fas fa-check-circle text-success me-1"></i> <code>type</code></li>
                                    <li><i class="fas fa-check-circle text-success me-1"></i> <code>affiliated_university</code></li>
                                </ul>
                            </div>
                            <div class="alert alert-info border-0 small mt-3 p-2 mb-0" style="font-size: 0.75rem;">
                                <i class="fas fa-info-circle me-1"></i> Any institution name containing the word "university" will automatically be categorized as a University.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="ajaxModel" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modelHeading"></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" style="max-height: 75vh; overflow-y: auto;">
                    <form id="collegeForm" name="collegeForm" class="form-horizontal">
                        <input type="hidden" name="college_id" id="college_id">
                        
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="name" class="form-label fw-semibold">Institution Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter Institution Name" maxlength="255" required>
                            </div>

                            <div class="col-md-3">
                                <label for="code" class="form-label fw-semibold">Institution Code</label>
                                <input type="text" class="form-control" id="code" name="code" placeholder="e.g. C-12345" maxlength="50">
                            </div>

                            <div class="col-md-3">
                                <label for="type" class="form-label fw-semibold">Type</label>
                                <select class="form-select" id="type" name="type">
                                    <option value="">Select Type</option>
                                    <option value="Affiliated">Affiliated</option>
                                    <option value="Autonomous">Autonomous</option>
                                    <option value="Constituent">Constituent</option>
                                    <option value="Deemed">Deemed</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                             <div class="col-md-3">
                                <label for="university_id" class="form-label fw-semibold">Affiliated University</label>
                                <select class="form-select" id="university_id" name="university_id">
                                    <option value="">Select University</option>
                                    @foreach($universities as $uni)
                                        <option value="{{ $uni->id }}">{{ $uni->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold d-block">Category</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="is_university" name="is_university" value="1">
                                    <label class="form-check-label fw-semibold" for="is_university">Is University?</label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label for="naac_grade" class="form-label fw-semibold">NAAC Grade</label>
                                <input type="text" class="form-control" id="naac_grade" name="naac_grade" placeholder="e.g. A++">
                            </div>

                            <div class="col-md-3">
                                <label for="nirf_ranking" class="form-label fw-semibold">NIRF Ranking</label>
                                <input type="number" class="form-control" id="nirf_ranking" name="nirf_ranking" placeholder="e.g. 50">
                            </div>

                            <div class="col-md-3">
                                <label for="established_year" class="form-label fw-semibold">Established Year</label>
                                <input type="number" class="form-control" id="established_year" name="established_year" placeholder="e.g. 1995" min="1800" max="{{ date('Y') }}">
                            </div>
                            
                            <div class="col-md-3">
                                <label for="student_strength" class="form-label fw-semibold">Student Strength</label>
                                <input type="number" class="form-control" id="student_strength" name="student_strength" placeholder="e.g. 2500">
                            </div>

                            <div class="col-md-6">
                                <label for="official_email" class="form-label fw-semibold">Official Email</label>
                                <input type="email" class="form-control" id="official_email" name="official_email" placeholder="info@institution.edu">
                            </div>

                            <div class="col-md-6">
                                <label for="website" class="form-label fw-semibold">Website</label>
                                <input type="text" class="form-control" id="website" name="website" placeholder="https://www.institution.edu">
                            </div>

                            <div class="col-md-6">
                                <label for="office_phone" class="form-label fw-semibold">Office Phone</label>
                                <input type="text" class="form-control" id="office_phone" name="office_phone" placeholder="e.g. 022-12345678">
                            </div>

                            <div class="col-md-6">
                                <label for="office_mobile" class="form-label fw-semibold">Office Mobile</label>
                                <input type="text" class="form-control" id="office_mobile" name="office_mobile" placeholder="e.g. +91 9876543210">
                            </div>

                            <div class="col-md-12">
                                <label for="address" class="form-label fw-semibold">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="2" placeholder="Full Address"></textarea>
                            </div>

                            <div class="col-md-3">
                                <label for="district" class="form-label fw-semibold">District/City</label>
                                <input type="text" class="form-control" id="district" name="district">
                            </div>

                            <div class="col-md-3">
                                <label for="state" class="form-label fw-semibold">State <span class="text-danger">*</span></label>
                                <select class="form-select" id="state" name="state" required>
                                    <option value="">Select State</option>
                                    @foreach($states as $st)
                                        <option value="{{ $st }}">{{ $st }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="pin_code" class="form-label fw-semibold">PIN Code</label>
                                <input type="text" class="form-control" id="pin_code" name="pin_code">
                            </div>

                            <div class="col-md-3">
                                <label for="country" class="form-label fw-semibold">Country</label>
                                <input type="text" class="form-control" id="country" name="country" value="India">
                            </div>



                            <div class="col-md-6">
                                <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="fdp_client" class="form-label fw-semibold">FDP Client?</label>
                                <select class="form-select" id="fdp_client" name="fdp_client">
                                    <option value="No">No</option>
                                    <option value="Yes">Yes</option>
                                </select>
                            </div>



                            <div class="col-md-12">
                                <label for="remarks" class="form-label fw-semibold">Remarks</label>
                                <textarea class="form-control" id="remarks" name="remarks" rows="2"></textarea>
                            </div>
                        </div>
        
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script type="text/javascript">
        $(function () {
            
            // Initialize Select2 filter
            $('.select2-filter').select2({
                theme: 'bootstrap-5',
                allowClear: true
            });
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('colleges.index') }}",
                    data: function (d) {
                        d.type = $('#filter_type').val();
                        d.is_university = $('#filter_is_university').val();
                        d.university_id = $('#filter_university_id').val();
                        d.state = $('#filter_state').val();
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'code', name: 'code'},
                    {data: 'name', name: 'name'},
                    {data: 'is_university', name: 'is_university', render: function(data) {
                        return data ? '<span class="badge bg-primary">University</span>' : '<span class="badge bg-secondary">Institution</span>';
                    }},
                    {data: 'affiliated_university', name: 'affiliated_university'},
                    {data: 'type', name: 'type'},
                    {data: 'state', name: 'state'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                language: {
                    paginate: {
                        previous: '<i class="fas fa-chevron-left"></i>',
                        next: '<i class="fas fa-chevron-right"></i>'
                    }
                }
            });
            
            // Redraw table when filters change
            $('#filter_type, #filter_is_university, #filter_university_id, #filter_state').on('change', function() {
                table.draw();
            });

            // Reset filters and redraw
            $('#resetFilterBtn').click(function() {
                $('#filter_type').val(null).trigger('change');
                $('#filter_is_university').val(null).trigger('change');
                $('#filter_university_id').val(null).trigger('change');
                $('#filter_state').val(null).trigger('change');
            });
            
            $('#createNewCollege').click(function () {
                $('#saveBtn').val("create-college");
                $('#college_id').val('');
                $('#collegeForm').trigger("reset");
                $('#is_university').prop('checked', false);
                $('#fdp_client').val('No');
                $('#modelHeading').html("Create New Institution");
                $('#ajaxModel').modal('show');
            });

            $('#btnBulkImport').click(function () {
                $('#bulkImportModal').modal('show');
            });
            
            $('body').on('click', '.editBtn', function () {
                var college_id = $(this).data('id');
                $.get("{{ route('colleges.index') }}" +'/' + college_id +'/edit', function (data) {
                    $('#modelHeading').html("Edit Institution");
                    $('#saveBtn').val("edit-college");
                    $('#ajaxModel').modal('show');
                    
                    $('#college_id').val(data.id);
                    $('#name').val(data.name);
                    $('#code').val(data.code);
                    $('#type').val(data.type);
                    $('#university_id').val(data.university_id);
                    $('#status').val(data.status || 'active');
                    $('#fdp_client').val(data.fdp_client || 'No');
                    $('#naac_grade').val(data.naac_grade);
                    $('#nirf_ranking').val(data.nirf_ranking);
                    $('#established_year').val(data.established_year);
                    $('#website').val(data.website);
                    $('#address').val(data.address);
                    $('#district').val(data.district);
                    $('#state').val(data.state);
                    $('#country').val(data.country);
                    $('#pin_code').val(data.pin_code);
                    $('#office_phone').val(data.office_phone);
                    $('#office_mobile').val(data.office_mobile);
                    $('#official_email').val(data.official_email);
                    $('#student_strength').val(data.student_strength);
                    $('#remarks').val(data.remarks);
                    
                    if(data.is_university) {
                        $('#is_university').prop('checked', true);
                    } else {
                        $('#is_university').prop('checked', false);
                    }
                })
            });
            
            $('#saveBtn').click(function (e) {
                e.preventDefault();
                $(this).html('Saving...');
            
                $.ajax({
                    data: $('#collegeForm').serialize(),
                    url: "{{ route('colleges.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        $('#collegeForm').trigger("reset");
                        $('#ajaxModel').modal('hide');
                        table.draw();
                        $('#saveBtn').html('Save Changes');
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: data.success,
                            showConfirmButton: false,
                            timer: 3000
                        });
                    },
                    error: function (data) {
                        console.log('Error:', data);
                        $('#saveBtn').html('Save Changes');
                        
                        var errorMsg = 'An error occurred. Please check your inputs.';
                        if (data.responseJSON && data.responseJSON.errors) {
                            errorMsg = '';
                            $.each(data.responseJSON.errors, function(key, value) {
                                errorMsg += value[0] + '<br>';
                            });
                        } else if (data.responseJSON && data.responseJSON.message) {
                            errorMsg = data.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            html: errorMsg,
                        });
                    }
                });
            });
            
            $('body').on('click', '.deleteBtn', function () {
                var college_id = $(this).data("id");
                
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "DELETE",
                            url: "{{ route('colleges.index') }}"+'/'+college_id,
                            success: function (data) {
                                table.draw();
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'success',
                                    title: 'Deleted successfully.',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                            },
                            error: function (data) {
                                console.log('Error:', data);
                                var errorMsg = 'Something went wrong!';
                                if(data.status === 403) {
                                    errorMsg = "Unauthorized action.";
                                }
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: errorMsg,
                                });
                            }
                        });
                    }
                });
            });
            
        });
    </script>
    @endpush
</x-app-layout>
