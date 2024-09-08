<?php

// Include the Database class from the 'database.php' file.
require_once 'database.php';

// The Product class handles operations related to products in the database.
class Product {
    // These properties represent the columns in the 'product' table.
    public $id = '';            // The ID of the product. Typically used when updating or deleting a product.
    public $code = '';          // The unique code of the product.
    public $name = '';          // The name of the product.
    public $category = '';      // The category the product belongs to.
    public $price = '';         // The price of the product.
    public $availability = '';  // Availability status of the product (e.g., 'In Stock', 'Out of Stock').

    protected $db; // This will hold an instance of the Database class for database operations.

    // The constructor method initializes the Product class by creating a new Database object.
    function __construct() {
        $this->db = new Database(); // Instantiate the Database class.
    }

    // The add() method is used to add a new product to the database.
    function add() {
        // SQL query to insert a new product into the 'product' table.
        $sql = "INSERT INTO product (code, name, category, price, availability) VALUES (:code, :name, :category, :price, :availability);";

        // Prepare the SQL statement for execution.
        $query = $this->db->connect()->prepare($sql);

        // Bind the product properties to the named placeholders in the SQL statement.
        $query->bindParam(':code', $this->code);
        $query->bindParam(':name', $this->name);
        $query->bindParam(':category', $this->category);
        $query->bindParam(':price', $this->price);
        $query->bindParam(':availability', $this->availability);

        // Execute the query. If successful, return true; otherwise, return false.
        return $query->execute();
    }

    // The showAll() method retrieves all products from the database and returns them.
    function showAll($keyword='', $category='') {
        // SQL query to select all products, ordered alphabetically by name.
        // If keyword or category are provided, they are used for filtering the results.
        $sql = "SELECT * FROM product WHERE (code LIKE CONCAT('%', :keyword, '%') OR name LIKE CONCAT('%', :keyword, '%') OR category LIKE CONCAT('%', :keyword, '%')) AND (category LIKE CONCAT('%', :category, '%')) ORDER BY name ASC;";

        // Prepare the SQL statement for execution.
        $query = $this->db->connect()->prepare($sql);

        // Bind the keyword and category parameters to the SQL query.
        $query->bindParam(':keyword', $keyword);
        $query->bindParam(':category', $category);

        $data = null; // Initialize a variable to hold the fetched data.

        // Execute the query. If successful, fetch all the results into an array.
        if ($query->execute()) {
            $data = $query->fetchAll(); // Fetch all rows from the result set.
        }

        return $data; // Return the fetched data.
    }

    // The edit() method is used to update an existing product in the database.
    function edit() {
        // SQL query to update an existing product in the 'product' table.
        $sql = "UPDATE product SET code = :code, name = :name, category = :category, price = :price, availability = :availability WHERE id = :id;";

        // Prepare the SQL statement for execution.
        $query = $this->db->connect()->prepare($sql);

        // Bind the product properties and ID to the SQL statement.
        $query->bindParam(':code', $this->code);
        $query->bindParam(':name', $this->name);
        $query->bindParam(':category', $this->category);
        $query->bindParam(':price', $this->price);
        $query->bindParam(':availability', $this->availability);
        $query->bindParam(':id', $this->id);

        // Execute the query. If successful, return true; otherwise, return false.
        return $query->execute();
    }

    // The fetchRecord() method retrieves a single product record from the database based on its ID.
    function fetchRecord($recordID) {
        // SQL query to select a single product based on its ID.
        $sql = "SELECT * FROM product WHERE id = :recordID;";

        // Prepare the SQL statement for execution.
        $query = $this->db->connect()->prepare($sql);

        // Bind the recordID parameter to the SQL query.
        $query->bindParam(':recordID', $recordID);

        $data = null; // Initialize a variable to hold the fetched data.

        // Execute the query. If successful, fetch the result.
        if ($query->execute()) {
            $data = $query->fetch(); // Fetch the single row from the result set.
        }

        return $data; // Return the fetched data.
    }

    // The delete() method removes a product from the database based on its ID.
    function delete($recordID) {
        // SQL query to delete a product by its ID.
        $sql = "DELETE FROM product WHERE id = :recordID;";

        // Prepare the SQL statement for execution.
        $query = $this->db->connect()->prepare($sql);

        // Bind the recordID parameter to the SQL query.
        $query->bindParam(':recordID', $recordID);
        
        // Execute the query. If successful, return true; otherwise, return false.
        return $query->execute();
    }

    // The codeExists() method checks if a product code already exists in the database.
    // It can exclude a specific product ID when performing the check (useful during updates).
    function codeExists($code, $excludeID = null) {
        // SQL query to check if the product code exists.
        $sql = "SELECT COUNT(*) FROM product WHERE code = :code";

        // If $excludeID is provided, modify the SQL query to exclude the record with this ID.
        if ($excludeID) {
            $sql .= " AND id != :excludeID";
        }

        // Prepare the SQL statement.
        $query = $this->db->connect()->prepare($sql);

        // Bind the parameters.
        $query->bindParam(':code', $code);

        if ($excludeID) {
            $query->bindParam(':excludeID', $excludeID);
        }

        // Execute the query.
        $query->execute();

        // Fetch the count. If it's greater than 0, the code already exists.
        $count = $query->fetchColumn();

        return $count > 0;
    }
}

// Uncomment the lines below to test the Product class methods.

// Create a new Product instance and display all products.
// $obj = new Product();
// var_dump($obj->showAll());

// Uncomment to add a product using the add() method.
// $obj->add();

// Uncomment to update a product using the edit() method.
// $obj->edit(1);

// Uncomment to fetch a product's record by ID.
// var_dump($obj->fetchRecord(1));