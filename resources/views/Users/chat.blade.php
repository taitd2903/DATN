<div class="chat-button" id="chatButton" onclick="toggleChat()">
    <span>Chat với Admin</span>
</div>

<!-- Hộp thoại chat -->
<div class="chat-box" id="chatBox">
    <div class="chat-header">
        <span>Chat với Admin</span>
        <button class="close-btn" onclick="toggleChat()"><img
                src="https://cdn-icons-png.flaticon.com/128/12613/12613236.png" alt=""
                style="width: 25px; height: 23px;"></button>
    </div>
    <div class="chat-content" id="chatMessages">
        {{-- <p>Chào bạn! Bạn có câu hỏi gì không?.</p> --}}
    </div>
    <div class="chat-input">
        <input type="text" id="messageInput" placeholder="Nhập tin nhắn...">
        <button onclick="sendMessage()">Gửi</button>
    </div>
</div>

@vite(['resources/js/app.js'])
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://js.pusher.com/7.0/pusher.min.js"></script>
<script>
    function toggleChat() {
        const chatBox = document.getElementById('chatBox');
        const chatButton = document.getElementById('chatButton');
        chatBox.style.display = chatBox.style.display === 'flex' ? 'none' : 'flex';
        chatButton.style.display = chatBox.style.display === 'flex' ? 'none' : 'block';

        if (chatBox.style.display === 'flex') {
            loadChatHistory();
        }
    }

    function loadChatHistory() {
        $.get('/chat/history', function(response) {
            console.log('Chat history:', response);
            $('#chatMessages').empty();
            response.messages.forEach(function(msg) {
                let sender = msg.is_admin ? 'Admin' : 'Bạn';
                $('#chatMessages').append(`<p><strong>${sender}:</strong> ${msg.message}</p>`);
            });
            $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);
        });
    }

    function sendMessage() {
        const messageInput = document.getElementById('messageInput');
        const message = messageInput.value.trim();

        if (!message) return;

        fetch("{{ route('chat.send') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({
                    message: message
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log(data);
                if (data.status === 'Message sent!') {
                    $('#chatMessages').append(`<p><strong>Bạn:</strong> ${message}</p>`);
                    $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);
                    messageInput.value = '';
                } else {
                    console.error('Error from server:', data.message);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Pusher để nhận tin nhắn từ admin
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
    console.log('Pusher initialized with key:', '{{ env('PUSHER_APP_KEY') }}', 'and cluster:',
        '{{ env('PUSHER_APP_CLUSTER') }}');

    pusher.connection.bind('connected', function() {
        console.log('Connected to Pusher');
    });
    pusher.connection.bind('error', function(err) {
        console.error('Pusher connection error:', err);
    });
    const channel = pusher.subscribe('chat.user.{{ auth()->id() }}');

    console.log('Subscribing to private-chat.user.{{ auth()->id() }}');

    channel.bind('pusher:subscription_succeeded', function() {
        console.log('Subscribed to private-chat.user.{{ auth()->id() }}');
    });
    channel.bind('pusher:subscription_error', function(error) {
        console.error('Subscription error:', error);
    });

    channel.bind('message.sent', function(data) {
        console.log('Received message from Admin:', data);
        $('#chatMessages').append(`<p><strong>Admin:</strong> ${data.message}</p>`);
        $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);
    });
</script>
