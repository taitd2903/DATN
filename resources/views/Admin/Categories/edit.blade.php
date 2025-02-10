{{-- @extends('layouts.app') --}}

{{-- @section('content') --}}
<div class="container">
    <h2>Chỉnh Sửa Danh Mục</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Tên Danh Mục</label>
            <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
        </div>

        @if ($category->children->count() > 0)
            <div class="mb-3">
                <label class="form-label">Danh Mục Cha</label>
                <p><strong>Danh mục này là danh mục cha và không thể thay đổi danh mục cha!</strong></p>
                <input type="hidden" name="parent_id" value="{{ $category->parent_id }}">
            </div>
        @else
            <div class="mb-3">
                <label for="parent_id" class="form-label">Danh Mục Cha (Tùy chọn)</label>
                <select name="parent_id" class="form-control">
                    <option value="">Chọn danh mục cha</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $cat->id == $category->parent_id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif

        <button type="submit" class="btn btn-primary">Cập Nhật</button>
    </form>
</div>
{{-- @endsection --}}
