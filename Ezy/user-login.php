<?php
session_start();

// Include the database connection file
include('db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute the SQL query
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the user exists in the database
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Login successful, start the session and store user data
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect the user to their dashboard based on their role
            if ($user['role'] == 'User') {
                header("Location: user-dash.html");
            } elseif ($user['role'] == 'Admin') {
                header("Location: admin-dashboard.php");
            } elseif ($user['role'] == 'Agent') {
                header("Location: agent-dashboard.php");
            }
            exit();
        } else {
            // Incorrect password
            $error = "Invalid username or password.";
            header("Location: user-login.html?error=" . urlencode($error));
            exit();
        }
    } else {
        // Username not found
        $error = "Invalid username or password.";
        header("Location: user-login.html?error=" . urlencode($error));
        exit();
    }
}