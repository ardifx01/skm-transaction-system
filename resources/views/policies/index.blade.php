@extends('main')

@section('title', 'Policy Management')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Policy List</h3>
                            <div class="card-tools">
                                <form id="search-form" action="{{ route('policies.index') }}" method="GET"
                                    class="form-inline ml-3">
                                    <div class="input-group input-group-sm">
                                        <input type="text" id="search-input" name="search" class="form-control"
                                            placeholder="Search by Policy No. or Customer" value="{{ request('search') }}">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-default"><i
                                                    class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                </form>
                                @can('create-policies')
                                    <a href="{{ route('policies.create') }}" class="btn btn-primary btn-sm ml-2">Add Policy</a>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body">
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
        });
    </script>
@endpush
