<x-app-layout>
    <x-slot name="header">
        Designation Master
    </x-slot>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center py-3">
            <h6 class="mb-0 fw-bold">Manage Designations</h6>
            <button class="btn btn-primary btn-sm" id="createNewDesignation">
                <i class="fas fa-plus"></i> Add New Designation
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered data-table w-100">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Name</th>
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
                    <form id="designationForm" name="designationForm" class="form-horizontal">
                        <input type="hidden" name="designation_id" id="designation_id">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Designation Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter Designation" value="" maxlength="255" required>
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
                ajax: "{{ route('designations.index') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
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
            
            $('#createNewDesignation').click(function () {
                $('#saveBtn').val("create-designation");
                $('#designation_id').val('');
                $('#designationForm').trigger("reset");
                $('#modelHeading').html("Create New Designation");
                $('#ajaxModel').modal('show');
            });
            
            $('body').on('click', '.editBtn', function () {
                var designation_id = $(this).data('id');
                $.get("{{ route('designations.index') }}" +'/' + designation_id +'/edit', function (data) {
                    $('#modelHeading').html("Edit Designation");
                    $('#saveBtn').val("edit-designation");
                    $('#ajaxModel').modal('show');
                    $('#designation_id').val(data.id);
                    $('#name').val(data.name);
                    $('#status').val(data.status);
                })
            });
            
            $('#saveBtn').click(function (e) {
                e.preventDefault();
                $(this).html('Saving...');
            
                $.ajax({
                    data: $('#designationForm').serialize(),
                    url: "{{ route('designations.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        $('#designationForm').trigger("reset");
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
                var designation_id = $(this).data("id");
                
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
                            url: "{{ route('designations.index') }}"+'/'+designation_id,
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
