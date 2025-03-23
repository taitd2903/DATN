<div class="chat-button" id="chatButton" onclick="toggleChat()">
    <span>Chat với Admin</span>
</div>

<!-- Hộp thoại chat -->
<div class="chat-box" id="chatBox">
    <div class="chat-header">
        <span>Chat với Admin</span>
        <button class="close-btn" onclick="toggleChat()">
            <img src="https://cdn-icons-png.flaticon.com/128/12613/12613236.png" alt=""
                style="width: 25px; height: 23px;">
        </button>
    </div>
    <div class="chat-content" id="chatMessages">
        @guest
            <p class="login-prompt">Bạn hãy đăng nhập để tiếp tục nhé!</p>
        @endguest
    </div>
    <div class="chat-input" @guest style="display: none;" @endguest>
        <input type="text" id="messageInput" placeholder="Nhập tin nhắn...">
        <button onclick="sendMessage()">Gửi</button>
    </div>
</div>
<div id="newMessageNotification" class="notification" style="display: none;">
    <p id="notificationMessage">Bạn có tin nhắn mới!</p>
    <button onclick="closeNotification()">Đóng</button>
</div>
<audio id="notificationSound" src="/assets/sound/minion.mp3" preload="auto"></audio>

@vite(['resources/js/app.js'])
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://js.pusher.com/7.0/pusher.min.js"></script>
<script>
    function formatDate(dateString) {
        const date = new Date(dateString);
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        return `${day}/${month}/${year}`;
    }

    function formatTime(dateString) {
        const date = new Date(dateString);
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        return `${hours}:${minutes}`;
    }

    function closeNotification() {
        $('#newMessageNotification').hide();
    }

    function toggleChat() {
        const chatBox = document.getElementById('chatBox');
        const chatButton = document.getElementById('chatButton');
        chatBox.style.display = chatBox.style.display === 'flex' ? 'none' : 'flex';
        chatButton.style.display = chatBox.style.display === 'flex' ? 'none' : 'block';
        @if (auth()->check())
            if (chatBox.style.display === 'flex') {
                loadChatHistory();
                $('#newMessageNotification').hide();
            }
        @endif
    }

    function loadChatHistory() {
        $.get('/chat/history', function(response) {
            $('#chatMessages').empty();
            let currentDate = "";
            response.messages.forEach(function(msg) {
                const msgDate = msg.date;
                const msgTime = msg.time; 

                if (msgDate !== currentDate) {
                    $('#chatMessages').append(`<div class="date-separator">${msgDate}</div>`);
                    currentDate = msgDate;
                }
                let sender = msg.is_admin ? 'Admin' : 'Bạn';
                $('#chatMessages').append(
                    `<p><strong>${sender}:</strong> ${msg.message} <span>(${msgTime})</span></p>`);
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
                if (data.status === 'Message sent!') {
                    $('#chatMessages').append(
                        `<p><strong>Bạn:</strong> ${message} <span>(${formatTime(new Date())})</span></p>`);
                    $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);
                    messageInput.value = '';
                }
            });
    }

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

    @if (auth()->check())
        const channel = pusher.subscribe('chat.user.{{ auth()->id() }}');
        channel.bind('message.sent', function(data) {
            const today = data.date;
            const lastDate = $('#chatMessages .date-separator:last').text();
            if (lastDate !== today) {
                $('#chatMessages').append(`<div class="date-separator">${today}</div>`);
            }
            $('#chatMessages').append(
                `<p><strong>Admin:</strong> ${data.message} <span>(${formatTime(new Date())})</span></p>`);
            $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);

            const chatBox = document.getElementById('chatBox');
            if (chatBox.style.display !== 'flex') {
                $('#notificationMessage').text('Bạn có tin nhắn mới!');
                $('#newMessageNotification').show();
                document.getElementById('notificationSound').play();

                setTimeout(() => {
                    $('#newMessageNotification').hide();
                }, 5000);
            }
        });
    @endif
</script>
