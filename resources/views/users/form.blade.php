@extends('main')

@section('title', isset($user) ? 'Edit User' : 'Add User')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">{{ isset($user) ? 'Edit User' : 'Add User' }}</h3>
                        </div>
                        <form id="userForm" class="form-horizontal">
                            @csrf
                            @if (isset($user))
                                @method('PUT')
                            @endif
                            <div class="card-body">
                                <div class="form-group row">
                                    <label for="name" class="col-sm-2 col-form-label">Name</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="name" class="form-control" id="name"
                                            value="{{ $user->name ?? '' }}" placeholder="Enter Name" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="email" class="col-sm-2 col-form-label">Email</label>
                                    <div class="col-sm-10">
                                        <input type="email" name="email" class="form-control" id="email"
                                            value="{{ $user->email ?? '' }}" placeholder="Enter Email" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="role" class="col-sm-2 col-form-label">Role</label>
                                    <div class="col-sm-10">
                                        <select name="role" id="role" class="form-control">
                                            <option value="">Select a Role</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->name }}"
                                                    @if (isset($user) && $user->hasRole($role->name)) selected @endif>
                                                    {{ $role->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="password" class="col-sm-2 col-form-label">Password</label>
                                    <div class="col-sm-10">
                                        <input type="password" name="password" class="form-control" id="password"
                                            placeholder="Enter Password">
                                        <small class="form-text text-muted">Leave blank to keep current password</small>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="password_confirmation" class="col-sm-2 col-form-label">Confirm
                                        Password</label>
                                    <div class="col-sm-10">
                                        <input type="password" name="password_confirmation" class="form-control"
                                            id="password_confirmation" placeholder="Confirm Password">
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                                <a href="{{ route('users.index') }}" class="btn btn-default">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#userForm').on('submit', function(e) {
                e.preventDefault();

                var form = $(this);
                var url = "{{ isset($user) ? route('users.update', $user->id) : route('users.store') }}";
                var method = "{{ isset($user) ? 'PUT' : 'POST' }}";

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
                            if (method === 'POST') {
                                form[0].reset();
                            }
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
