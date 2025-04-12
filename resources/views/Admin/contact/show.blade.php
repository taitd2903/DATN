@extends('layouts.layout')

@section('content')
    <h1>Chi tiết liên hệ</h1>

    <p><strong>Họ tên:</strong> {{ $contact->name }}</p>
    <p><strong>Email:</strong> {{ $contact->email }}</p>
    <p><strong>Nội dung:</strong> {{ $contact->message }}</p>
    <p><strong>Gửi lúc:</strong> {{ $contact->created_at->format('d/m/Y H:i') }}</p>

    <a href="{{ route('admin.contacts.index') }}" class="btn btn-secondary">Quay lại</a>
@endsection
