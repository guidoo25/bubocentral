<?php
namespace Models;

class Session {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function create($operatorId) {
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        $sql = "INSERT INTO operator_sessions (operator_id, token, expires_at)
                VALUES (?, ?, ?)";
                
        $this->db->query($sql, [$operatorId, $token, $expiresAt]);
        return $token;
    }
    
    public function validate($token) {
        $sql = "SELECT os.*, o.nombre, o.email, o.estado, o.telefono
                FROM operator_sessions os
                JOIN operators o ON o.id = os.operator_id
                WHERE os.token = ?
                AND os.expires_at > NOW()
                AND o.estado = 'ON'
                LIMIT 1";
                
        return $this->db->query($sql, [$token])->fetch();
    }
    
    public function invalidate($token) {
        $sql = "DELETE FROM operator_sessions WHERE token = ?";
        return $this->db->query($sql, [$token])->rowCount() > 0;
    }
}