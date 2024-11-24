<?php
namespace Muhammadwasim\StudentCrudTwig;

use PDO;
use PDOException;

class Database
{
    // Database connection settings
    private $host = 'mi-linux.wlv.ac.uk';   // Hostname of your database server
    private $db_name = 'db2357488';         // Your database name
    private $username = '2357488';          // Database username
    private $password = 'Cha911%';          // Database password
    public $conn;
    public function getConnection()
    {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, 
                $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
