<x-app-layout>
    <x-slot name="header">
        Edit Role Permissions: {{ $role->label }}
    </x-slot>

    <div class="d-flex mb-3">
        <a href="{{ route('roles.index') }}" class="btn btn-secondary btn-sm px-3">
            <i class="fas fa-arrow-left me-1"></i> Back to Roles
        </a>
    </div>

    <form action="{{ route('roles.update', $role->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Role Info Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0 fw-bold text-dark">Role Properties</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-md-4 mb-3">
                        <label for="name" class="form-label fw-semibold">Role Name (Key)</label>
                        <input type="text" class="form-control bg-light" id="name" name="name" value="{{ $role->name }}" readonly>
                        <div class="form-text small text-muted">System keys are unique and cannot be renamed.</div>
                    </div>
                    <div class="col-12 col-md-4 mb-3">
                        <label for="label" class="form-label fw-semibold">Display Label <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('label') is-invalid @enderror" id="label" name="label" value="{{ old('label', $role->label) }}" required>
                        @error('label')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-4 mb-3">
                        <label for="description" class="form-label fw-semibold">Description</label>
                        <input type="text" class="form-control" id="description" name="description" value="{{ old('description', $role->description) }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Permissions Matrix Grid Section -->
        <h5 class="fw-bold mb-3 text-dark"><i class="fas fa-shield-alt me-2 text-primary"></i>Assign Permissions</h5>
        
        <!-- Toggle All Permissions Helper button -->
        <div class="mb-3 d-flex gap-2">
            <button type="button" class="btn btn-xs btn-outline-secondary px-3 py-1" id="selectAll">Select All</button>
            <button type="button" class="btn btn-xs btn-outline-secondary px-3 py-1" id="deselectAll">Deselect All</button>
        </div>

        <div class="row g-4 mb-4">
            @foreach($permissionsByModule as $module => $permissions)
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card h-100">
                    <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                        <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.95rem;">
                            <i class="fas fa-folder-open me-2 text-primary opacity-75"></i>{{ $module }}
                        </h6>
                        <button type="button" class="btn btn-xs btn-link text-decoration-none text-muted p-0 select-module-btn" data-module="{{ Str::slug($module) }}">Toggle All</button>
                    </div>
                    <div class="card-body p-3">
                        @foreach($permissions as $permission)
                        <div class="form-check mb-2">
                            <input class="form-check-input perm-checkbox module-{{ Str::slug($module) }}" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="permission_{{ $permission->id }}"
                                @if($role->permissions->contains($permission->id)) checked @endif>
                            <label class="form-check-label text-dark small fw-medium" for="permission_{{ $permission->id }}">
                                {{ $permission->label }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Action Submit Buttons -->
        <div class="card border-0 shadow-sm p-3 d-flex flex-row justify-content-end gap-2 mb-5">
            <a href="{{ route('roles.index') }}" class="btn btn-secondary px-4">Cancel</a>
            <button type="submit" class="btn btn-primary px-5 fw-semibold">Save Changes & Update Role</button>
        </div>
    </form>

    @push('scripts')
    <script>
        $(function() {
            // Select All Click Handler
            $('#selectAll').click(function() {
                $('.perm-checkbox').prop('checked', true);
            });

            // Deselect All Click Handler
            $('#deselectAll').click(function() {
                $('.perm-checkbox').prop('checked', false);
            });

            // Toggle Module Checkboxes Click Handler
            $('.select-module-btn').click(function() {
                var moduleClass = '.module-' + $(this).data('module');
                var allChecked = true;
                $(moduleClass).each(function() {
                    if (!$(this).prop('checked')) {
                        allChecked = false;
                        return false; // break
                    }
                });
                $(moduleClass).prop('checked', !allChecked);
            });
        });
    </script>
    @endpush
</x-app-layout>
