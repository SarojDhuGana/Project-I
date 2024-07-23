<?php

class DatabaseConnection
{
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "project";
    public $conn;
    private static $instance;

 
    // Constructor to establish database connection
    public function __construct()
    {
        $this->conn = mysqli_connect($this->servername, $this->username, $this->password, $this->dbname);

        if ($this->conn->connect_error) {
            die("Connection failed: " . mysqli_connect_error());
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new DatabaseConnection();
        }
        return self::$instance;
    }
    // Method to get the database connection
    public function getConnection()
    {
        return $this->conn;
    }

    // Method to close the database connection
    public function closeConnection()
    {
        if ($this->conn) {
            $this->conn->close();
        }

        return $this->conn; // Return the connection after closing it
    }



}


?>