<?php

// The Database class is designed to handle the connection to a MySQL database.
class Database{
    // These are the properties that store the database connection details.
    private $host = 'localhost';      // The hostname of the database server.
    private $username = 'root';       // The username used to connect to the database.
    private $password = '';           // The password used to connect to the database (empty string means no password).
    private $dbname = 'sample_db';  // Make sure this matches your database name

    protected $connection; // This property will hold the PDO connection object once connected.

    // The connect() method is used to establish a connection to the database.
    function connect(){
        if($this->connection === null){
            try {
                $this->connection = new PDO(
                    "mysql:host=$this->host;dbname=$this->dbname", 
                    $this->username, 
                    $this->password
                );
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                error_log("Database connected successfully");
            } catch(PDOException $e) {
                error_log("Connection failed: " . $e->getMessage());
                die("Connection failed: " . $e->getMessage());
            }
        }
        return $this->connection;
    }
}

// Uncomment the lines below to test the connection by creating an instance of the Database class and calling the connect() method.
// $obj = new Database();
// $obj->connect();
