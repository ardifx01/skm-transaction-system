@extends('main')

@section('title', isset($role) ? 'Edit Role' : 'Add Role')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <form id="roleForm" class="form-horizontal">
                @csrf
                @if (isset($role))
                    @method('PUT')
                @endif
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">{{ isset($role) ? 'Edit Role' : 'Add Role' }}</h3>
                    </div>
                    <div class="card-body">
                        <!-- Role Name -->
                        <div class="form-group row">
                            <label for="name" class="col-sm-2 col-form-label">Role Name</label>
                            <div class="col-sm-10">
                                <input type="text" name="name" class="form-control" id="name"
                                    value="{{ $role->name ?? '' }}" placeholder="Enter Role Name" required>
                            </div>
                        </div>

                        <!-- Permissions per Modul -->
                        @php
                            $groupedPermissions = $permissions->groupBy(function ($perm) {
                                return explode('-', $perm->name)[1] ?? 'General';
                            });
                            $modules = $groupedPermissions->keys();
                        @endphp

                        <div class="row">
                            @foreach ($modules as $module)
                                <div class="col-md-6 col-lg-4">
                                    <div class="card mb-3">
                                        <div class="card-header bg-light text-uppercase font-weight-bold">
                                            {{ $module }}
                                        </div>
                                        <div class="card-body">
                                            <div class="row permission-row">
                                                @foreach ($groupedPermissions[$module] as $permission)
                                                    <div class="col-12 permission-item">
                                                        <div
                                                            class="form-check d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="permissions[]" value="{{ $permission->name }}"
                                                                    id="permission-{{ $permission->id }}"
                                                                    @if (isset($role) && $role->hasPermissionTo($permission->name)) checked @endif>
                                                                <label class="form-check-label"
                                                                    for="permission-{{ $permission->id }}">
                                                                    {{ $permission->name }}
                                                                </label>
                                                            </div>
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm delete-permission">Delete</button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <!-- Tambah permission baru -->
                                            <div class="mt-2 d-flex flex-wrap">
                                                <input type="text" class="form-control new-permission mr-2 mb-2"
                                                    placeholder="Add new permission in {{ $module }}"
                                                    data-module="{{ $module }}">
                                                <button type="button" class="btn btn-success mb-2 add-permission"
                                                    data-module="{{ $module }}">Add</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <a href="{{ route('roles.index') }}" class="btn btn-default">Back</a>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            // Tambah permission baru
            $('.add-permission').on('click', function() {
                var module = $(this).data('module');
                var input = $(this).siblings('.new-permission');
                var value = input.val().trim();

                if (value === '') {
                    Swal.fire('Error', 'Permission name cannot be empty!', 'error');
                    return;
                }

                var checkboxId = 'perm-' + module + '-' + value.replace(/\s+/g, '-').toLowerCase();
                var newCheckbox = `
            <div class="col-12 mt-2 permission-item">
                <div class="form-check d-flex justify-content-between align-items-center">
                    <div>
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="${value}" id="${checkboxId}" checked>
                        <label class="form-check-label" for="${checkboxId}">${value}</label>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm delete-permission">Delete</button>
                </div>
            </div>
        `;
                $(this).parent().prev('.permission-row').append(newCheckbox);
                Swal.fire('Success', `Permission "${value}" added!`, 'success');
                input.val('');
            });

            // Hapus permission
            $(document).on('click', '.delete-permission', function() {
                var parent = $(this).closest('.permission-item');
                var permName = parent.find('input').val();
                Swal.fire({
                    title: 'Are you sure?',
                    text: `Delete permission "${permName}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                }).then((result) => {
                    if (result.isConfirmed) {
                        parent.remove();
                        Swal.fire('Deleted!', `Permission "${permName}" has been deleted.`,
                            'success');
                    }
                });
            });

            // Submit form via AJAX
            $('#roleForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var url = "{{ isset($role) ? route('roles.update', $role->id) : route('roles.store') }}";
                var method = "{{ isset($role) ? 'PUT' : 'POST' }}";

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: form.serialize() + '&_method=' + method,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.success,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            if (method === 'POST') form[0].reset();
                        });
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;
                        var errorMessages = '';
                        $.each(errors, function(key, value) {
                            errorMessages += value + '<br>';
                        });
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            html: errorMessages
                        });
                    }
                });
            });

        });
    </script>
@endpush
