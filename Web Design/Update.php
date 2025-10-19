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
        echo "<script>alert('All fields are required!'); window.location.href='Add.php';</script>";
        exit();
    }

    // Debugging: Verify received values
    error_log("Received data: ActorID=$actorid, FullName=$fullname, Age=$age, Gender=$sex, Role=$role, Email=$email");

    // Check if ActorID already exists before inserting/updating
    $check_query = $data->prepare("SELECT ActorID FROM Add_Actor3 WHERE ActorID = ?");
    
    if (!$check_query) {
        die("Error preparing statement: " . $data->error);
    }

    $check_query->bind_param("s", $actorid);
    $check_query->execute();
    $check_query->store_result();

    if ($check_query->num_rows > 0) {
        // If Actor exists, UPDATE the details
        $check_query->free_result();
        $check_query->close();

        $update_query = $data->prepare("UPDATE Add_Actor3 SET FullName = ?, Age = ?, Gender = ?, Role1 = ?, Email = ? WHERE ActorID = ?");
        
        if (!$update_query) {
            die("Error preparing update statement: " . $data->error);
        }

        $update_query->bind_param("sissss", $fullname, $age, $sex, $role, $email, $actorid);

        if ($update_query->execute()) {
            echo "<script>alert('Actor information updated successfully!'); window.location.href='Add.php';</script>";
        } else {
            error_log("Error updating actor: " . $update_query->error);
            echo "<script>alert('An unexpected error occurred. Please try again later.');</script>";
        }

        $update_query->close();

    } else {
        // If Actor does NOT exist, INSERT as new
        $check_query->close();

        $insert_query = $data->prepare("INSERT INTO Add_Actor3 (ActorID, FullName, Age, Gender, Role1, Email) VALUES (?, ?, ?, ?, ?, ?)");

        if (!$insert_query) {
            die("Error preparing insert statement: " . $data->error);
        }

        $insert_query->bind_param("ssisss", $actorid, $fullname, $age, $sex, $role, $email);

        if ($insert_query->execute()) {
            echo "<script>alert('Actor added successfully!'); window.location.href='Add.php';</script>";
        } else {
            error_log("Error adding actor: " . $insert_query->error);
            echo "<script>alert('An unexpected error occurred. Please try again later.');</script>";
        }

        $insert_query->close();
    }

    // Close connection
    $data->close();
}
?>