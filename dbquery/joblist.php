<?php
include "../config.php";
$conn = open_connection();

if (isset($_GET['department_id'])) {
    $department_id = $_GET['department_id'];

    $query = "SELECT job_id, job FROM tbljob WHERE department_id = ? ORDER BY job ASC";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $department_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $status = 1;
    $data = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = array(
            "job_id" => $row['job_id'],
            "job" => $row['job']
        );
    }

    echo json_encode(array("response" => $data, "status" => $status));
} else {
    echo json_encode(array("response" => array(), "status" => 0));
}
