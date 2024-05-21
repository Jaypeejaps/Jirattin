<?php
session_start();
$userlevel_id = $_SESSION['userlevel_id'] ?? null;

function load_granttchart()
{
    $conn = open_connection();
    $userlevel_id = $_SESSION['userlevel_id'];
    $employee_id = $_SESSION['employee_id'];

    $searchQuery = $_GET['searchQuery'] ?? '';

    $where = "";

    if ($searchQuery) {
        $where .= " AND tblemployee.employee_id = '$searchQuery'";
    }

    if ($userlevel_id == 1) {
        $where  .= "";
    } else {
        $where  .= " AND tblticket.employee_id = '$employee_id'";
    }


    $sql = "SELECT tbljob.job, tblticket.task, tblsub_job.subjob, tblemployee.employee_name, tblemployee.employee_name,tblemployee.employee_nickname,
        tblticket.deadline_start, tblticket.deadline_end, tblticket.duration_start, tblticket.duration_end, tblticket_status.ticketstatus
        FROM tblticket
        LEFT JOIN tbljob ON tblticket.job_id = tbljob.job_id
        LEFT JOIN tblsub_job ON tblticket.subjob_id = tblsub_job.subjob_id
        LEFT JOIN tblemployee ON tblticket.employee_id = tblemployee.employee_id
        LEFT JOIN tblticket_status ON tblticket.ticketstatus_id = tblticket_status.ticketstatus_id
        WHERE 1 $where";

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) <= 0) {
        $result = 0;
    }

    $conn->close();
    return $result;
}
