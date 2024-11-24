<?php

namespace Muhammadwasim\StudentCrudTwig;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use PDO;

class PetController {
    private $twig;
    private $conn;
    private $table = 'pet';
    protected $db;

    // Define pet properties
    public $name;
    public $species;
    public $age;
    public $owner;

    public function __construct($db)
    {
        // Initialize the database connection
        // Initialize the database connection
        $this->conn = $db;
        $this->db = $db;  // Set $db first

        // Set the DB connection in the pet model
        Pet::setDb($this->db);  // Now the database is set in the model

        // Initialize Twig
            $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates'); // Update the path if necessary
            $this->twig = new \Twig\Environment($loader);
    }

    // Method to display all students 
    public function index() {
        session_start();

        // Fetch all students
        $pets = $this->getAllpets();
    
        // Retrieve the session message if it exists
        $message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
    
        // Clear the session message after retrieving it
        unset($_SESSION['message']);
    
        // Render the Twig template with students and message
        echo $this->twig->render('index.html.twig', [
            'pet' => $pets,
            'message' => $message,
        ]);
    }

    // Method to display the create student form
    public function create() {
        echo $this->twig->render('create.html.twig');
    }

    // Method to store a new student
    public function store($data)
    {
        // Sanitize and assign data
        $this->name = $data['name'];
        $this->species = $data['species'];
        $this->age = $data['age'];
        $this->owner = $data['owner'];

        // SQL query to insert student into the database
        $query = "INSERT INTO " . $this->table . " (name, species, age, owner) 
                  VALUES (:name, :species, :age, :owner)";
    
        // Prepare the query
        $stmt = $this->conn->prepare($query);
    
        // Bind values
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':species', $this->species);
        $stmt->bindParam(':age', $this->age);
        $stmt->bindParam(':owner', $this->owner);
        session_start();
        // Execute the query and return success or failure
        if ($stmt->execute()) {
            $_SESSION['message'] = "Pet Created successfully.";

                // Redirect back to the index route (e.g., '/')
                header("Location: /~2359011/pet_management/public/"); 
                exit;
        }
    
        $_SESSION['message'] = "Pet not found.";
        header("Location: /");
        exit;
    }

    // Method to display the edit form with student data

    public function edit($id)
    {
        $pet = Pet::find($id);
        if ($pet) {
            // Render the edit form with pet data
            echo $this->twig->render('edit.html.twig', ['pet' => $pet]);
        } else {
            http_response_code(404); // If pet not found
            echo "pet not found.";
        }
    }
    

    public function update($data)
    {
        if (isset($data['id'])) {
            // Use the Student model to find the student
            $pet = Pet::find($data['id']);
            
            if ($pet) {
                // Now use the model's database connection to update the pet data
                $stmt = Pet::$db->prepare("UPDATE pet SET name = :name, species = :species, age = :age, owner = :owner WHERE id = :id");
                $stmt->bindParam(':id', $data['id']);
                $stmt->bindParam(':name', $data['name']);
                $stmt->bindParam(':species', $data['species']);
                $stmt->bindParam(':age', $data['age']);
                $stmt->bindParam(':owner', $data['owner']);
                $stmt->execute();
                session_start();

                // Store the success message in the session
                $_SESSION['message'] = "Pet updated successfully.";
              
                // Redirect back to the index route (e.g., '/')
                header("Location: /~2359011/pet_management/public/");
                exit;           
             } else {
                   // Store the error message in the session
                    $_SESSION['message'] = "Pet not found.";
                    header("Location: /~2359011/pet_management/public/");
                    exit;
            }
        } else {
            $_SESSION['message'] = "Invalid data.";
            header("Location: /~2359011/pet_management/public/");
            exit;
        }
    }
    
    public function delete($id) {
        session_start(); // Start the session
    
        // Assuming $this->deletepetById($id) deletes the pet from the database
        if ($this->deletepetById($id)) {
            $_SESSION['message'] = "Pet deleted successfully.";
            http_response_code(200); // Success response
            echo "pet deleted successfully.";
        } else {
            $_SESSION['message'] = "Failed to delete the pet.";
            http_response_code(400); // Error response
            echo "Failed to delete the pet.";
        }
    
        // Redirect to the index page
        // header("Location: /");
        exit;
    }
    

    // Method to get all pets
    private function getAllpets() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Method to find a pet by ID
    private function findpetById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function search($query)
    {
        // Prepare the SQL query to search by name, species, age, or owner
        $stmt = $this->conn->prepare("SELECT * FROM pet WHERE name LIKE :query OR species LIKE :query OR age LIKE :query OR owner LIKE :query");
        $searchTerm = "%$query%"; // Add wildcard for searching
        $stmt->bindParam(':query', $searchTerm, PDO::PARAM_STR);
        $stmt->execute();
    
        $pets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Loop through the pets and create table rows
        $html = '';
        foreach ($pets as $pet) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($pet['name']) . '</td>';
            $html .= '<td>' . htmlspecialchars($pet['species']) . '</td>';
            $html .= '<td>' . htmlspecialchars($pet['age']) . '</td>';
            $html .= '<td>' . htmlspecialchars($pet['owner']) . '</td>';
            $html .= '<td>';
            $html .= '<a href="/~2359011/pet_management/public/edit/' . $pet['id'] . '" class="btn btn-warning btn-sm mr-2" style="margin-right: 5px; padding: 5px 15px;">Edit</a>';
            $html .= '<a href="#" class="btn btn-danger btn-sm" style="padding: 5px 10px;" 
               onclick="if (confirm(\'Are you sure you want to delete this Pet?\')) { 
                   fetch(\'/~2359011/pet_management/public/delete/' . $pet['id'] . '\', { 
                       method: \'POST\' 
                   })
                   .then(response => {
                       if (response.ok) {
                           return response.text(); // Expecting a session message to be set
                       } else {
                           throw new Error(\'Failed to delete\');
                       }
                   })
                   .then(() => {
                       location.reload(); // Reload after delete is successful
                   })
                   .catch(error => {
                       console.error(\'Error:\', error);
                   }); 
               } return false;">
               Delete
            </a>';
            $html .= '</td>';
            $html .= '</tr>';
        }
    
        // Return the filtered table rows directly for Ajax to process
        echo $html;
        exit; // Ensure nothing else is sent
    }
    private function deletepetById($id) {
        // Assuming $this->db is your database connection
        $stmt = $this->db->prepare("DELETE FROM pet WHERE id = ?");
        return $stmt->execute([$id]);
    }
    

}
