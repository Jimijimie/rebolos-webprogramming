<?php

require_once 'database.php';

class Account{
    public $id = '';
    public $first_name = '';
    public $last_name = '';
    public $username = '';
    public $password = '';
    public $role = 'staff';
    public $is_staff = true;
    public $is_admin = false;


    protected $db;

    function __construct(){
        $this->db = new Database();
    }

    function add(){
        try {
            $sql = "INSERT INTO account (first_name, last_name, username, password, role, is_staff, is_admin) VALUES (:first_name, :last_name, :username, :password, :role, :is_staff, :is_admin);";
            $query = $this->db->connect()->prepare($sql);

            $query->bindParam(':first_name', $this->first_name);
            $query->bindParam(':last_name', $this->last_name);
            $query->bindParam(':username', $this->username);
            $hashpassword = password_hash($this->password, PASSWORD_DEFAULT);
            $query->bindParam(':password', $hashpassword);
            $query->bindParam(':role', $this->role);
            $query->bindParam(':is_staff', $this->is_staff);
            $query->bindParam(':is_admin', $this->is_admin);

            return $query->execute();
        } catch(PDOException $e) {
            error_log("Error adding account: " . $e->getMessage());
            return false;
        }
    }

    function usernameExist($username, $excludeID){
        $sql = "SELECT COUNT(*) FROM account WHERE username = :username";
        if ($excludeID){
            $sql .= " and id != :excludeID";
        }

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':username', $username);

        if ($excludeID){
            $query->bindParam(':excludeID', $excludeID);
        }

        $count = $query->execute() ? $query->fetchColumn() : 0;

        return $count > 0;
    }

    function login($username, $password){
        try {
            $sql = "SELECT * FROM account WHERE username = :username LIMIT 1";
            $query = $this->db->connect()->prepare($sql);
            $query->bindParam(':username', $username);
            $query->execute();
            
            $user = $query->fetch(PDO::FETCH_ASSOC);
            if($user) {
                error_log("Found user: " . print_r($user, true));
                if(password_verify($password, $user['password'])){
                    error_log("Password verified successfully");
                    return true;
                }
                error_log("Password verification failed");
            }
            error_log("No user found with username: " . $username);
            return false;
        } catch(PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }

    function fetch($username){
        $sql = "SELECT * FROM account WHERE username = :username LIMIT 1";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':username', $username);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    function getAll(){
        try {
            $sql = "SELECT * FROM account ORDER BY id DESC";
            $query = $this->db->connect()->prepare($sql);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error fetching accounts: " . $e->getMessage());
            return [];
        }
    }

    function delete($id){
        $sql = "DELETE FROM account WHERE id = :id";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':id', $id);
        return $query->execute();
    }
}

// $obj = new Account();

// $obj->add();

