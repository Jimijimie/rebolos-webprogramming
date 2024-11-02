<?php
require_once 'database.php';

class Role {
    public $id = '';
    public $name = '';
    public $description = '';
    protected $db;

    function __construct() {
        $this->db = new Database();
    }

    function add() {
        $sql = "INSERT INTO roles (name, description) VALUES (:name, :description)";
        $query = $this->db->connect()->prepare($sql);
        
        $query->bindParam(':name', $this->name);
        $query->bindParam(':description', $this->description);
        
        return $query->execute();
    }

    function getAll() {
        $sql = "SELECT r.*, COUNT(DISTINCT a.id) as users_count, COUNT(DISTINCT rp.permission_id) as permissions_count 
                FROM roles r 
                LEFT JOIN account a ON r.name = a.role 
                LEFT JOIN role_permissions rp ON r.id = rp.role_id 
                GROUP BY r.id 
                ORDER BY r.id DESC";
        $query = $this->db->connect()->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    function delete($id) {
        $sql = "DELETE FROM roles WHERE id = :id";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':id', $id);
        return $query->execute();
    }

    function assignPermissions($roleId, $permissionIds) {
        $this->db->connect()->beginTransaction();
        
        try {
            // Delete existing permissions
            $sql = "DELETE FROM role_permissions WHERE role_id = :role_id";
            $query = $this->db->connect()->prepare($sql);
            $query->bindParam(':role_id', $roleId);
            $query->execute();
            
            // Insert new permissions
            $sql = "INSERT INTO role_permissions (role_id, permission_id) VALUES (:role_id, :permission_id)";
            $query = $this->db->connect()->prepare($sql);
            
            foreach ($permissionIds as $permissionId) {
                $query->bindParam(':role_id', $roleId);
                $query->bindParam(':permission_id', $permissionId);
                $query->execute();
            }
            
            $this->db->connect()->commit();
            return true;
        } catch (Exception $e) {
            $this->db->connect()->rollBack();
            return false;
        }
    }
}
