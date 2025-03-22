@extends('layouts.layout')
@section('content')
    <div class="admin-chat-container">
        <!-- Danh sách người dùng -->
        <div class="user-list">
            <h3>Tin nhắn đến</h3>
            <ul class="list-group">
                @foreach ($users as $user)
                    <li class="list-group-item user-item d-flex align-items-center" data-user-id="{{ $user->id }}"
                        style="cursor: pointer;">
                        <span class="abc">{{ $user->name }}</span>
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
                <div class="chat-messages" id="chatMessages" style="height: 400px; overflow-y: auto;">
                    <!-- Tin nhắn ở đây -->
                </div>
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
        $('.user-item').on('click', function() {
            let userId = $(this).data('user-id');
            let userName = $(this).find('.abc').text();
            $('#receiver_id').val(userId);
            $('#chat-user-name').text(userName);
            $(this).find('.unread').remove();
            $.get('/chat/history', {
                receiver_id: userId
            }, function(response) {
                console.log('Chat history:', response);
                $('#chatMessages').empty();
                response.messages.forEach(function(msg) {
                    let sender = msg.is_admin ? 'Admin' : userName;
                    $('#chatMessages').append(`<p><strong>${sender}:</strong> ${msg.message}</p>`);
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
                console.log(response);
                if (response.status === 'Message sent!') {
                    $('#messageInput').val('');
                    $.get('/chat/history', {
                        receiver_id: receiverId
                    }, function(response) {
                        $('#chatMessages').empty();
                        response.messages.forEach(function(msg) {
                            let sender = msg.is_admin ? 'Admin' : $('#chat-user-name')
                                .text();
                            $('#chatMessages').append(
                                `<p><strong>${sender}:</strong> ${msg.message}</p>`);
                        });
                        $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);
                    });
                }
            }).fail(function(xhr) {
                console.error('Error:', xhr.responseText);
            });
        });
        console.log('Pusher script loaded');
        const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            encrypted: true,
            authEndpoint: '/broadcasting/auth',
            auth: {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }
        });
        console.log('Pusher initialized');
        console.log('Pusher initialized with key:', '{{ env('PUSHER_APP_KEY') }}', 'and cluster:',
            '{{ env('PUSHER_APP_CLUSTER') }}');
        pusher.connection.bind('connected', function() {
            console.log('Connected to Pusher');
        });
        pusher.connection.bind('error', function(err) {
            console.error('Pusher connection error:', err);
        });
        const channel = pusher.subscribe('chat.user.admin');
        console.log('Subscribing to private-chat.user.admin');

        channel.bind('pusher:subscription_succeeded', function() {
            console.log('Subscribed to private-chat.user.admin');
        });
        channel.bind('pusher:subscription_error', function(error) {
            console.error('Subscription error:', error);
        });

        channel.bind('message.sent', function(data) {
            console.log('Received message from User:', data);
            let userItem = $(`.user-item[data-user-id="${data.userId}"]`);
            if (userItem.length) {
                $('.user-list ul').prepend(userItem);
            } else {
                $('.user-list ul').prepend(`<li class="list-group-item user-item d-flex align-items-center" 
                                    data-user-id="${data.userId}" style="cursor: pointer;">
                                    <span>${data.userName}</span>
                                    <span class="badge badge-danger unread">(Mới)</span>
                                </li>`);
            }

            if ($('#receiver_id').val() != data.userId) {
                userItem.find('.unread').remove();
                userItem.append(`<span class="badge badge-danger unread" style="margin-left: 5px">(Mới)</span>`);
            }

            if ($('#receiver_id').val() == data.userId) {
                $('#chatMessages').append(`<p><strong>${data.userName}:</strong> ${data.message}</p>`);
                $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);
            }
        });
    </script>
@endsection
