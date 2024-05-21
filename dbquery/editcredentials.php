<?php
session_start();

if (isset($_POST['btnUpdateEmp'])) {
    include "../config.php";
    $conn = open_connection();

    $employee_id = $_POST['employee_id'];
    $employee_name = $_POST['empname'];
    $employee_nickname = $_POST['empnickname'];
    $employee_email = $_POST['empemail'];
    $employee_number = $_POST['empnumber'];
    $department_id = $_POST['empdepartment'];
    $employee_password = $_POST['emppassword'];

    $query = "UPDATE tblemployee SET employee_name = '$employee_name', employee_nickname = '$employee_nickname', employee_email = '$employee_email', 
    employee_number = '$employee_number', department_id = '$department_id', employee_password = '$employee_password'
              WHERE employee_id = '$employee_id'";

    $result = mysqli_query($conn, $query);

    if ($result) {
        echo '<script>alert("Employee data updated successfully!");</script>';
        echo '<script>window.location.href = "../edituser.php";</script>';
        exit();
    } else {
        die("Error updating employee data: " . mysqli_error($conn));
    }
} else {
    die("Invalid request");
}
