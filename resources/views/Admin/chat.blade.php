@extends('layouts.layout')
@section('content')
    <div class="admin-chat-container">
        <!-- Danh sách người dùng -->
        <div class="user-list">
            <h3>Tin nhắn đến</h3>
            <ul class="list-group" id="userList">
                @foreach ($users as $user)
                    <li class="list-group-item user-item d-flex align-items-center" data-user-id="{{ $user->id }}"
                        style="cursor: pointer;">
                        <span class="abc">{{ $user->name }}</span>
                        @if ($user->has_unread)
                            <span class="unread"></span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Nội dung chat -->
        <div class="chat-content">
            <div class="chat-area">
                <div class="chat-header">
                    <h3 id="chat-user-name">Chọn người dùng để chat</h3>
                </div>
                <div class="chat-messages" id="chatMessages" style="height: 400px; overflow-y: auto;"></div>
                <div class="chat-input">
                    <form id="adminMessageForm">
                        @csrf
                        <input type="hidden" id="receiver_id" name="receiver_id">
                        <input type="text" id="messageInput" name="message" placeholder="Nhập tin nhắn...">
                        <button type="submit">Gửi</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @vite(['resources/js/app.js'])

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script>
        $(document).ready(function() {
            // Chọn user để chat
            $(document).on('click', '.user-item', function() {
                let userId = $(this).data('user-id');
                let userName = $(this).find('.abc').text();
                $('#receiver_id').val(userId);
                $('#chat-user-name').text(userName);
                $(this).find('.unread').remove();

                $.get('/chat/history', {
                    receiver_id: userId
                }, function(response) {
                    $('#chatMessages').empty();
                    let lastDate = "";
                    response.messages.forEach(function(msg) {
                        if (msg.date !== lastDate) {
                            $('#chatMessages').append(
                                `<p class="date-separator">${msg.date}</p>`
                            );
                            lastDate = msg.date;
                        }
                        if (msg.is_admin) {
                            $('#chatMessages').append(
                                `<p class="admin-message"><strong>${msg.sender}</strong>: ${msg.message} <span class="message-time">${msg.time}</span></p>`
                            );
                        } else {
                            $('#chatMessages').append(
                                `<p class="user-message"><strong>${msg.sender}</strong>: ${msg.message} <span class="message-time">${msg.time}</span></p>`
                            );
                        }
                    });
                    $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);
                });
            });

            $('#adminMessageForm').on('submit', function(e) {
                e.preventDefault();
                let message = $('#messageInput').val();
                let receiverId = $('#receiver_id').val();
                if (!message || !receiverId) return;

                $.post('/chat/send', {
                    _token: '{{ csrf_token() }}',
                    message: message,
                    receiver_id: receiverId,
                    is_admin: true
                }, function(response) {
                    if (response.status === 'Message sent!') {
                        $('#messageInput').val('');
                    }
                }).fail(function(xhr) {
                    console.error('Error:', xhr.responseText);
                });
            });

            // Kết nối Pusher
            const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
                cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
                encrypted: true
            });

            const channel = pusher.subscribe('chat.user.admin');

            channel.bind('message.sent', function(data) {
                let isFromUser = data.receiverId === 'admin';
                let relevantUserId = isFromUser ? data.userId : data.receiverId;
                let senderName = data.userName;
                let time = data.time.split(' ')[1].slice(0, 5);
                let userItem = $(`.user-item[data-user-id="${relevantUserId}"]`);
                if (userItem.length) {
                    $('#userList').prepend(userItem);
                } else if (isFromUser) {
                    $('#userList').prepend(`<li class="list-group-item user-item d-flex align-items-center" 
            data-user-id="${relevantUserId}" style="cursor: pointer;">
            <span class="abc">${data.userName}</span>
            <span class="unread"></span>
        </li>`);
                    userItem = $(`.user-item[data-user-id="${relevantUserId}"]`);
                }

                if ($('#receiver_id').val() != relevantUserId) {
                    userItem.find('.unread').remove();
                    userItem.append(`<span class="unread"></span>`);
                } else {
                    userItem.find('.unread').remove();
                }

                if ($('#receiver_id').val() == relevantUserId) {
                    const lastDate = $('#chatMessages .date-separator:last').text();
                    const today = new Date().toLocaleDateString('vi-VN', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric'
                    });
                    if (lastDate !== today) {
                        $('#chatMessages').append(`<p class="date-separator">${today}</p>`);
                    }
                    if (isFromUser) {
                        $('#chatMessages').append(
                            `<p class="user-message"><strong>${senderName}</strong>: ${data.message} <span class="message-time">${time}</span></p>`
                        );
                    } else {
                        $('#chatMessages').append(
                            `<p class="admin-message"><strong>${senderName}</strong>: ${data.message} <span class="message-time">${time}</span></p>`
                        );
                    }
                    $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);
                }
            });
        });
    </script>
@endsection
