{{-- @extends('layouts.app')

@section('content') --}}
<div class="container">
    <h2>Thêm Danh Mục</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Tên Danh Mục</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="parent_id" class="form-label">Danh Mục Cha (Tùy chọn)</label>
            <select name="parent_id" class="form-control">
                <option value="">Chọn danh mục cha</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-success">Lưu</button>
    </form>
</div>
{{-- @endsection --}}
