db

-- Create the database
CREATE DATABASE student_course_db;

-- Use the database
USE student_course_db;

-- Create a single table for students and their course registrations
CREATE TABLE student_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    courses VARCHAR(255) NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

register.html

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Course Registration</title>
</head>
<body>
    <h1>Course Registration</h1>
    <form action="register.php" method="post">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="courses">Select Courses:</label><br>
        <input type="checkbox" name="courses[]" value="Mathematics"> Mathematics<br>
        <input type="checkbox" name="courses[]" value="Science"> Science<br>
        <input type="checkbox" name="courses[]" value="History"> History<br>
        <input type="checkbox" name="courses[]" value="Literature"> Literature<br>
        <input type="checkbox" name="courses[]" value="Computer Science"> Computer Science<br><br>

        <input type="submit" value="Register">
    </form>
</body>
</html>


register.php

<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_course_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to validate the form inputs
function validateInputs($name, $email, $courses) {
    if (empty($name) || empty($email) || empty($courses)) {
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
    $courses = $_POST["courses"];

    // Validate inputs
    $validationResult = validateInputs($name, $email, $courses);
    if ($validationResult !== true) {
        echo "<p>Error: " . $validationResult . "</p>";
        exit();
    }

    // Convert courses array to a comma-separated string
    $coursesStr = implode(", ", $courses);

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO student_registrations (name, email, courses) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $coursesStr);

    if ($stmt->execute()) {
        echo "<p>Registration successful.</p>";
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

$conn->close();
?>
