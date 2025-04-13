@extends('layouts.layout')

@section('content')
    <h1>Chi tiết liên hệ</h1>

    <p><strong>Họ tên:</strong> {{ $contact->name }}</p>
    <p><strong>Email:</strong> {{ $contact->email }}</p>
    <p><strong>Nội dung:</strong> {{ $contact->message }}</p>
    <p><strong>Gửi lúc:</strong> {{ $contact->created_at->format('d/m/Y H:i') }}</p>
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
    

    <a href="{{ route('admin.contacts.index') }}" class="btn btn-secondary">Quay lại</a>
@endsection
