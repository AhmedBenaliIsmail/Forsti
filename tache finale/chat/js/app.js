document.addEventListener("DOMContentLoaded", () => {
  const sendButton = document.getElementById("sendBtn");
  const messageInput = document.getElementById("messageInput");
  const messagesContainer = document.getElementById("messagesContainer");
  const userList = document.getElementById("userList");
  const errorMessage = document.getElementById("errorMessage");

  let senderId = 1;
  let receiverId = null;

  loadUsers();

  sendButton.addEventListener("click", () => {
    const messageText = messageInput.value.trim();
    if (messageText === "") {
      displayError("Please enter a message.");
      return;
    }

    if (!receiverId) {
      displayError("Please select a user to chat with.");
      return;
    }

    sendMessage(messageText);
    messageInput.value = "";
  });

  messageInput.addEventListener("keydown", (event) => {
    if (event.keyCode === 13) {
      event.preventDefault();
      sendMessage(messageInput.value.trim());
      messageInput.value = "";
    }
  });

  	function loadUsers() {
    fetch("index.php?action=getUsers")
      .then((response) => {
        const result = response.json();
		return result
      })
      .then((users) => {
		console.log(users)
        if (users.error) {
          displayError(users.error);
        } else {
          userList.innerHTML = ""; // Clear existing list
          users.forEach((user) => {
            const li = document.createElement("li");
            li.textContent = user.username;
            li.addEventListener("click", () => {
              userList
                .querySelectorAll("li")
                .forEach((el) => el.classList.remove("active"));
              li.classList.add("active");
              receiverId = user.user_id;
              loadMessages();
            });
            userList.appendChild(li);
          });
        }
      })
      .catch((error) => displayError("Failed to load users: " + error));
  }

  function loadMessages() {
    if (!receiverId) return;
	console.log(receiverId)
    fetch(
      `index.php?action=getMessages&senderId=${encodeURIComponent(
        senderId
      )}&receiverId=${encodeURIComponent(receiverId)}`
    )
      .then((response) => {
        const result = response.json();
        console.log(result)
        return result;
      })
      .then((messages) => {
        if (messages.error) {
          displayError(messages.error);
        } else {
          messagesContainer.innerHTML = "";
          messages.forEach((msg) => {
            const messageDiv = document.createElement("div");
            messageDiv.classList.add(
              "message",
              msg.sender_id === senderId ? "sent" : "received"
            );
            messageDiv.textContent = msg.message;
            messagesContainer.appendChild(messageDiv);
          });
          messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
      })
      .catch((error) => displayError("Failed to load messages: " + error));
  }

  function sendMessage(messageText) {
    const formData = new URLSearchParams({
        action: "sendMessage",
        senderId: senderId,
        receiverId: receiverId,
        message: messageText,
    });

    const url = `index.php?${formData.toString()}`;

    fetch(url, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: formData.toString(),
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }else{
            loadMessages();
        }
      })    
      .catch((error) => displayError("Failed to send message: " + error));
  }

  function displayError(message) {
    console.log(message);
  }
});
