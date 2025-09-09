{{-- resources/views/policies/_table.blade.php --}}
<table id="policiesTable" class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>ID</th>
            <th>Policy Number</th>
            <th>Customer</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($policies as $policy)
            <tr id="policy-{{ $policy->id }}">
                <td>{{ $policy->id }}</td>
                <td>{{ $policy->no_policy ?? '-' }}</td>
                <td>{{ $policy->user->name }}</td>
                <td>{{ $policy->status }}</td>
                <td>


                    <!-- Detail Button -->
                    @can('policies-show')
                        <a href="{{ route('policies.show', $policy->id) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                    @endcan

                    <!-- Export Button -->
                    @if ($policy->status === 'paid')
                        @can('policies-export')
                            <a href="{{ route('policies.export-excel', ['policy' => $policy->id]) }}"
                                class="btn btn-success btn-sm">
                                <i class="fas fa-file-export"></i> Export
                            </a>
                        @endcan

                        @can('policies-pdf')
                            <a href="{{ route('generate-pdf', ['policy' => $policy->id]) }}" class="btn btn-success btn-sm">
                                <i class="fas fa-file-export"></i> Export PDF
                            </a>
                        @endcan

                        <!-- Send Mail Button -->
                        @can('policies-send-mail')
                            {{-- <form action="{{ route('policies.send-mail', $policy->id) }}" method="POST" class="d-inline"> --}}
                            <form action="#" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-envelope"></i> Send Mail
                                </button>
                            </form>
                        @endcan
                    @endif

                    <!-- Edit Button -->
                    @if ($policy->status === 'pending_verification')
                        @can('policies-edit')
                            <a href="{{ route('policies.edit', $policy->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        @endcan

                        <!-- Delete Button -->
                        @can('policies-delete')
                            <button type="button" class="btn btn-danger btn-sm"
                                onclick="deletePolicy({{ $policy->id }})">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        @endcan
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<div class="mt-3">
    {{ $policies->links() }}
</div>
