<?php
namespace Models;

class Operator {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function authenticate($email, $password) {
        $sql = "SELECT id, nombre, email, password_hash, estado, telefono
                FROM operators 
                WHERE email = ? AND estado = 'ON'
                LIMIT 1";
                
        $result = $this->db->query($sql, [$email])->fetch();
        
        if ($result && password_verify($password, $result['password_hash'])) {
            unset($result['password_hash']);
            return $result;
        }
        
        return false;
    }
    
    public function updateStatus($id, $status) {
        $sql = "UPDATE operators
                SET estado = ?, ultimo_login = NOW()
                WHERE id = ?";
                
        return $this->db->query($sql, [$status, $id])->rowCount() > 0;
    }
}