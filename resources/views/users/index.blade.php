@extends('main')

@section('title', 'User Management')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">User List</h3>
                            <div class="card-tools">
                                <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">Add User</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="search-form" action="{{ route('users.index') }}" method="GET"
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
                            <div id="users-table-container">
                                @include('users._table')
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
        // Pastikan SweetAlert2 sudah dimuat di layout utama
        // Tambahkan ini untuk token CSRF
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function deleteUser(userId) {
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
                        url: `/users/${userId}`,
                        type: 'DELETE',
                        success: function(response) {
                            Swal.fire(
                                'Deleted!',
                                response.success,
                                'success'
                            );
                            // Hapus baris user dari tabel secara dinamis
                            $('#user-' + userId).remove();
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error!',
                                'Failed to delete user.',
                                'error'
                            );
                        }
                    });
                }
            });
        }

        $(document).ready(function() {
            $('#search-form').on('submit', function(e) {
                e.preventDefault();

                var keyword = $('#search-input').val();

                $.ajax({
                    url: "{{ route('users.index') }}",
                    type: 'GET',
                    data: {
                        search: keyword,
                        // Tandai permintaan ini sebagai AJAX
                        ajax: 1
                    },
                    success: function(response) {
                        // Perbarui seluruh konten container
                        $('#users-table-container').html(response);
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Failed to search users.', 'error');
                    }
                });
            });
        });
    </script>
@endpush
