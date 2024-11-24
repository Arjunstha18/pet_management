<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/Database.php'; // Correct path

use Muhammadwasim\StudentCrudTwig\Database;
use Muhammadwasim\StudentCrudTwig\StudentController;

// Create a new Database instance and get the connection
$database = new Database(); 
$db = $database->getConnection();
// Pass the connection to the StudentController
$controller = new StudentController($db);
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

switch (true) { // Switch on `true` to allow `preg_match` conditions to work
    case $path === '/~2357488/student_management/public/':
        $controller->index();
        break;

    case $path === '/~2357488/student_management/public/create':
        $controller->create();
        break;

    case $path === '/~2357488/student_management/public/store' && $method === 'POST':
        $controller->store($_POST); // Store the data
        break;

    case preg_match('/^\/~2357488\/student_management\/public\/edit\/(\d+)$/', $path, $matches):
        // Matches the edit route and captures the ID
        $controller->edit($matches[1]); // Pass the captured ID to the edit method
        break;

    case $path === '/~2357488/student_management/public/update' && $method === 'POST' && isset($_POST['id']):
        $controller->update($_POST); // Update the student data
        break;

    case preg_match('/^\/~2357488\/student_management\/public\/delete\/(\d+)$/', $path, $matches) && $method === 'POST':
        $controller->delete($matches[1]); // Call the delete method with the captured ID
        break;

    case $path === '/~2357488/student_management/public/search' && $method === 'GET' && isset($_GET['search']):
        echo $controller->search($_GET['search']); // Echoing the search results directly
        break;

    default:
        http_response_code(404);
        echo "Page not found, Wasim";
        break;
}
