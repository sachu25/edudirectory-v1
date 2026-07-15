<x-app-layout>
    <x-slot name="header">
        Interactions Management
    </x-slot>

    <!-- Include Select2 CSS -->
    @push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    @endpush

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center py-3">
            <h6 class="mb-0 fw-bold">Manage Interactions</h6>
            @can('interactions.create')
            <button class="btn btn-primary btn-sm" id="createNewInteraction">
                <i class="fas fa-plus"></i> Log Interaction
            </button>
            @endcan
        </div>
        <div class="card-body">
            <!-- Filter Row -->
            <div class="row mb-4 align-items-end g-3">
                <div class="col-md-5 col-lg-4">
                    <label for="filter_college_id" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem; text-uppercase; letter-spacing: 0.5px;">Filter by Institution</label>
                    <select class="form-select" id="filter_college_id">
                        <option value="">All Institutions</option>
                        @foreach($colleges as $college)
                            <option value="{{ $college->id }}">{{ $college->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5 col-lg-4">
                    <label for="filter_user_id" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem; text-uppercase; letter-spacing: 0.5px;">Filter by Contacted By</label>
                    <select class="form-select" id="filter_user_id">
                        <option value="">All Staff</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-lg-2">
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
                            <th>Date</th>
                            <th>Institution</th>
                            <th>Contact Person</th>
                            <th>Mode</th>
                            <th>Purposes</th>
                            <th>Status</th>
                            <th>Staff</th>
                            <th width="100px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="ajaxModel" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modelHeading"></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="interactionForm" name="interactionForm" class="form-horizontal">
                        <input type="hidden" name="interaction_id" id="interaction_id">
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="college_id" class="form-label fw-semibold">Institution <span class="text-danger">*</span></label>
                                <select class="form-select" id="college_id" name="college_id" required>
                                    <option value="">Select Institution</option>
                                    @foreach($colleges as $college)
                                        <option value="{{ $college->id }}">{{ $college->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="contact_person_id" class="form-label fw-semibold">Contact Person</label>
                                <select class="form-select" id="contact_person_id" name="contact_person_id">
                                    <option value="">Select Contact Person</option>
                                    <!-- Populated via AJAX -->
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="user_id" class="form-label fw-semibold">Contacted By (Staff) <span class="text-danger">*</span></label>
                                <select class="form-select" id="user_id" name="user_id" required>
                                    <option value="">Select Staff</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ auth()->id() == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="contact_date" class="form-label fw-semibold">Contact Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="contact_date" name="contact_date" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="contact_mode_id" class="form-label fw-semibold">Mode of Contact <span class="text-danger">*</span></label>
                                <select class="form-select" id="contact_mode_id" name="contact_mode_id" required>
                                    <option value="">Select Mode</option>
                                    @foreach($contactModes as $mode)
                                        <option value="{{ $mode->id }}">{{ $mode->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="purposes" class="form-label fw-semibold">Purpose <span class="text-danger">*</span></label>
                                <select class="form-select select2-single" id="purposes" name="purposes[]" required>
                                    <option value="">Select Purpose</option>
                                    @foreach($purposes as $purpose)
                                        <option value="{{ $purpose->id }}">{{ $purpose->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="college_response" class="form-label fw-semibold">Institution Response</label>
                            <textarea class="form-control" id="college_response" name="college_response" rows="3" placeholder="What did the institution say?"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="remarks" class="form-label fw-semibold">Internal Remarks</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="2" placeholder="Any internal notes..."></textarea>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="interaction_status_id" class="form-label fw-semibold">Interaction Status</label>
                                <select class="form-select" id="interaction_status_id" name="interaction_status_id">
                                    <option value="">Select Status</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="next_followup_date" class="form-label fw-semibold">Next Follow-up Date</label>
                                <input type="date" class="form-control" id="next_followup_date" name="next_followup_date">
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script type="text/javascript">
        $(function () {
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Initialize Select2
            $('.select2-single').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#ajaxModel')
            });

            $('#college_id').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#ajaxModel')
            });
            
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('interactions.index') }}",
                    data: function(d) {
                        d.college_id = $('#filter_college_id').val();
                        d.user_id = $('#filter_user_id').val();
                    }
                },
                order: [[0, 'desc']], // Order by date descending
                columns: [
                    {data: 'contact_date_formatted', name: 'contact_date'},
                    {data: 'college', name: 'college.name'},
                    {data: 'contact_person', name: 'contactPerson.name'},
                    {data: 'contact_mode', name: 'contact_mode'},
                    {data: 'purposes', name: 'purposes.name', orderable: false, searchable: false},
                    {data: 'status', name: 'status.name'},
                    {data: 'user', name: 'user.name'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                language: {
                    paginate: {
                        previous: '<i class="fas fa-chevron-left"></i>',
                        next: '<i class="fas fa-chevron-right"></i>'
                    }
                }
            });

            // Initialize Select2 on Filter and Redraw on change
            $('#filter_college_id').select2({
                theme: 'bootstrap-5',
                placeholder: 'Filter by Institution',
                allowClear: true
            }).on('change', function() {
                table.draw();
            });

            $('#filter_user_id').select2({
                theme: 'bootstrap-5',
                placeholder: 'Filter by Contacted By',
                allowClear: true
            }).on('change', function() {
                table.draw();
            });

            $('#resetFilterBtn').click(function() {
                $('#filter_college_id').val('').trigger('change');
                $('#filter_user_id').val('').trigger('change');
            });

            // Fetch contact persons based on college selection
            $('#college_id').change(function() {
                var collegeId = $(this).val();
                $('#contact_person_id').html('<option value="">Select Contact Person</option>');
                
                if (collegeId) {
                    $.ajax({
                        url: "{{ url('interactions/get-contacts') }}/" + collegeId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $.each(data, function(key, value) {
                                $('#contact_person_id').append('<option value="' + value.id + '">' + value.name + ' (' + (value.designation ? value.designation.name : 'No Designation') + ')</option>');
                            });
                        }
                    });
                }
            });
            
            $('#createNewInteraction').click(function () {
                $('#saveBtn').val("create-interaction");
                $('#interaction_id').val('');
                $('#interactionForm').trigger("reset");
                $('#college_id').val('').trigger('change');
                $('#purposes').val(null).trigger('change');
                $('#contact_person_id').html('<option value="">Select Contact Person</option>');
                
                // Reset user to logged in user
                $('#user_id').val("{{ auth()->id() }}");

                // Set default datetime to now
                var now = new Date();
                now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                $('#contact_date').val(now.toISOString().slice(0,16));

                $('#modelHeading').html("Log New Interaction");
                $('#ajaxModel').modal('show');
            });
            
            $('body').on('click', '.editBtn', function () {
                var interaction_id = $(this).data('id');
                $.get("{{ route('interactions.index') }}" +'/' + interaction_id +'/edit', function (data) {
                    $('#modelHeading').html("Edit Interaction");
                    $('#saveBtn').val("edit-interaction");
                    $('#interaction_id').val(data.id);
                    $('#college_id').val(data.college_id).trigger('change');
                    $('#user_id').val(data.user_id);
                    
                    // Fetch contacts and then set the selected one
                    $.ajax({
                        url: "{{ url('interactions/get-contacts') }}/" + data.college_id,
                        type: 'GET',
                        dataType: 'json',
                        success: function(contacts) {
                            $('#contact_person_id').html('<option value="">Select Contact Person</option>');
                            $.each(contacts, function(key, value) {
                                $('#contact_person_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                            });
                            $('#contact_person_id').val(data.contact_person_id);
                        }
                    });

                    // Format datetime for input[type=datetime-local]
                    var contactDate = new Date(data.contact_date);
                    var formattedDate = contactDate.toISOString().slice(0,16);
                    
                    $('#contact_date').val(formattedDate);
                    $('#contact_mode_id').val(data.contact_mode_id);
                    $('#college_response').val(data.college_response);
                    $('#remarks').val(data.remarks);
                    $('#interaction_status_id').val(data.interaction_status_id);
                    
                    if(data.next_followup_date) {
                        $('#next_followup_date').val(data.next_followup_date.split('T')[0]);
                    } else {
                        $('#next_followup_date').val('');
                    }

                    // Set purposes (Select2)
                    $('#purposes').val(data.purpose_ids).trigger('change');

                    $('#ajaxModel').modal('show');
                })
            });
            
            $('#saveBtn').click(function (e) {
                e.preventDefault();
                $(this).html('Saving...');
            
                $.ajax({
                    data: $('#interactionForm').serialize(),
                    url: "{{ route('interactions.store') }}",
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
                            url: "{{ route('interactions.index') }}"+'/'+interaction_id,
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
