@extends('layouts.layout')
@section('content')
<div class="admin-chat-container">
    <!-- Danh sách người dùng -->
    <div class="user-list">
            <h3>Tin nhắn đến</h3>
        <ul>
            <li class="user-item">Hoàng Tử Vĩnh Phúc</li>
            <li class="user-item">Thắng Đẳng cấp</li>
            <li class="user-item">Thắng FullStack</li>
        </ul>
    </div>

    <!-- Nội dung chat -->
    <div class="chat-content">
        {{-- <div class="empty-state">
            <p>Hi Admin</p>
        </div> --}}
        <div class="chat-area">
            <div class="chat-header">
                <h3>Hoàng Cửu Bảo</h3>
            </div>
            <div class="chat-messages">
                <p><strong>Hoàng Cửu Bảo:</strong> Xin chào, shop mình có bán thuốc diệt cỏ không?!</p>
                <p><strong>Admin:</strong> ??????</p>
            </div>
            <div class="chat-input">
                <input type="text" placeholder="Nhập tin nhắn...">
                <button>Gửi</button>
            </div>
        </div>
        <!-- abcd -->
    </div>
</div>
@endsection