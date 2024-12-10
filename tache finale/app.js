document.addEventListener('DOMContentLoaded', () => {
    const sendButton = document.getElementById('sendBtn');
    const messageInput = document.getElementById('messageInput');
    const messagesContainer = document.getElementById('messagesContainer');
    const userList = document.getElementById('userList');
    const errorMessage = document.getElementById('errorMessage');

    let senderId = 1;
    let receiverId = null;

    loadUsers();

    sendButton.addEventListener('click', () => {
        const messageText = messageInput.value.trim();
        if (messageText === '') {
            displayError('Please enter a message.');
            return;
        }

        if (!receiverId) {
            displayError('Please select a user to chat with.');
            return;
        }

        sendMessage(messageText);
        messageInput.value = '';
    });

    function loadUsers() {
        fetch('index.php?action=getUsers')
            .then(response => response.json())
            .then(users => {
                if (users.error) {
                    displayError(users.error);
                } else {
                    users.forEach(user => {
                        const li = document.createElement('li');
                        li.textContent = user.username;
                        li.addEventListener('click', () => {
                            receiverId = user.id;
                            loadMessages();
                        });
                        userList.appendChild(li);
                    });
                }
            })
            .catch(error => displayError('Failed to load users: ' + error));
    }

    function loadMessages() {
        if (!receiverId) return;

        fetch(`index.php?action=getMessages&senderId=${senderId}&receiverId=${receiverId}`)
            .then(response => response.json())
            .then(messages => {
                if (messages.error) {
                    displayError(messages.error);
                } else {
                    messagesContainer.innerHTML = '';
                    messages.forEach(msg => {
                        const messageDiv = document.createElement('div');
                        messageDiv.classList.add('message', msg.sender_id === senderId ? 'sent' : 'received');
                        messageDiv.textContent = msg.message;
                        messagesContainer.appendChild(messageDiv);
                    });
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                }
            })
            .catch(error => displayError('Failed to load messages: ' + error));
    }

    function sendMessage(messageText) {
        fetch('index.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=sendMessage&senderId=${senderId}&receiverId=${receiverId}&message=${messageText}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                loadMessages();
            } else {
                displayError(data.error || 'Failed to send message.');
            }
        })
        .catch(error => displayError('Failed to send message: ' + error));
    }

    function displayError(message) {
        errorMessage.textContent = message;
    }
});