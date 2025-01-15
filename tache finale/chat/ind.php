<?php

// Autoload dependencies or manually include required files
require_once __DIR__ . '/Controllers/ChatController.php';
require_once __DIR__ . '/Models/ChatModel.php';

// Use the controller to handle requests
use App\Controllers\ChatController;

// Set content type for API responses
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    header('Content-Type: application/json');

    try {
        // Instantiate the ChatController
        $controller = new ChatController();

        // Determine the action from the request
        $action = $_GET['action'] ?? null;

        // Route the action to the appropriate method
        switch ($action) {
            case 'getUsers':
                $controller->getUsers();
                break;

            case 'getMessages':
                $controller->getMessages();
                break;

            case 'sendMessage':
                $controller->sendMessage();
                break;

            default:
                http_response_code(404);
                echo json_encode(['error' => 'Invalid action specified.']);
                break;
        }
    } catch (Exception $e) {
        // Handle unexpected errors
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }

    exit; // Stop further processing for API requests
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat App</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        #chatApp {
            display: flex;
            height: 100%;
        }
        #userList {
            width: 25%;
            border-right: 1px solid #ccc;
            padding: 10px;
            list-style: none;
            margin: 0;
            overflow-y: auto;
        }
        #userList li {
            padding: 10px;
            cursor: pointer;
        }
        #userList li.active {
            background-color: #ddd;
        }
        #messagesContainer {
            flex: 1;
            padding: 10px;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }
        #messagesContainer .message {
            margin: 5px 0;
            padding: 10px;
            border-radius: 5px;
            max-width: 70%;
        }
        .sent {
            background-color: #e1ffc7;
            align-self: flex-end;
        }
        .received {
            background-color: #f1f1f1;
            align-self: flex-start;
        }
        #chatInput {
            display: flex;
            border-top: 1px solid #ccc;
            padding: 10px;
        }
        #messageInput {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-right: 10px;
        }
        #sendBtn {
            padding: 10px;
            border: none;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        #sendBtn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div id="chatApp">
        <ul id="userList"></ul>
        <div id="messagesContainer"></div>
    </div>
    <div id="chatInput">
        <input type="text" id="messageInput" placeholder="Type a message...">
        <button id="sendBtn">Send</button>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const sendButton = document.getElementById("sendBtn");
            const messageInput = document.getElementById("messageInput");
            const messagesContainer = document.getElementById("messagesContainer");
            const userList = document.getElementById("userList");

            let senderId = 1; // Example sender ID
            let receiverId = null;

            function displayError(message) {
                console.error(message);
            }

            function loadUsers() {
                fetch("?action=getUsers")
                    .then((response) => response.json())
                    .then((users) => {
                        if (users.error) {
                            displayError(users.error);
                        } else {
                            userList.innerHTML = "";
                            users.forEach((user) => {
                                const li = document.createElement("li");
                                li.textContent = user.FullName;
                                li.addEventListener("click", () => {
                                    userList.querySelectorAll("li").forEach((el) => el.classList.remove("active"));
                                    li.classList.add("active");
                                    receiverId = user.id_registration;
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
                fetch(`?action=getMessages&senderId=${senderId}&receiverId=${receiverId}`)
                    .then((response) => response.json())
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
                if (!receiverId) {
                    displayError("Please select a user to chat with.");
                    return;
                }

                const formData = new URLSearchParams({
                    action: "sendMessage",
                    senderId: senderId,
                    receiverId: receiverId,
                    message: messageText,
                });

                fetch("?", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: formData.toString(),
                })
                    .then((response) => response.json())
                    .then(() => {
                        loadMessages();
                    })
                    .catch((error) => displayError("Failed to send message: " + error));
            }

            sendButton.addEventListener("click", () => {
                const messageText = messageInput.value.trim();
                if (messageText) {
                    sendMessage(messageText);
                    messageInput.value = "";
                }
            });

            messageInput.addEventListener("keydown", (event) => {
                if (event.key === "Enter") {
                    const messageText = messageInput.value.trim();
                    if (messageText) {
                        sendMessage(messageText);
                        messageInput.value = "";
                    }
                }
            });

            loadUsers();
        });
    </script>
</body>
</html>
