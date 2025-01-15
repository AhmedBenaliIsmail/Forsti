<?php

namespace App\Controllers;

use App\Models\ChatModel;

class ChatController
{
    private $model;

    public function __construct()
    {
        $this->model = new ChatModel();
    }

    public function getUsers()
    {
        try {
            $users = $this->model->fetchUsers();
            echo json_encode($users);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getMessages()
    {
        $senderId = $_GET['senderId'] ?? null;
        $receiverId = $_GET['receiverId'] ?? null;

        if (!$senderId || !$receiverId) {
            echo json_encode(['error' => 'Invalid parameters for getting messages.']);
            return;
        }

        try {
            $messages = $this->model->fetchMessages($senderId, $receiverId);
            echo json_encode($messages);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    public function sendMessage($senderId, $receiverId, $message)
    {
        // Remove the header as it's already set in index.php
        // header('Content-Type: application/json');

        // Validate inputs
        if (!$senderId || !$receiverId || !$message) {
            return json_encode([
                'status' => 'error',
                'message' => 'Invalid parameters for sending a message.'
            ]);
        }

        try {
            $data = [
                'senderId' => $senderId,
                'receiverId' => $receiverId,
                'message' => $message
            ];

            // Remove debug print_r
            // error_log(print_r($data, true));  // Use this for debugging if needed

            $result = $this->model->insertMessage($data);

            if ($result) {
                return json_encode([
                    'status' => 'success',
                    'message' => 'Message sent successfully'
                ]);
            } else {
                return json_encode([
                    'status' => 'error',
                    'message' => 'Failed to send message'
                ]);
            }
        } catch (\Exception $e) {
            return json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }


}

?>