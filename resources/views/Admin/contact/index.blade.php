@extends('layouts.layout')

@section('content')
     <h1>Danh sách liên hệ</h1>

     @if(session('success'))
           <div class="alert alert-success">{{ session('success') }}</div>
      @endif

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
                                          @if ($contact->status)
                                                         <span class="badge bg-success">Đã trả lời</span>
                                                    @else
                                                                        <span class="badge bg-warning">Chưa trả lời</span>
                                                                   @endif
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