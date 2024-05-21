<?php
session_start();

include "./config.php";

if (isset($_GET['employee_id']) && !empty($_GET['employee_id'])) {
    $employee_id = $_GET['employee_id'];

    $sql = "SELECT * FROM tlblticket WHERE employee_id = '$employee_id'";
    $result = mysqli_query($conn, $sql);

    $filterdata = array();

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $filterdata[] = $row;
        }
    }

    echo json_encode($filterdata);
}
