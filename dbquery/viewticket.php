<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
$userlevel_id = $_SESSION['userlevel_id'];
$employee_id = $_SESSION['employee_id'];

$statusID = $_GET['statusID'] ?? null;
$searchQuery = $_GET['searchQuery'] ?? '';

$where  = "";

// Query for search
if ($searchQuery) {
    $where .= " AND (tblticket.ticket_id LIKE '%$searchQuery%' OR tbljob.job LIKE '%$searchQuery%' OR tblticket.task LIKE '%$searchQuery%' OR tblsub_job.subjob LIKE '%$searchQuery%' OR tblassign.employee_nickname LIKE '%$searchQuery%' OR tblemployee.employee_name LIKE '%$searchQuery%' OR tblticket_status.ticketstatus LIKE '%$searchQuery%')";
}


if ($userlevel_id == 1) {
    $where  .= "";
} else {
    $where  .= " AND tblticket.employee_id = '$employee_id'";
}



include "../config.php";
$conn = open_connection();

$Perpage = 10;
$Pagenumber = 8;
$Activepage = $_GET['Activepage'] ?? 1;

$offset = ($Activepage - 1) * $Perpage;

$selectQuery = "SELECT tblticket.ticket_id, tbljob.job, tblticket.task, tblsub_job.subjob, tblticket.note,
    tblemployee.department_id, tblassign.employee_nickname AS assign_nickname,
    tblemployee.employee_name, DATE_FORMAT(tblticket.date_created, '%M %e %Y %h:%i %p') AS formatted_date_created,
    DATE_FORMAT(tblticket.date_accepted, '%M %e %Y %h:%i %p') AS formatted_date_accepted,
    DATE_FORMAT(tblticket.deadline_start, '%M %e %Y %h:%i %p') AS formatted_deadline_start, 
    DATE_FORMAT(tblticket.deadline_end, '%M %e %Y %h:%i %p') AS formatted_deadline_end,
    DATE_FORMAT(tblticket.duration_start, '%M %e %Y %h:%i %p') AS formatted_duration_start, 
    DATE_FORMAT(tblticket.duration_end, '%M %e %Y %h:%i %p') AS formatted_duration_end, 
    tblticket_status.ticketstatus,
    tblticket.lastmodified,
    CASE 
        WHEN tblticket_status.ticketstatus = 'Open' AND NOW() > DATE_ADD(tblticket.deadline_start, INTERVAL 1 HOUR) THEN CONCAT('Since: ', TIMESTAMPDIFF(HOUR, tblticket.deadline_start, NOW()), 'hr ', MOD(TIMESTAMPDIFF(MINUTE, tblticket.deadline_start, NOW()), 60), 'min')
        ELSE NULL
    END AS deadline_warning
    FROM tblticket
    LEFT JOIN tbljob ON tblticket.job_id = tbljob.job_id
    LEFT JOIN tblsub_job ON tblticket.subjob_id = tblsub_job.subjob_id
    LEFT JOIN tblemployee AS tblassign ON tblticket.assign_id = tblassign.employee_id
    LEFT JOIN tblemployee ON tblticket.employee_id = tblemployee.employee_id
    LEFT JOIN tblticket_status ON tblticket.ticketstatus_id = tblticket_status.ticketstatus_id
    WHERE 1 $where";

// Query for status
if ($statusID != 0 && $statusID != null) {
    $selectQuery .= " AND tblticket_status.ticketstatus_id = $statusID";
}

// Query for pagination
$selectQuery .= " ORDER BY tblticket.lastmodified DESC, tblticket.ticket_id DESC LIMIT $offset, $Perpage";

// Execute the query and process results
$result = mysqli_query($conn, $selectQuery);

$data = array();
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
}

$TotalrowsQuery = "SELECT COUNT(*) as total_tickets FROM tblticket
    LEFT JOIN tbljob ON tblticket.job_id = tbljob.job_id
    LEFT JOIN tblsub_job ON tblticket.subjob_id = tblsub_job.subjob_id
    LEFT JOIN tblemployee AS tblassign ON tblticket.assign_id = tblassign.employee_id
    LEFT JOIN tblemployee ON tblticket.employee_id = tblemployee.employee_id
    LEFT JOIN tblticket_status ON tblticket.ticketstatus_id = tblticket_status.ticketstatus_id
    WHERE 1 $where";

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
