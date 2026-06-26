<x-app-layout>
    <x-slot name="header">
        Non-Academic Client Master
    </x-slot>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center py-3">
            <h6 class="mb-0 fw-bold">Manage Non-Academic Clients</h6>
            <button class="btn btn-primary btn-sm" id="createNewClient">
                <i class="fas fa-plus"></i> Add New Client
            </button>
        </div>
        <div class="card-body">
            <!-- Filter Options -->
            <div class="row mb-4 align-items-end g-3">
                <div class="col-md-4">
                    <label for="filter_industry" class="form-label fw-semibold text-muted small text-uppercase">Filter by Industry</label>
                    <select class="form-select select2-filter" id="filter_industry">
                        <option value="">All Industries</option>
                        @foreach($industries as $ind)
                            <option value="{{ $ind }}">{{ $ind }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="filter_employee" class="form-label fw-semibold text-muted small text-uppercase">Filter by Contacted Employee</label>
                    <select class="form-select select2-filter" id="filter_employee">
                        <option value="">All Employees</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 col-lg-2">
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
                            <th>Company Name</th>
                            <th>Industry</th>
                            <th>Contact Person</th>
                            <th>Designation</th>
                            <th>Contacted Employee</th>
                            <th>Contact Reason</th>
                            <th width="120px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Client Modal -->
    <div class="modal fade" id="ajaxModel" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modelHeading"></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" style="max-height: 75vh; overflow-y: auto;">
                    <form id="clientForm" name="clientForm" class="form-horizontal">
                        <input type="hidden" name="client_id" id="client_id">
                        
                        <div class="row g-4">
                            <!-- Section 1: Client Information -->
                            <div class="col-12">
                                <div class="d-flex align-items-center mb-2 pb-1 border-bottom">
                                    <span class="badge bg-primary me-2"><i class="fas fa-building"></i></span>
                                    <h6 class="mb-0 fw-bold text-primary">1. Client / Company Information</h6>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold">Company Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter Company Name" required>
                            </div>

                            <div class="col-md-3">
                                <label for="industry" class="form-label fw-semibold">Industry / Sector</label>
                                <select class="form-select" id="industry" name="industry">
                                    <option value="">Select Industry</option>
                                    <option value="IT / Software">IT / Software</option>
                                    <option value="Finance & Banking">Finance & Banking</option>
                                    <option value="Healthcare & Pharma">Healthcare & Pharma</option>
                                    <option value="Manufacturing & Core">Manufacturing & Core</option>
                                    <option value="Retail & E-commerce">Retail & E-commerce</option>
                                    <option value="Consulting & Services">Consulting & Services</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="phone" class="form-label fw-semibold">Office Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone" placeholder="e.g. +91 9876543210">
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">Official Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="info@company.com">
                            </div>

                            <div class="col-md-6">
                                <label for="website" class="form-label fw-semibold">Website</label>
                                <input type="text" class="form-control" id="website" name="website" placeholder="https://www.company.com">
                            </div>

                            <div class="col-md-12">
                                <label for="address" class="form-label fw-semibold">Office Address</label>
                                <textarea class="form-control" id="address" name="address" rows="2" placeholder="Full Company Address"></textarea>
                            </div>

                            <div class="col-md-12">
                                <label for="remarks" class="form-label fw-semibold">Remarks / Notes</label>
                                <textarea class="form-control" id="remarks" name="remarks" rows="2" placeholder="General client notes"></textarea>
                            </div>

                            <!-- Section 2: Contact Person Details -->
                            <div class="col-12 mt-4">
                                <div class="d-flex align-items-center mb-2 pb-1 border-bottom">
                                    <span class="badge bg-secondary me-2"><i class="fas fa-user-tie"></i></span>
                                    <h6 class="mb-0 fw-bold text-secondary">2. Primary Contact Person</h6>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="contact_person_name" class="form-label fw-semibold">Contact Person Name</label>
                                <input type="text" class="form-control" id="contact_person_name" name="contact_person_name" placeholder="Enter Contact Person's Name">
                            </div>

                            <div class="col-md-6">
                                <label for="contact_person_designation" class="form-label fw-semibold">Designation</label>
                                <input type="text" class="form-control" id="contact_person_designation" name="contact_person_designation" placeholder="e.g. HR Manager, Talent Acquisition">
                            </div>

                            <div class="col-md-6">
                                <label for="contact_person_email" class="form-label fw-semibold">Contact Person Email</label>
                                <input type="email" class="form-control" id="contact_person_email" name="contact_person_email" placeholder="personal@company.com">
                            </div>

                            <div class="col-md-6">
                                <label for="contact_person_phone" class="form-label fw-semibold">Contact Person Mobile</label>
                                <input type="text" class="form-control" id="contact_person_phone" name="contact_person_phone" placeholder="e.g. +91 9999988888">
                            </div>
                        </div>
        
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Save Client</button>
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
                    url: "{{ route('non-academic-clients.index') }}",
                    data: function (d) {
                        d.industry = $('#filter_industry').val();
                        d.contacted_user_id = $('#filter_employee').val();
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'industry', name: 'industry'},
                    {data: 'contact_person_name', name: 'contact_person_name', defaultContent: 'N/A'},
                    {data: 'contact_person_designation', name: 'contact_person_designation', defaultContent: 'N/A'},
                    {data: 'contacted_employee_name', name: 'contacted_employee_name'},
                    {data: 'contact_reason', name: 'contact_reason', defaultContent: 'N/A'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });
            
            $('#filter_industry, #filter_employee').on('change', function() {
                table.draw();
            });

            $('#resetFilterBtn').click(function() {
                $('#filter_industry').val(null).trigger('change');
                $('#filter_employee').val(null).trigger('change');
            });
            
            $('#createNewClient').click(function () {
                $('#saveBtn').val("create-client");
                $('#client_id').val('');
                $('#clientForm').trigger("reset");
                $('#modelHeading').html("Add New Non-Academic Client");
                $('#ajaxModel').modal('show');
            });
            
            $('body').on('click', '.editBtn', function () {
                var client_id = $(this).data('id');
                $.get("{{ route('non-academic-clients.index') }}" +'/' + client_id +'/edit', function (data) {
                    $('#modelHeading').html("Edit Non-Academic Client");
                    $('#saveBtn').val("edit-client");
                    $('#ajaxModel').modal('show');
                    
                    $('#client_id').val(data.id);
                    $('#name').val(data.name);
                    $('#industry').val(data.industry);
                    $('#phone').val(data.phone);
                    $('#email').val(data.email);
                    $('#website').val(data.website);
                    $('#address').val(data.address);
                    
                    $('#contact_person_name').val(data.contact_person_name);
                    $('#contact_person_designation').val(data.contact_person_designation);
                    $('#contact_person_email').val(data.contact_person_email);
                    $('#contact_person_phone').val(data.contact_person_phone);
                    
                    $('#remarks').val(data.remarks);
                })
            });
            
            $('#saveBtn').click(function (e) {
                e.preventDefault();
                $(this).html('Saving...');
            
                $.ajax({
                    data: $('#clientForm').serialize(),
                    url: "{{ route('non-academic-clients.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        $('#clientForm').trigger("reset");
                        $('#ajaxModel').modal('hide');
                        table.draw();
                        $('#saveBtn').html('Save Client');
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
                        $('#saveBtn').html('Save Client');
                        
                        var errorMsg = 'An error occurred. Please check your inputs.';
                        if (data.responseJSON && data.responseJSON.errors) {
                            errorMsg = '';
                            $.each(data.responseJSON.errors, function(key, value) {
                                errorMsg += value[0] + '<br>';
                            });
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
                var client_id = $(this).data("id");
                
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
                            url: "{{ route('non-academic-clients.index') }}"+'/'+client_id,
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
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Something went wrong!',
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
