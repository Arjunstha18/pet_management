<?php
namespace Muhammadwasim\StudentCrudTwig;
use PDO;

class Pet
{
    public $id;
    public $name;
    public $species;
    public $age;
    public $owner;

    // Assume we have a PDO instance in the Database class
    public static $db;

    // Set the PDO database connection
    public static function setDb($db)
    {
        self::$db = $db;
    }

    public static function all()
    {
        // Fetch all pet from the database
        $stmt = self::$db->query("SELECT * FROM pet");
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Fetch a student by ID
    public static function find($id)
    {
        // Ensure db connection is set
        if (self::$db === null) {
            throw new \Exception("Database connection is not set.");
        }

        $stmt = self::$db->prepare("SELECT * FROM pet WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function save()
    {
        // Insert or update the student
        if ($this->id) {
            // Update existing record
            $stmt = self::$db->prepare("UPDATE pet SET name = :name, species = :species, age = :age, owner = :owner WHERE id = :id");
            $stmt->bindParam(':id', $this->id);
        } else {
            // Insert new record
            $stmt = self::$db->prepare("INSERT INTO pet (name, species, age, owner) VALUES (:name, :species, :age, :owner)");
        }

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':species', $this->species);
        $stmt->bindParam(':age', $this->age);
        $stmt->bindParam(':owner', $this->owner);
        $stmt->execute();
    }
}


