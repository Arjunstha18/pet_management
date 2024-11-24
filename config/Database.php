<?php
namespace Muhammadwasim\StudentCrudTwig;

use PDO;
use PDOException;

class Database
{
    // Database connection settings
    private $host = 'mi-linux.wlv.ac.uk';   // Hostname of your database server
    private $db_name = 'db2359011';         // Your database name
    private $username = '2359011';           // Database username
    private $password = '#Venom$&1515';          // Database password
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
