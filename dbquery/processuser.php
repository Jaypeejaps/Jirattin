<?php

include "../config.php";
$conn = open_connection();

if (
    isset($_POST['empname']) && isset($_POST['empnickname']) && isset($_POST['empemail']) && isset($_POST['empnumber']) &&
    isset($_POST['empdepartment']) && isset($_POST['empuserlevel']) && isset($_POST['emppassword'])
) {

    $employee_name = $_POST['empname'];
    $employee_nickname = $_POST['empnickname'];
    $employee_email = $_POST['empemail'];
    $employee_number = $_POST['empnumber'];
    $employee_password = $_POST['emppassword'];
    $department_id = $_POST['empdepartment'];
    $userlevel_id = $_POST['empuserlevel'];

    $createQuery = "INSERT INTO tblemployee(employee_name, employee_nickname, employee_email, employee_number, employee_password,
    department_id, userlevel_id) VALUES ('$employee_name', '$employee_nickname', '$employee_email', '$employee_number', '$employee_password',
    '$department_id', '$userlevel_id')";

    $query = mysqli_query($conn, $createQuery);

    if ($query) {
        $employee_id = mysqli_insert_id($conn);

        echo json_encode(array("status" => "success", "employee_id" => $employee_id));
    } else {
        echo json_encode(array("status" => "error", "message" => "Error: " . mysqli_error($conn)));
    }
    exit();
}
