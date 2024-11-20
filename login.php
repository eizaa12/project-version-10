<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Database connection details
$host = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'signup';

// Create a connection to the database
$conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);

// Check connection
if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve and sanitize form data
    $Email = isset($_POST['Email']) ? $conn->real_escape_string($_POST['Email']) : '';
    $Password = isset($_POST['Password']) ? $_POST['Password'] : '';
    $role = isset($_POST['role']) ? $conn->real_escape_string($_POST['role']) : '';

    // Check for missing inputs
    if (empty($Email) || empty($Password) || empty($role)) {
        echo "Email, password, or role is missing.";
        exit();
    }

    // Prepare SQL statement to retrieve user by email and role
    $stmt = $conn->prepare("SELECT * FROM signup_details WHERE Email = ? AND Role = ?");
    $stmt->bind_param("ss", $Email, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the user exists
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Compare the passwords directly (since passwords are not hashed)
        if ($Password === $user['Password']) {
            // Password is correct; set session variables
            $_SESSION['user_name'] = $user['FullName'];
            $_SESSION['user_role'] = $user['Role'];

            // Redirect to the home page
            header("Location: home.html");
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "Invalid email or role.";
    }

    // Close the statement and connection
    $stmt->close();
} else {
    echo "Invalid request method.";
}
$conn->close();
?>
