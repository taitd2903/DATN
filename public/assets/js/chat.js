function toggleChat() {
    const chatBox = document.getElementById('chatBox');
    if (chatBox.style.display === 'flex') {
        chatBox.style.display = 'none';
        chatButton.style.display = 'block';
    } else {
        chatBox.style.display = 'flex';
        chatButton.style.display = 'none';
    }
}