<?php
include "../config.php";
$conn = open_connection();

$query = "SELECT subjob_id, subjob FROM tblsub_job ORDER BY subjob ASC";
$result = mysqli_query($conn, $query);

$subJobOptions = array();
while ($row = mysqli_fetch_assoc($result)) {
    $subJobOptions[$row['subjob_id']] = $row['subjob'];
}

echo json_encode($subJobOptions);
