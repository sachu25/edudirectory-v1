<x-app-layout>
    <x-slot name="header">
        Non-Academic Client Interactions
    </x-slot>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center py-3">
            <h6 class="mb-0 fw-bold">Manage Client Interactions</h6>
            <button class="btn btn-primary btn-sm" id="createNewInteraction">
                <i class="fas fa-plus"></i> Log Interaction
            </button>
        </div>
        <div class="card-body">
            <!-- Filter Options -->
            <div class="row mb-4 align-items-end g-3">
                <div class="col-md-3">
                    <label for="filter_client" class="form-label fw-semibold text-muted small text-uppercase">Filter by Client</label>
                    <select class="form-select select2-filter" id="filter_client">
                        <option value="">All Clients</option>
                        @foreach($clients as $cli)
                            <option value="{{ $cli->id }}" {{ request('non_academic_client_id') == $cli->id ? 'selected' : '' }}>{{ $cli->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filter_employee" class="form-label fw-semibold text-muted small text-uppercase">Filter by Employee</label>
                    <select class="form-select select2-filter" id="filter_employee">
                        <option value="">All Employees</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="filter_purpose" class="form-label fw-semibold text-muted small text-uppercase">Filter by Purpose</label>
                    <select class="form-select" id="filter_purpose">
                        <option value="">All Purposes</option>
                        <option value="Campus Placement">Campus Placement</option>
                        <option value="Student Internship">Student Internship</option>
                        <option value="MoU / Tie-up">MoU / Tie-up</option>
                        <option value="Consultancy & Projects">Consultancy & Projects</option>
                        <option value="Industrial Visit">Industrial Visit</option>
                        <option value="Sponsorship & Seminars">Sponsorship & Seminars</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="filter_status" class="form-label fw-semibold text-muted small text-uppercase">Filter by Status</label>
                    <select class="form-select" id="filter_status">
                        <option value="">All Statuses</option>
                        <option value="Interested">Interested</option>
                        <option value="Not Interested">Not Interested</option>
                        <option value="Follow-up">Follow-up</option>
                        <option value="Meeting Scheduled">Meeting Scheduled</option>
                        <option value="MoU Signed">MoU Signed</option>
                        <option value="Closed">Closed</option>
                    </select>
                </div>
                <div class="col-md-2">
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
                            <th>Client Name</th>
                            <th>Employee Name</th>
                            <th>Contact Date</th>
                            <th>Contact Mode</th>
                            <th>Purpose</th>
                            <th>Status</th>
                            <th width="120px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Interaction Modal -->
    <div class="modal fade" id="ajaxModel" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modelHeading">Log Interaction</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="interactionForm" name="interactionForm" class="form-horizontal">
                        <input type="hidden" name="interaction_id" id="interaction_id">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="non_academic_client_id" class="form-label fw-semibold">Non-Academic Client <span class="text-danger">*</span></label>
                                <select class="form-select" id="non_academic_client_id" name="non_academic_client_id" required>
                                    <option value="">Select Client</option>
                                    @foreach($clients as $cli)
                                        <option value="{{ $cli->id }}">{{ $cli->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="user_id" class="form-label fw-semibold">Contacted Employee <span class="text-danger">*</span></label>
                                <select class="form-select" id="user_id" name="user_id" required>
                                    <option value="">Select Employee</option>
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}" {{ auth()->id() == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="contact_date" class="form-label fw-semibold">Contact Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="contact_date" name="contact_date" value="{{ date('Y-m-d\TH:i') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label for="contact_mode" class="form-label fw-semibold">Contact Mode <span class="text-danger">*</span></label>
                                <select class="form-select" id="contact_mode" name="contact_mode" required>
                                    <option value="Email">Email</option>
                                    <option value="Phone Call">Phone Call</option>
                                    <option value="In-Person Visit">In-Person Visit</option>
                                    <option value="LinkedIn">LinkedIn</option>
                                    <option value="WhatsApp">WhatsApp</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="purpose" class="form-label fw-semibold">Purpose <span class="text-danger">*</span></label>
                                <select class="form-select" id="purpose" name="purpose" required>
                                    <option value="">Select Purpose</option>
                                    <option value="Campus Placement">Campus Placement</option>
                                    <option value="Student Internship">Student Internship</option>
                                    <option value="MoU / Tie-up">MoU / Tie-up</option>
                                    <option value="Consultancy & Projects">Consultancy & Projects</option>
                                    <option value="Industrial Visit">Industrial Visit</option>
                                    <option value="Sponsorship & Seminars">Sponsorship & Seminars</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="interaction_status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="interaction_status" name="interaction_status" required>
                                    <option value="Interested">Interested</option>
                                    <option value="Not Interested">Not Interested</option>
                                    <option value="Follow-up">Follow-up</option>
                                    <option value="Meeting Scheduled">Meeting Scheduled</option>
                                    <option value="MoU Signed">MoU Signed</option>
                                    <option value="Closed">Closed</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="next_followup_date" class="form-label fw-semibold">Next Follow-up Date</label>
                                <input type="date" class="form-control" id="next_followup_date" name="next_followup_date">
                            </div>

                            <div class="col-md-12">
                                <label for="client_response" class="form-label fw-semibold">Client Response</label>
                                <textarea class="form-control" id="client_response" name="client_response" rows="2" placeholder="What was the client's reaction/response?"></textarea>
                            </div>

                            <div class="col-md-12">
                                <label for="remarks" class="form-label fw-semibold">Remarks / Internal Notes</label>
                                <textarea class="form-control" id="remarks" name="remarks" rows="2" placeholder="Internal notes or next steps"></textarea>
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Save Interaction</button>
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

            var initialClientFilter = "{{ request('non_academic_client_id', '') }}";
            if (initialClientFilter) {
                $('#filter_client').val(initialClientFilter).trigger('change');
            }
            
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('non-academic-interactions.index') }}",
                    data: function (d) {
                        d.non_academic_client_id = $('#filter_client').val();
                        d.user_id = $('#filter_employee').val();
                        d.purpose = $('#filter_purpose').val();
                        d.interaction_status = $('#filter_status').val();
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'client_name', name: 'client.name'},
                    {data: 'employee_name', name: 'employee.name'},
                    {data: 'formatted_date', name: 'contact_date'},
                    {data: 'contact_mode', name: 'contact_mode'},
                    {data: 'purpose', name: 'purpose'},
                    {data: 'interaction_status', name: 'interaction_status'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });
            
            $('#filter_client, #filter_employee, #filter_purpose, #filter_status').on('change', function() {
                table.draw();
            });

            $('#resetFilterBtn').click(function() {
                $('#filter_client').val(null).trigger('change');
                $('#filter_employee').val(null).trigger('change');
                $('#filter_purpose').val('');
                $('#filter_status').val('');
                table.draw();
            });
            
            $('#createNewInteraction').click(function () {
                $('#saveBtn').val("create-interaction");
                $('#interaction_id').val('');
                $('#interactionForm').trigger("reset");
                $('#modelHeading').html("Log Non-Academic Interaction");
                $('#ajaxModel').modal('show');
            });
            
            $('body').on('click', '.editBtn', function () {
                var interaction_id = $(this).data('id');
                $.get("{{ route('non-academic-interactions.index') }}" +'/' + interaction_id +'/edit', function (data) {
                    $('#modelHeading').html("Edit Interaction Logs");
                    $('#saveBtn').val("edit-interaction");
                    $('#ajaxModel').modal('show');
                    
                    $('#interaction_id').val(data.id);
                    $('#non_academic_client_id').val(data.non_academic_client_id);
                    $('#user_id').val(data.user_id);
                    
                    // Format datetime-local input compatibility
                    if (data.contact_date) {
                        var date = new Date(data.contact_date);
                        var localDate = date.getFullYear() + '-' + 
                                       String(date.getMonth() + 1).padStart(2, '0') + '-' + 
                                       String(date.getDate()).padStart(2, '0') + 'T' + 
                                       String(date.getHours()).padStart(2, '0') + ':' + 
                                       String(date.getMinutes()).padStart(2, '0');
                        $('#contact_date').val(localDate);
                    }
                    
                    $('#contact_mode').val(data.contact_mode);
                    $('#purpose').val(data.purpose);
                    $('#interaction_status').val(data.interaction_status);
                    $('#next_followup_date').val(data.next_followup_date);
                    $('#client_response').val(data.client_response);
                    $('#remarks').val(data.remarks);
                })
            });
            
            $('#saveBtn').click(function (e) {
                e.preventDefault();
                $(this).html('Saving...');
            
                $.ajax({
                    data: $('#interactionForm').serialize(),
                    url: "{{ route('non-academic-interactions.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        $('#interactionForm').trigger("reset");
                        $('#ajaxModel').modal('hide');
                        table.draw();
                        $('#saveBtn').html('Save Interaction');
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
                        $('#saveBtn').html('Save Interaction');
                        
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
                var interaction_id = $(this).data("id");
                
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
                            url: "{{ route('non-academic-interactions.index') }}"+'/'+interaction_id,
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
