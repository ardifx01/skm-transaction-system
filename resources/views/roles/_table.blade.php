{{-- resources/views/roles/_table.blade.php --}}
<table id="rolesTable" class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Guard Name</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($roles as $role)
            <tr id="role-{{ $role->id }}">
                <td>{{ $role->id }}</td>
                <td>{{ $role->name }}</td>
                <td>{{ $role->guard_name ?? '-' }}</td>
                <td>
                    <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <button type="button" class="btn btn-danger btn-sm"
                        onclick="deleteRole({{ $role->id }})">Delete</button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<div class="mt-3">
    {{ $roles->links() }}
</div>
