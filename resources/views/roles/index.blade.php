<x-app-layout>
    <x-slot name="header">
        Roles & Permissions Management
    </x-slot>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center py-3">
            <h6 class="mb-0 fw-bold text-dark">Manage System Roles</h6>
            <button class="btn btn-primary btn-sm" id="createNewRole">
                <i class="fas fa-plus"></i> Add Dynamic Role
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered data-table w-100">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Role Identifier</th>
                            <th>Display Label</th>
                            <th>Description</th>
                            <th>Assigned Users</th>
                            <th width="180px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div class="modal fade" id="ajaxModel" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modelHeading"></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="roleForm" name="roleForm" class="form-horizontal">
                        <input type="hidden" name="role_id" id="role_id">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Role Name (Key) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="e.g. manager, developer" maxlength="50" required>
                            <div class="form-text text-muted small">System key. Will be converted to lowercase, snake_case.</div>
                        </div>
        
                        <div class="mb-3">
                            <label for="label" class="form-label fw-semibold">Display Label <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="label" name="label" placeholder="e.g. Project Manager" maxlength="100" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea class="form-control" id="description" name="description" placeholder="Brief role description..." rows="2" maxlength="255"></textarea>
                        </div>
        
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Save Role</button>
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
                ajax: "{{ route('roles.index') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'label', name: 'label'},
                    {data: 'description', name: 'description'},
                    {data: 'users_count', name: 'users_count', orderable: false, searchable: false},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                language: {
                    paginate: {
                        previous: '<i class="fas fa-chevron-left"></i>',
                        next: '<i class="fas fa-chevron-right"></i>'
                    }
                }
            });
            
            $('#createNewRole').click(function () {
                $('#saveBtn').val("create-role");
                $('#role_id').val('');
                $('#roleForm').trigger("reset");
                $('#name').removeAttr('readonly');
                $('#modelHeading').html("Create Dynamic Role");
                $('#ajaxModel').modal('show');
            });
            
            $('#saveBtn').click(function (e) {
                e.preventDefault();
                $(this).html('Saving...');
            
                $.ajax({
                    data: $('#roleForm').serialize(),
                    url: "{{ route('roles.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        $('#roleForm').trigger("reset");
                        $('#ajaxModel').modal('hide');
                        table.draw();
                        $('#saveBtn').html('Save Role');
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
                        $('#saveBtn').html('Save Role');
                        
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
                var role_id = $(this).data("id");
                
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Users with this role will be reassigned to 'Regular User' role.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "DELETE",
                            url: "{{ route('roles.index') }}"+'/'+role_id,
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
                                    errorMsg = data.responseJSON.error || "Unauthorized action.";
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
