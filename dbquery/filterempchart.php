<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

include "../config.php";
$conn = open_connection();
if (isset($_GET['employee_id']) && !empty($_GET['employee_id'])) {
    $employee_id = $_GET['employee_id'];

    $sql = "SELECT tbljob.job, tblticket.task, tblsub_job.subjob, tblemployee.employee_name, tblemployee.employee_name, tblemployee.employee_nickname,
            tblticket.deadline_start, tblticket.deadline_end, tblticket.duration_start, tblticket.duration_end, tblticket_status.ticketstatus
            FROM tblticket
            LEFT JOIN tbljob ON tblticket.job_id = tbljob.job_id
            LEFT JOIN tblsub_job ON tblticket.subjob_id = tblsub_job.subjob_id
            LEFT JOIN tblemployee ON tblticket.employee_id = tblemployee.employee_id
            LEFT JOIN tblticket_status ON tblticket.ticketstatus_id = tblticket_status.ticketstatus_id
            WHERE tblticket.employee_id = '$employee_id'";

    $result = mysqli_query($conn, $sql);

    $filterdata = array();

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $filterdata[] = $row;
        }
    }

    echo json_encode($filterdata);
}
