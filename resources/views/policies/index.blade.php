@extends('main')

@section('title', 'Policy Management')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Policies List</h3>
                            <div class="card-tools">
                                @can('policies-create')
                                    <a href="{{ route('policies.create') }}" class="btn btn-primary btn-sm">Add Policies</a>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="search-form" action="{{ route('policies.index') }}" method="GET"
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
                            <div id="policies-table-container">
                                @include('policies._table')
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
            // Search
            $('#search-form').on('submit', function(e) {
                e.preventDefault();
                var keyword = $('#search-input').val();
                $.ajax({
                    url: "{{ route('policies.index') }}",
                    type: 'GET',
                    data: {
                        search: keyword,
                        ajax: 1
                    },
                    success: function(response) {
                        $('#policies-table-container').html(response);
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Failed to search policies.', 'error');
                    }
                });
            });

            // Delete
            window.deletePolicy = function(policyId) {
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
                            url: `/policies/${policyId}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                Swal.fire('Deleted!', response.success, 'success');
                                $(`#policy-${policyId}`).remove();
                            },
                            error: function(xhr) {
                                Swal.fire('Error!', 'Failed to delete policy.', 'error');
                            }
                        });
                    }
                });
            };

            // 🔥 Alert kalau ada message dari backend
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: "{{ session('success') }}"
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: "{{ session('error') }}"
                });
            @endif
        });
    </script>
@endpush
