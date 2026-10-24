
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// ... your existing database connection code stays here ...
$host = 'localhost';
$dbname = 'blog_system';
$username = 'root'; // Update with your MySQL user
$password = '';     // Update with your MySQL password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Function to generate exactly 20 character alphanumeric ID
function generateBlogId() {
    $length = 20;
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $id = '';
    for ($i = 0; $i < $length; $i++) {
        $id .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $id;
}
?>