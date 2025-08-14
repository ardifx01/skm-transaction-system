@extends('main')

@section('title', 'Role Management')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Role List</h3>
                            <div class="card-tools">
                                <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm">Add Role</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="search-form" action="{{ route('roles.index') }}" method="GET"
                                class="form-inline mb-3 float-right">
                                <div class="input-group input-group-sm">
                                    <input type="text" id="search-input" name="search" class="form-control float-right"
                                        placeholder="Search" value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-default"><i
                                                class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </form>

                            {{-- Container untuk konten tabel dan paginasi --}}
                            <div id="roles-table-container">
                                @include('roles._table')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Skrip AJAX untuk pencarian
            $('#search-form').on('submit', function(e) {
                e.preventDefault();
                var keyword = $('#search-input').val();
                $.ajax({
                    url: "{{ route('roles.index') }}",
                    type: 'GET',
                    data: {
                        search: keyword,
                        ajax: 1
                    },
                    success: function(response) {
                        $('#roles-table-container').html(response);
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Failed to search roles.', 'error');
                    }
                });
            });

            // Skrip AJAX untuk delete
            window.deleteRole = function(roleId) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/roles/${roleId}`,
                            type: 'DELETE',
                            success: function(response) {
                                Swal.fire('Deleted!', response.success, 'success');
                                $(`#role-${roleId}`).remove();
                            },
                            error: function(xhr) {
                                Swal.fire('Error!', 'Failed to delete role.', 'error');
                            }
                        });
                    }
                });
            };
        });
    </script>
@endpush
