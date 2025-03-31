{{-- @extends('layouts.layout')

@section('content')
<div class="container">
    <h1>Thêm Combo Mới</h1>
    <form action="{{ route('admin.combos.store') }}" method="POST" enctype="multipart/form-data">
     @csrf
     <div class="form-group">
         <label for="name">Tên Combo</label>
         <input type="text" name="name" class="form-control" value="{{ old('name') }}">
         @error('name') <div class="text-danger">{{ $message }}</div> @enderror
     </div>
 
     <div class="form-group">
          <label for="products">Chọn sản phẩm</label>
          @foreach ($products as $product)
          <label>
              <input type="checkbox" name="products[]" value="{{ $product->id }}">
              {{ $product->name }} - {{ $product->price }} VNĐ
          </label><br>
      @endforeach

           
          @error('products') <div class="text-danger">{{ $message }}</div> @enderror
      </div>
      
 
     <div class="form-group">
         <label for="discount_price">Giá ưu đãi</label>
         <input type="number" name="discount_price" class="form-control" value="{{ old('discount_price') }}">
         @error('discount_price') <div class="text-danger">{{ $message }}</div> @enderror
     </div>
 
     <div class="form-group">
         <label for="image">Ảnh Combo</label>
         <input type="file" name="image" class="form-control">
         @error('image') <div class="text-danger">{{ $message }}</div> @enderror
     </div>
 
     <button type="submit" class="btn btn-primary">Tạo Combo</button>
 </form>
 
</div>
@endsection --}}
