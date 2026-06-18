<x-app-layout>
    <x-slot name="header">
        University Master
    </x-slot>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center py-3">
            <h6 class="mb-0 fw-bold">Manage Universities</h6>
            @can('universities.create')
            <button class="btn btn-primary btn-sm" id="createNewUniversity">
                <i class="fas fa-plus"></i> Add New University
            </button>
            @endcan
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered data-table w-100">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Short Name</th>
                            <th>State</th>
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

    <!-- Modal -->
    <div class="modal fade" id="ajaxModel" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modelHeading"></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="universityForm" name="universityForm" class="form-horizontal">
                        <input type="hidden" name="university_id" id="university_id">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">University Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter University Name" value="" maxlength="255" required>
                        </div>
        
                        <div class="mb-3">
                            <label for="short_name" class="form-label fw-semibold">Short Name</label>
                            <input type="text" class="form-control" id="short_name" name="short_name" placeholder="e.g. DU, JNU" value="" maxlength="50">
                        </div>
                        
                        <div class="mb-3">
                            <label for="state" class="form-label fw-semibold">State</label>
                            <input type="text" class="form-control" id="state" name="state" placeholder="Enter State" value="" maxlength="100">
                        </div>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
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
    <script type="text/javascript">
        $(function () {
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('universities.index') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'short_name', name: 'short_name'},
                    {data: 'state', name: 'state'},
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
            
            $('#createNewUniversity').click(function () {
                $('#saveBtn').val("create-university");
                $('#university_id').val('');
                $('#universityForm').trigger("reset");
                $('#modelHeading').html("Create New University");
                $('#ajaxModel').modal('show');
            });
            
            $('body').on('click', '.editBtn', function () {
                var university_id = $(this).data('id');
                $.get("{{ route('universities.index') }}" +'/' + university_id +'/edit', function (data) {
                    $('#modelHeading').html("Edit University");
                    $('#saveBtn').val("edit-user");
                    $('#ajaxModel').modal('show');
                    $('#university_id').val(data.id);
                    $('#name').val(data.name);
                    $('#short_name').val(data.short_name);
                    $('#state').val(data.state);
                    $('#status').val(data.status);
                })
            });
            
            $('#saveBtn').click(function (e) {
                e.preventDefault();
                $(this).html('Saving...');
            
                $.ajax({
                    data: $('#universityForm').serialize(),
                    url: "{{ route('universities.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        $('#universityForm').trigger("reset");
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
                var university_id = $(this).data("id");
                
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
                            url: "{{ route('universities.index') }}"+'/'+university_id,
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
