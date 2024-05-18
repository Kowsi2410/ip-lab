db

-- Create the database
CREATE DATABASE contact_form_db;

-- Use the database
USE contact_form_db;

-- Create a table for contact form submissions
CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


contact.html

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Form</title>
</head>
<body>
    <h1>Contact Us</h1>
    <form action="submit_contact.php" method="post">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="message">Message:</label><br>
        <textarea id="message" name="message" rows="4" cols="50" required></textarea><br><br>

        <input type="submit" value="Submit">
    </form>
</body>
</html>


submit_contact.php

<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "contact_form_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to validate the form inputs
function validateInputs($name, $email, $message) {
    if (empty($name) || empty($email) || empty($message)) {
        return "All fields are required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Invalid email format.";
    }

    if (!preg_match("/^[a-zA-Z-' ]*$/", $name)) {
        return "Only letters and white space allowed in the name.";
    }

    return true;
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $message = $_POST["message"];

    // Validate inputs
    $validationResult = validateInputs($name, $email, $message);
    if ($validationResult !== true) {
        echo "<p>Error: " . $validationResult . "</p>";
        exit();
    }

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $message);

    if ($stmt->execute()) {
        echo "<p>Thank you for contacting us. Your message has been received.</p>";
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

$conn->close();
?>
