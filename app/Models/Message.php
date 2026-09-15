<?php
// app/Models/Message.php

namespace App\Models;

use PDO;

class Message {
    public function __construct(private PDO $db) {}

    /**
     * Finds a single message by ID.
     */
    public function find(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM messages WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $msg = $stmt->fetch();
        return $msg ?: null;
    }

    /**
     * Retrieves all messages tied to a project request.
     */
    public function findByRequest(int $requestId): array {
        $sql = "SELECT m.*, u.name AS sender_name, u.avatar_url AS sender_avatar 
                FROM messages m 
                JOIN users u ON m.sender_id = u.id 
                WHERE m.project_request_id = :request_id 
                ORDER BY m.created_at ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':request_id' => $requestId]);
        return $stmt->fetchAll();
    }

    /**
     * Creates a new message record.
     */
    public function create(array $data): int {
        $stmt = $this->db->prepare("INSERT INTO messages (sender_id, receiver_id, project_request_id, body, is_read, created_at) VALUES (:sender_id, :receiver_id, :project_request_id, :body, 0, NOW())");
        $stmt->execute([
            ':sender_id'          => $data['sender_id'],
            ':receiver_id'        => $data['receiver_id'],
            ':project_request_id' => $data['project_request_id'] ?? null,
            ':body'               => $data['body'],
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Marks a message as read.
     */
    public function markAsRead(int $messageId): bool {
        $stmt = $this->db->prepare("UPDATE messages SET is_read = 1 WHERE id = :id");
        return $stmt->execute([':id' => $messageId]);
    }

    /**
     * Returns count of unread messages for a recipient user.
     */
    public function unreadCount(int $userId): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = :id AND is_read = 0");
        $stmt->execute([':id' => $userId]);
        return (int)$stmt->fetchColumn();
    }
}
