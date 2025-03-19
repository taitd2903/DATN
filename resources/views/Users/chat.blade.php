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
        <p>Chào bạn! Bạn có câu hỏi gì không?.</p>
    </div>
    <div class="chat-input">
        <input type="text" id="messageInput" placeholder="Nhập tin nhắn...">
        <button onclick="sendMessage()">Gửi</button>
    </div>
</div>

@vite(['resources/js/app.js'])

<script>
    function toggleChat() {
        const chatBox = document.getElementById('chatBox');
        const chatButton = document.getElementById('chatButton');
        if (chatBox.style.display === 'flex') {
            chatBox.style.display = 'none';
            chatButton.style.display = 'block';
        } else {
            chatBox.style.display = 'flex';
            chatButton.style.display = 'none';
        }
    }

    function sendMessage() {
        const messageInput = document.getElementById('messageInput');
        const message = messageInput.value;

        if (message.trim() === '') return;

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
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                const chatMessages = document.getElementById('chatMessages');
                const newMessage = document.createElement('p');
                newMessage.textContent = `Bạn: ${message}`;
                chatMessages.appendChild(newMessage);
                chatMessages.scrollTop = chatMessages.scrollHeight;
                messageInput.value = '';
            })
            .catch(error => console.error('Error:', error));
    }
    window.Echo.private(`chat.user.{{ auth()->id() }}`)
        .listen('.message.sent', (e) => {
            const chatMessages = document.getElementById('chatMessages');
            const newMessage = document.createElement('p');
            newMessage.textContent = `Bạn: ${e.message}`;
            chatMessages.appendChild(newMessage);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        });
</script>
