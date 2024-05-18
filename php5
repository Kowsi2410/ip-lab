db

-- Create the database
CREATE DATABASE survey_db;

-- Use the database
USE survey_db;

-- Create a table for survey feedback
CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    feedback TEXT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


suvey.html

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Survey Form</title>
</head>
<body>
    <h1>Survey Form</h1>
    <form action="submit_feedback.php" method="post">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="feedback">Feedback:</label><br>
        <textarea id="feedback" name="feedback" rows="4" cols="50" required></textarea><br><br>

        <label for="rating">Rating (1 to 5):</label>
        <select id="rating" name="rating" required>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
        </select><br><br>

        <input type="submit" value="Submit Feedback">
    </form>
</body>
</html>


submit_feedback.php

<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "survey_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to validate the form inputs
function validateInputs($name, $email, $feedback, $rating) {
    if (empty($name) || empty($email) || empty($feedback) || empty($rating)) {
        return "All fields are required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Invalid email format.";
    }

    if (!preg_match("/^[a-zA-Z-' ]*$/", $name)) {
        return "Only letters and white space allowed in the name.";
    }

    if (!preg_match("/^[1-5]$/", $rating)) {
        return "Rating must be a number between 1 and 5.";
    }

    return true;
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $feedback = $_POST["feedback"];
    $rating = $_POST["rating"];

    // Validate inputs
    $validationResult = validateInputs($name, $email, $feedback, $rating);
    if ($validationResult !== true) {
        echo "<p>Error: " . $validationResult . "</p>";
        exit();
    }

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO feedback (name, email, feedback, rating) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $name, $email, $feedback, $rating);

    if ($stmt->execute()) {
        echo "<p>Thank you for your feedback!</p>";
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

$conn->close();
?>
