<?php
session_start();
$userlevel_id = $_SESSION['userlevel_id'];
$employee_id = $_SESSION['employee_id'] ?? '';

$searchQuery = $_GET['searchQuery'] ?? '';
$selectedDate = $_GET['selectedDate'] ?? '';

$startdate = $_GET['startdate'] ?? '';
$enddate = $_GET['enddate'] ?? '';

$where  = "";

if ($searchQuery) {
    $where .= " AND tblemployee.employee_id = '$searchQuery'";
}

if ($userlevel_id == 1) {
    $where  .= "";
} else {
    $where  .= " AND tblticket.employee_id = '$employee_id'";
}

if (!empty($startdate) && !empty($enddate)) {
    $where .= " AND tblticket.date_created BETWEEN '$startdate' AND '$enddate'";
}

// Per Day
if (!empty($selectedDate)) {
    $where .= " AND DATE(tblticket.date_created) = '$selectedDate'";
}

include "../config.php";
$conn = open_connection();

$Perpage = 10;
$Pagenumber = 8;
$Activepage = $_GET['Activepage'] ?? 1;

$offset = ($Activepage - 1) * $Perpage;

$selectQuery = "SELECT tblticket.ticket_id, tbljob.job, tblticket.task, tblsub_job.subjob, tblassign.employee_nickname AS assign_nickname,
        tblemployee.employee_name, DATE_FORMAT(tblticket.date_created, '%M %e %Y %h:%i %p') AS formatted_date_created,
        DATE_FORMAT(tblticket.deadline_start, '%M %e %Y %h:%i %p') AS formatted_deadline_start, 
        DATE_FORMAT(tblticket.deadline_end, '%M %e %Y %h:%i %p') AS formatted_deadline_end,
        DATE_FORMAT(tblticket.duration_start, '%M %e %Y %h:%i %p') AS formatted_duration_start, 
        DATE_FORMAT(tblticket.duration_end, '%M %e %Y %h:%i %p') AS formatted_duration_end, 
        tblticket_status.ticketstatus
        FROM tblticket
        LEFT JOIN tbljob ON tblticket.job_id = tbljob.job_id
        LEFT JOIN tblsub_job ON tblticket.subjob_id = tblsub_job.subjob_id
        LEFT JOIN tblemployee AS tblassign ON tblticket.assign_id = tblassign.employee_id
        LEFT JOIN tblemployee ON tblticket.employee_id = tblemployee.employee_id
        LEFT JOIN tblticket_status ON tblticket.ticketstatus_id = tblticket_status.ticketstatus_id
        WHERE 1 $where AND tblticket_status.ticketstatus_id = 4";

// Query for pagination
$selectQuery .= " ORDER BY tblticket.ticket_id DESC LIMIT $offset, $Perpage";


$result = mysqli_query($conn, $selectQuery);

$data = array();
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['subjob'] === '') {
            unset($row['subjob']);
        }
        $data[] = $row;
    }
}

$TotalrowsQuery = "SELECT COUNT(*) as total_tickets FROM tblticket
    LEFT JOIN tbljob ON tblticket.job_id = tbljob.job_id
    LEFT JOIN tblsub_job ON tblticket.subjob_id = tblsub_job.subjob_id
    LEFT JOIN tblemployee AS tblassign ON tblticket.assign_id = tblassign.employee_id
    LEFT JOIN tblemployee ON tblticket.employee_id = tblemployee.employee_id
    LEFT JOIN tblticket_status ON tblticket.ticketstatus_id = tblticket_status.ticketstatus_id
    WHERE 1 $where AND tblticket_status.ticketstatus_id = 4";

$TotalrowsResult = mysqli_query($conn, $TotalrowsQuery);
$TotalrowsRow = mysqli_fetch_assoc($TotalrowsResult);
$Totalrows = $TotalrowsRow['total_tickets'];

$Totalpages = ceil($Totalrows / $Perpage);
$Activepage = max(1, min($Totalpages, $Activepage));
$Start = max(1, $Activepage - floor($Pagenumber / 2));
$End = min($Totalpages, $Activepage + floor($Pagenumber / 2));

include "../libraries/pagination.php";

$response = array(
    'data' => $data,
    'pagination' => paulpagination($Activepage, $Start, $End, $Totalpages)
);

echo json_encode($response);
