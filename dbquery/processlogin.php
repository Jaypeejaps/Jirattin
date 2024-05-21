<?php
include "../config.php";
$conn = open_connection();

if (isset($_POST['email']) && $_POST['password']) {

    $employee_email = $_POST['email'];
    $employee_password = $_POST['password'];

    $sql = "SELECT * FROM tblemployee WHERE employee_email='" . $employee_email . "' AND employee_password='" . $employee_password . "'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    if (mysqli_num_rows($result) == 1) {
        session_start();

        $_SESSION["is_login"] = 1;

        $_SESSION["employee_id"] = $row["employee_id"];
        $_SESSION["employee_nickname"] = $row["employee_nickname"];
        $_SESSION["userlevel_id"] = $row["userlevel_id"];
        echo "success";
    } else {
        echo "error";
    }
}
