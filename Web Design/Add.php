<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$user = "root";
$password = "";
$db = "addactor";

// Establish database connection
$data = new mysqli($host, $user, $password, $db);

// Check connection
if ($data->connect_error) {
    die("Connection failed: " . $data->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture and sanitize user input
    $actorid = trim($_POST['aid']);
    $fullname = trim($_POST['fullname']);
    $age = intval($_POST['age']);
    $sex = trim($_POST['sex']);
    $role = trim($_POST['role']);
    $email = trim($_POST['email']);

    // Validate fields to prevent empty values
    if (empty($actorid) || empty($fullname) || empty($age) || empty($sex) || empty($role) || empty($email)) {
        die("<script>alert('All fields are required!'); window.location.href='Add.php';</script>");
    }

    // Debugging: Verify received values
    error_log("Received Data: ActorID=$actorid, FullName=$fullname, Age=$age, Gender=$sex, Role=$role, Email=$email");

    // Check if ActorID already exists
    $check_query = $data->prepare("SELECT ActorID FROM Add_Actor3 WHERE ActorID = ?");
    if (!$check_query) {
        die("Error preparing duplicate check: " . $data->error);
    }

    $check_query->bind_param("s", $actorid);
    $check_query->execute();
    $check_query->store_result();

    if ($check_query->num_rows > 0) {
        $check_query->free_result();
        $check_query->close();
        die("<script>alert('ActorID already exists!'); window.location.href='Add.php';</script>");
    }

    $check_query->free_result();
    $check_query->close();

    // Debugging: Before INSERT
    error_log("Attempting to insert ActorID: $actorid");

    // Insert new actor into Add_Actor3 table
    $query = $data->prepare("INSERT INTO Add_Actor3 (ActorID, FullName, Age, Gender, Role1, Email) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$query) {
        die("Error preparing insert statement: " . $data->error);
    }

    $query->bind_param("ssisss", $actorid, $fullname, $age, $sex, $role, $email);

    if ($query->execute()) {
        echo "<script>alert('Actor added successfully!'); window.location.href='Add.php';</script>";
    } else {
        error_log("Error adding actor: " . $query->error);
        die("Insertion failed: " . $query->error);
    }

    // Close resources
    $query->close();
    $data->close();
}
?>