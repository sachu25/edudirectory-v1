<x-app-layout>
    <x-slot name="header">
        Contact Person Management
    </x-slot>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center py-3">
            <h6 class="mb-0 fw-bold">Manage Contacts</h6>
            <div class="d-flex gap-2">
                @can('contacts.view')
                <a href="#" class="btn btn-outline-success btn-sm" id="btnExportExcel">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
                @endcan
                @can('contacts.create')
                <button class="btn btn-outline-primary btn-sm" id="btnBulkImport">
                    <i class="fas fa-file-upload"></i> Bulk Import
                </button>
                <button class="btn btn-primary btn-sm" id="createNewContact">
                    <i class="fas fa-plus"></i> Add New Contact
                </button>
                @endcan
            </div>
        </div>
        <div class="card-body">
            @if(session('skipped_contacts'))
                <div class="alert alert-warning alert-dismissible fade show mb-4 border-0 shadow" role="alert">
                    <h6 class="fw-bold mb-2"><i class="fas fa-exclamation-triangle me-2 text-warning"></i> Some rows were skipped during import:</h6>
                    <div style="max-height: 200px; overflow-y: auto;">
                        <table class="table table-sm table-hover align-middle mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th width="100px">Row Number</th>
                                    <th>Contact Name</th>
                                    <th>Reason / Resolution</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(session('skipped_contacts') as $skipped)
                                    <tr>
                                        <td><span class="badge bg-secondary">Row {{ $skipped['row'] }}</span></td>
                                        <td class="fw-medium text-dark">{{ $skipped['name'] }}</td>
                                        <td class="text-muted"><i class="fas fa-info-circle text-info me-1"></i> {{ $skipped['reason'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Filter Options -->
            <div class="row mb-4 align-items-end g-3">
                <div class="col-xl-5 col-lg-5 col-md-6 col-12">
                    <label for="filter_college_id" class="form-label fw-semibold text-muted small text-uppercase">Filter by Institution</label>
                    <select class="form-select select2-filter" id="filter_college_id" data-placeholder="All Institutions">
                        <option value="">All Institutions</option>
                        @foreach($colleges as $college)
                            <option value="{{ $college->id }}">{{ $college->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-4 col-lg-4 col-md-6 col-12">
                    <label for="filter_designation_id" class="form-label fw-semibold text-muted small text-uppercase">Filter by Designation</label>
                    <select class="form-select select2-filter" id="filter_designation_id" data-placeholder="All Designations">
                        <option value="">All Designations</option>
                        @foreach($designations as $designation)
                            <option value="{{ $designation->id }}">{{ $designation->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-6 col-12">
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
                            <th>Institution</th>
                            <th>Name</th>
                            <th>Designation</th>
                            <th>Department</th>
                            <th>Mobile</th>
                            <th>Status</th>
                            <th width="100px">Action</th>
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
                    <h5 class="modal-title"><i class="fas fa-file-upload me-2"></i> Bulk Import Contacts</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6 border-end">
                            <h6 class="fw-bold mb-3">Upload File</h6>
                            <p class="text-muted small mb-4">Select an Excel or CSV file to bulk import contact records.</p>
                            
                            <form action="{{ route('contacts.import') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-4">
                                    <label for="import_file" class="form-label fw-semibold">Select File (Excel/CSV) <span class="text-danger">*</span></label>
                                    <input class="form-control" type="file" id="import_file" name="import_file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                                    <div class="form-text small mb-3">Maximum size: 5MB</div>
                                    <a href="{{ route('contacts.download-template') }}" class="btn btn-sm btn-light border w-100 text-start py-2">
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
                                    <li><i class="fas fa-check-circle text-success me-1"></i> <code>contact_name</code> <span class="text-danger">*</span></li>
                                    <li><i class="fas fa-check-circle text-success me-1"></i> <code>designation</code> <span class="text-danger">*</span></li>
                                    <li><i class="fas fa-check-circle text-success me-1"></i> <code>department</code></li>
                                    <li><i class="fas fa-check-circle text-success me-1"></i> <code>mobile</code></li>
                                    <li><i class="fas fa-check-circle text-success me-1"></i> <code>email</code></li>
                                </ul>
                            </div>
                            <div class="alert alert-warning border-0 small mt-3 p-2 mb-0" style="font-size: 0.75rem;">
                                <i class="fas fa-exclamation-triangle me-1"></i> If the <strong>college_name</strong> does not match an existing institution, that row will be skipped. Designations will be created automatically.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="ajaxModel" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modelHeading"></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="contactForm" name="contactForm" class="form-horizontal">
                        <input type="hidden" name="contact_id" id="contact_id">
                        
                        <div class="mb-3">
                            <label for="college_id" class="form-label fw-semibold">Institution <span class="text-danger">*</span></label>
                            <select class="form-select" id="college_id" name="college_id" required>
                                <option value="">Select Institution</option>
                                @foreach($colleges as $college)
                                    <option value="{{ $college->id }}">{{ $college->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Contact Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter Full Name" maxlength="255" required>
                        </div>
        
                        <div class="mb-3">
                            <label for="designation_id" class="form-label fw-semibold">Designation <span class="text-danger">*</span></label>
                            <select class="form-select" id="designation_id" name="designation_id" required>
                                <option value="">Select Designation</option>
                                @foreach($designations as $designation)
                                    <option value="{{ $designation->id }}">{{ $designation->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="department" class="form-label fw-semibold">Department</label>
                            <input type="text" class="form-control" id="department" name="department" placeholder="e.g. Computer Science" maxlength="255">
                        </div>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="mobile" class="form-label fw-semibold">Mobile Number</label>
                                <input type="text" class="form-control" id="mobile" name="mobile" placeholder="e.g. 9876543210" maxlength="20">
                            </div>
                            <div class="col-md-6">
                                <label for="whatsapp_number" class="form-label fw-semibold">WhatsApp Number</label>
                                <input type="text" class="form-control" id="whatsapp_number" name="whatsapp_number" placeholder="e.g. 9876543210" maxlength="20">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="email@example.com" maxlength="255">
                        </div>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_primary" name="is_primary" value="1">
                            <label class="form-check-label fw-semibold" for="is_primary">Is Primary Contact?</label>
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
            $('.select2-filter').each(function() {
                $(this).select2({
                    theme: 'bootstrap-5',
                    placeholder: $(this).data('placeholder') || 'Select Option',
                    allowClear: true
                });
            });

            // Initialize Select2 inside create/edit modal
            $('#college_id').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#ajaxModel')
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
                    url: "{{ route('contacts.index') }}",
                    data: function (d) {
                        d.college_id = $('#filter_college_id').val();
                        d.designation_id = $('#filter_designation_id').val();
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'college', name: 'college.name'},
                    {data: 'name', name: 'name'},
                    {data: 'designation', name: 'designation.name'},
                    {data: 'department', name: 'department'},
                    {data: 'mobile', name: 'mobile'},
                    {data: 'status_badge', name: 'status'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                language: {
                    paginate: {
                        previous: '<i class="fas fa-chevron-left"></i>',
                        next: '<i class="fas fa-chevron-right"></i>'
                    }
                }
            });
            
            // Redraw table when filter changes
            $('#filter_college_id, #filter_designation_id').on('change', function() {
                table.draw();
            });

            // Reset filter and redraw
            $('#resetFilterBtn').click(function() {
                $('#filter_college_id').val(null).trigger('change');
                $('#filter_designation_id').val(null).trigger('change');
            });

            // Export Excel
            $('#btnExportExcel').click(function(e) {
                e.preventDefault();
                
                var collegeId = $('#filter_college_id').val() || '';
                var designationId = $('#filter_designation_id').val() || '';
                
                var order = table.order();
                var sortColIndex = order[0] ? order[0][0] : '';
                var sortDir = order[0] ? order[0][1] : '';
                
                var sortColumn = '';
                if (sortColIndex !== '') {
                    sortColumn = table.settings().init().columns[sortColIndex].name || '';
                }
                
                var search = table.search() || '';
                
                var exportUrl = "{{ route('contacts.export') }}?" + $.param({
                    college_id: collegeId,
                    designation_id: designationId,
                    search: search,
                    sort_column: sortColumn,
                    sort_direction: sortDir
                });
                
                window.location.href = exportUrl;
            });
            
            $('#btnBulkImport').click(function () {
                $('#bulkImportModal').modal('show');
            });

            $('#createNewContact').click(function () {
                $('#saveBtn').val("create-contact");
                $('#contact_id').val('');
                $('#contactForm').trigger("reset");
                $('#college_id').val('').trigger('change');
                $('#modelHeading').html("Create New Contact");
                $('#ajaxModel').modal('show');
            });
            
            $('body').on('click', '.editBtn', function () {
                var contact_id = $(this).data('id');
                $.get("{{ route('contacts.index') }}" +'/' + contact_id +'/edit', function (data) {
                    $('#modelHeading').html("Edit Contact Person");
                    $('#saveBtn').val("edit-contact");
                    $('#ajaxModel').modal('show');
                    $('#contact_id').val(data.id);
                    $('#college_id').val(data.college_id).trigger('change');
                    $('#name').val(data.name);
                    $('#designation_id').val(data.designation_id);
                    $('#department').val(data.department);
                    $('#mobile').val(data.mobile);
                    $('#whatsapp_number').val(data.whatsapp);
                    $('#email').val(data.email);
                    $('#status').val(data.status);
                    
                    if(data.is_priority) {
                        $('#is_primary').prop('checked', true);
                    } else {
                        $('#is_primary').prop('checked', false);
                    }
                })
            });
            
            $('#saveBtn').click(function (e) {
                e.preventDefault();
                $(this).html('Saving...');
            
                $.ajax({
                    data: $('#contactForm').serialize(),
                    url: "{{ route('contacts.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        $('#contactForm').trigger("reset");
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
                var contact_id = $(this).data("id");
                
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
                            url: "{{ route('contacts.index') }}"+'/'+contact_id,
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
