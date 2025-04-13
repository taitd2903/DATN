@extends('layouts.layout')

@section('content')
     <h1>Danh sách liên hệ</h1>

     @if(session('success'))
           <div class="alert alert-success">{{ session('success') }}</div>
      @endif
      <form method="GET" action="{{ route('admin.contacts.index') }}" class="mb-3 d-flex gap-2">
            <input type="text" name="keyword" placeholder="Tìm theo tên hoặc email" value="{{ request('keyword') }}" class="border px-2 py-1 rounded" />
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="border px-2 py-1 rounded" />
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="border px-2 py-1 rounded" />

            <select name="status" class="form-control">
                <option value="">-- Trạng thái --</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Chưa trả lời</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Đã trả lời</option>
            </select>
            <button type="submit" class="btn btn-primary">Lọc</button>
        </form>
        

     <table class="table table-bordered">
           <thead>
                 <tr>
                       <th>Họ tên</th>
                       <th>Email</th>

                       <th>Thời gian</th>
                       <th>Trạng thái</th>
                       <th>Hành động</th>
                 </tr>
           </thead>
           <tbody>
                 @foreach($contacts as $contact)
                          <tr>
                                 <td>{{ $contact->name }}</td>
                                 <td>{{ $contact->email }}</td>

                                 <td>{{ $contact->created_at->format('d/m/Y H:i') }}</td>
                                 <td>
                                    @if ($contact->status == 1)
                                        <span class="badge bg-success">Đã trả lời</span>
                                    @else
                                        <span class="badge bg-warning">Chưa trả lời</span>
                                    @endif
                                    <form action="{{ route('admin.contacts.updateStatus', $contact->id) }}" method="POST">
                                          @csrf
                                          <label>Trạng thái:</label>
                                          <select name="status" onchange="this.form.submit()" class="form-select">
                                                @if($contact->status == 0)
                                                    <option value="0" selected>Chưa trả lời</option>
                                                    <option value="1">Đã trả lời</option>
                                                @else
                                                    <option value="1" selected>Đã trả lời</option>
                                                @endif
                                            </select>
                                            
                                      </form>
                                      
                                </td>
                                
                                 <td>
                                    
                                          <a href="{{ route('admin.contacts.show', $contact->id) }}" class="btn btn-info btn-sm">Xem</a>


                                          <form action="{{ route('admin.contacts.destroy', $contact->id) }}" method="POST"
                                                  style="display:inline;">
                                                  @csrf
                                                  @method('DELETE')
                                                  <button onclick="return confirm('Bạn chắc chắn muốn xoá?')"
                                                         class="btn btn-danger btn-sm">Xoá</button>
                                          </form>
                                 </td>
                          </tr>
                     @endforeach
           </tbody>
     </table>

     {{ $contacts->links() }}
@endsection