<?php
include "config.php";
$conn = open_connection();

session_start();

if (isset($_SESSION["is_login"]) && $_SESSION["is_login"] == 1) {

    header('Location: ' . 'dashboard.php');

    die();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Jirattin</title>
    <link rel="icon" type="image/x-icon" href="css/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.2.1/css/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div class="container-fluid">
        <form id="loginForm" method="post" class="mx-auto">
            <h4 class="text-center">Jirattin (੭•͈ω•͈)੭</h4>
            <div class="mb-3 mt-5">
                <label for="nickname" class="form-label"><i class="bi bi-person-circle"></i> Email:</label>
                <input type="email" class="form-control" name="email" placeholder="Enter your email •⩊•" required>
            </div>
            <div class="mb-1">
                <label for="password" class="form-label"><i class="bi bi-key"></i></i> Password:</label>
                <input type="password" class="form-control" name="password" placeholder="Password •⩊•" required>
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-primary mt-5"><i class="bi bi-unlock"></i> Login </button>
            </div>
            <!-- <div class="mb-2">
                <h5>Not yet registered? <a href="createuser.php">Register here!</a></h5>
            </div> -->
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="js/script.js"></script>
</body>

</html>