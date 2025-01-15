<?php

namespace App\Models;

use PDO;
use PDOException;

class ChatModel
{
    private $db;

    public function __construct()
    {
        $this->db = $this->connectDatabase();
    }

    private function connectDatabase()
    {
        try {
            return new PDO('mysql:host=localhost;dbname=forsti', 'root', '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            throw new \Exception('Database connection failed: ' . $e->getMessage());
        }
    }

    public function fetchUsers()
    {
        $query = $this->db->query("SELECT id_registration, FullName FROM registration");
        return $query->fetchAll();
    }

    public function fetchMessages($senderId, $receiverId)
    {
        $stmt = $this->db->prepare(
            "SELECT sender_id, message FROM messages 
             WHERE (sender_id = :sender AND receiver_id = :receiver)
                OR (sender_id = :receiver AND receiver_id = :sender)
             ORDER BY created_at ASC"
        );
        $stmt->execute(['sender' => $senderId, 'receiver' => $receiverId]);
        return $stmt->fetchAll();
    }

    public function insertMessage($data)
{
    $stmt = $this->db->prepare(
        "INSERT INTO messages (sender_id, receiver_id, message, created_at)
         VALUES (:senderId, :receiverId, :message, NOW())"
    );
    return $stmt->execute($data);
}

}

?>
