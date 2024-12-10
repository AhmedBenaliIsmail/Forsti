<?php
include 'php/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($_GET['action'] == 'getMessages') {
        fetchMessages($_GET['senderId'], $_GET['receiverId']);
    }
    if ($_GET['action'] == 'getUsers') {
        fetchUsers();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] == 'sendMessage') {
    sendMessage($_POST['senderId'], $_POST['receiverId'], $_POST['message']);
}

function fetchUsers() {
    global $pdo;
    try {
        $sql = "SELECT * FROM users";
        $stmt = $pdo->query($sql);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($users);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Failed to fetch users: ' . $e->getMessage()]);
    }
}

function fetchMessages($senderId, $receiverId) {
    global $pdo;
    try {
        $sql = "SELECT * FROM messages WHERE (sender_id = :senderId AND receiver_id = :receiverId) OR (sender_id = :receiverId AND receiver_id = :senderId) ORDER BY created_at ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['senderId' => $senderId, 'receiverId' => $receiverId]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($messages);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Failed to fetch messages: ' . $e->getMessage()]);
    }
}

function sendMessage($senderId, $receiverId, $message) {
    global $pdo;
    try {
        $sql = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (:senderId, :receiverId, :message)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['senderId' => $senderId, 'receiverId' => $receiverId, 'message' => $message]);
        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Failed to send message: ' . $e->getMessage()]);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat App</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<div class="chat-container">
   
    <div class="user-list">
        <h3>Users</h3>
        <ul id="userList">
           
    </div>

    
    <div class="chat-box">
        <div class="messages" id="messagesContainer">
            
        </div>
        <textarea id="messageInput" placeholder="Type your message..."></textarea>
        <button id="sendBtn">Send</button>
        <div id="errorMessage" style="color: red; margin-top: 10px;"></div>
    </div>
</div>

<script src="js/app.js"></script>
</body>
</html>