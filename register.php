<?php
session_start();
include("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["regUsername"]);
    $password = md5(trim($_POST["regPassword"]));
    $confirm  = md5(trim($_POST["regConfirmPassword"]));

    if ($password !== $confirm) {
        $_SESSION["register_error"] = "Passwords do not match.";
        header("Location: index.php?tab=register");
        exit();
    }

    $check = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
    mysqli_stmt_bind_param($check, "s", $username);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);

    if (mysqli_stmt_num_rows($check) > 0) {
        $_SESSION["register_error"] = "Username already taken.";
        header("Location: index.php?tab=register");
        exit();
    }

    $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    mysqli_stmt_execute($stmt);

    $_SESSION["register_success"] = "Account created! You can now log in.";
    header("Location: index.php?tab=login");
    exit();
}
?>