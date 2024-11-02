<?php
require_once 'database.php';

class Permission {
    public $id = '';
    public $name = '';
    public $description = '';
    protected $db;

    function __construct() {
        $this->db = new Database();
    }

    function add() {
        $sql = "INSERT INTO permissions (name, description) VALUES (:name, :description)";
        $query = $this->db->connect()->prepare($sql);
        
        $query->bindParam(':name', $this->name);
        $query->bindParam(':description', $this->description);
        
        return $query->execute();
    }

    function getAll() {
        $sql = "SELECT p.*, COUNT(rp.role_id) as roles_count 
                FROM permissions p 
                LEFT JOIN role_permissions rp ON p.id = rp.permission_id 
                GROUP BY p.id 
                ORDER BY p.id DESC";
        $query = $this->db->connect()->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    function delete($id) {
        $sql = "DELETE FROM permissions WHERE id = :id";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':id', $id);
        return $query->execute();
    }
}
