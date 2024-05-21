<?php
include "../config.php";
$conn = open_connection();

if (isset($_POST['delete_ticket_id'])) {
    $delete_ticket_id = $_POST['delete_ticket_id'];

    $deleteQuery = "DELETE FROM tblticket WHERE ticket_id = '$delete_ticket_id'";
    $deleteResult = mysqli_query($conn, $deleteQuery);

    if ($deleteResult) {
        echo json_encode(["status" => "success", "message" => "Ticket deleted successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . mysqli_error($conn)]);
    }
    exit;
}
