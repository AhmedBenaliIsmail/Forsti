document.addEventListener('DOMContentLoaded', () => {
    const sendButton = document.getElementById('sendBtn');
    const messageInput = document.getElementById('messageInput');
    const messagesContainer = document.getElementById('messagesContainer');

   
    function sendMessage() {
        const messageText = messageInput.value.trim();

       
        if (messageText === '') {
            alert('Please enter a message!');
            return;
        }

       
        addMessage(messageText, 'sent');

       
        setTimeout(() => {
            addMessage('This is an automated reply.', 'received');
        }, 1000);

       
        messageInput.value = '';
    }

    
    function addMessage(text, type) {
        const messageDiv = document.createElement('div');
        messageDiv.classList.add('message', type);
        messageDiv.textContent = text;
        messagesContainer.appendChild(messageDiv);

        
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

  
    sendButton.addEventListener('click', sendMessage);

    
    messageInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault(); 
            sendMessage();
        }
    });
});
