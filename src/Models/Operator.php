<?php
namespace Models;

class Operator
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function create($data)
    {
        $sql = "INSERT INTO teleoperadores (telefono, nombre, email, password_hash, estado) 
                VALUES (:telefono, :nombre, :email, :password_hash, :estado)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return $this->db->lastInsertId();
    }

    // ... other methods ...
}

