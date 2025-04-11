<form action="{{ route('admin.user.permissions.update', $user->id) }}" method="POST">
    @csrf
    
    @foreach ($permissions as $permission)
        <div class="mb-2">
            <label>
                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                       {{ in_array($permission->id, $userPermissions) ? 'checked' : '' }}>
                {{ $permission->label }}
            </label>
        </div>
    @endforeach
    <button type="submit" class="btn btn-primary">Lưu quyền</button>
</form>
