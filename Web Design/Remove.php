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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove'])) {
    $actorid = trim($_POST['aid']);

    // Validate Actor ID format
    if (!preg_match("/^[A-Za-z]{1,2}[0-9]{2}$/", $actorid)) {
        echo "<script>alert('Invalid Actor ID format!'); window.location.href='Remove.php';</script>";
        exit();
    }

    // Debugging message to confirm received ActorID
    error_log("Actor ID received for deletion: " . $actorid);

    // Check if ActorID exists before deleting from `add_actor3`
    $check_query = $data->prepare("SELECT ActorID FROM add_actor3 WHERE ActorID = ?");
    if (!$check_query) {
        die("Error preparing check statement: " . $data->error);
    }

    $check_query->bind_param("s", $actorid);
    $check_query->execute();
    $check_query->store_result();

    if ($check_query->num_rows === 0) {
        echo "<script>alert('Actor ID does not exist!'); window.location.href='Remove.php';</script>";
        $check_query->free_result();
        $check_query->close();
        exit();
    }

    $check_query->free_result();
    $check_query->close();

    // Debugging message before deletion
    error_log("Attempting to delete ActorID: " . $actorid);

    // Delete the actor based on ID
    $delete_query = $data->prepare("DELETE FROM add_actor3 WHERE ActorID = ?");
    if (!$delete_query) {
        die("Error preparing delete statement: " . $data->error);
    }

    $delete_query->bind_param("s", $actorid);

    if ($delete_query->execute()) {
        echo "<script>
                alert('Actor and all associated information removed successfully!');
                window.location.href = 'Remove.php';
            </script>";
        exit();
    } else {
        error_log("Error removing actor: " . $delete_query->error);
        echo "<script>alert('An unexpected error occurred. Please try again later.');</script>";
        exit();
    }

    $delete_query->close();
    $data->close();
}
?>