<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "../config.php";
include "../server_config/server_config.php";

$conn = open_connection();

date_default_timezone_set('Asia/Manila');

session_start();

if (!isset($_SESSION['employee_id'])) {
    echo json_encode(array("status" => 0, "message" => "User not logged in"));
    exit();
}

$logged_in_employee_id = $_SESSION['employee_id'];

$dept_query = "SELECT department_id FROM tblemployee WHERE employee_id = '$logged_in_employee_id'";
$dept_result = mysqli_query($conn, $dept_query);

if ($dept_result && mysqli_num_rows($dept_result) > 0) {
    $dept_row = mysqli_fetch_assoc($dept_result);
    $department_id = $dept_row['department_id'];
} else {
    echo json_encode(array("status" => 0, "message" => "Failed to fetch department ID"));
    exit();
}

$date_accepted = date("Y-m-d H:i:s", time());
$lastmodified = time();

if (isset($_POST['edit_ticket_id']) && !empty($_POST['edit_ticket_id'])) {
    $edit_ticket_id = $_POST['edit_ticket_id'];

    $selectQuery = "SELECT tblticket.*, tbljob.job, tblticket.task, tblsub_job.subjob, tblticket.note, tblassign.employee_nickname AS assign_nickname,
    tblticket.date_created, tblticket.date_accepted, tblticket.lastmodified, tblemployee.department_id,
    tblemployee.employee_name, tblticket.deadline_start, tblticket.deadline_end, tblticket.duration_start, tblticket.duration_end,
    tblticket_status.ticketstatus
    FROM tblticket
    LEFT JOIN tbljob ON tblticket.job_id = tbljob.job_id
    LEFT JOIN tblsub_job ON tblticket.subjob_id = tblsub_job.subjob_id
    LEFT JOIN tblemployee AS tblassign ON tblticket.assign_id = tblassign.employee_id
    LEFT JOIN tblemployee ON tblticket.employee_id = tblemployee.employee_id
    LEFT JOIN tblticket_status ON tblticket.ticketstatus_id = tblticket_status.ticketstatus_id
    WHERE ticket_id = $edit_ticket_id";
    $result = mysqli_query($conn, $selectQuery);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        $data = [
            'status' => 1,
            'job_id' => $row['job_id'],
            'customjob' => $row['job'],
            'task' => $row['task'],
            'subjob_id' => $row['subjob_id'],
            'customsubjob' => $row['subjob'],
            'note' => $row['note'],
            'assign_id' => $row['assign_id'],
            'employee_id' => $row['employee_id'],
            'date_created' => $row['date_created'],
            'lastmodified' => $row['lastmodified'],
            'date_accepted' => $row['date_accepted'],
            'deadline_start' => $row['deadline_start'],
            'deadline_end' => $row['deadline_end'],
            'duration_start' => $row['duration_start'],
            'duration_end' => $row['duration_end'],
            'ticketstatus_id' => $row['ticketstatus_id'],
        ];
    } else {
        $data = ['status' => 0];
    }
    echo json_encode($data);
    exit();
} else {
    $ticket_id = (isset($_POST["ticket_id"]) && $_POST["ticket_id"] ? $_POST["ticket_id"] : 0);

    if ($ticket_id) {
        $job_id = (isset($_POST["jobdesc"]) && $_POST["jobdesc"]) ? intval($_POST["jobdesc"]) : 0;
        $task = (isset($_POST["taskdesc"]) && $_POST["taskdesc"]) ? mysqli_real_escape_string($conn, $_POST["taskdesc"]) : '';
        $subjob_id = (isset($_POST["subjobdesc"]) && $_POST["subjobdesc"]) ? intval($_POST["subjobdesc"]) : 0;
        $note = (isset($_POST["notedesc"]) && $_POST["notedesc"]) ? mysqli_real_escape_string($conn, $_POST["notedesc"]) : '';
        $assign_id = (isset($_POST["assignname"]) && $_POST["assignname"]) ? intval($_POST["assignname"]) : 0;
        $deadline_start = (isset($_POST["deadlinestart"]) && $_POST["deadlinestart"]) ? $_POST["deadlinestart"] : '';
        $deadline_end = (isset($_POST["deadlineend"]) && $_POST["deadlineend"]) ? $_POST["deadlineend"] : '';
        $ticketstatus_id = (isset($_POST["ticketstatus"]) && $_POST["ticketstatus"]) ? intval($_POST["ticketstatus"]) : 0;

        $previous_date_accepted = isset($_SESSION['previous_date_accepted']) ? $_SESSION['previous_date_accepted'] : null;


        $duration_start = "0000-00-00 00:00:00";
        $duration_end = "0000-00-00 00:00:00";

        if ($ticketstatus_id == 2) {
            $date_accepted = date("Y-m-d H:i:s", time());
            $lastmodified = time();
        } elseif ($ticketstatus_id == 3) {
            if ($previous_date_accepted !== null) {
                $date_accepted = $previous_date_accepted;
            }
            $duration_start = date("Y-m-d H:i:s", time());
            $lastmodified = time();
        } elseif ($ticketstatus_id == 4) {
            if ($previous_date_accepted !== null) {
                $date_accepted = $previous_date_accepted;
            }
            $duration_end = date("Y-m-d H:i:s", time());
            $lastmodified = time();
        } elseif ($ticketstatus_id == 5) {
            if ($previous_date_accepted !== null) {
                $date_accepted = $previous_date_accepted;
            }
            $lastmodified = time();
        } elseif ($ticketstatus_id == 6) {
            $duration_start = "0000-00-00 00:00:00";
            $duration_end = "0000-00-00 00:00:00";
            $lastmodified = time();
        } elseif ($ticketstatus_id == 7) {
            $duration_start = "0000-00-00 00:00:00";
            $duration_end = "0000-00-00 00:00:00";
            $lastmodified = time();
        }

        $_SESSION['previous_date_accepted'] = $date_accepted;

        if ($job_id === 0) {
            $custom_job = mysqli_real_escape_string($conn, $_POST['customjob']);
            $check_job_query = "SELECT job_id FROM tbljob WHERE job = '$custom_job'";
            $check_job_result = mysqli_query($conn, $check_job_query);

            if (mysqli_num_rows($check_job_result) > 0) {
                $job_row = mysqli_fetch_assoc($check_job_result);
                $job_id = $job_row['job_id'];
            } else {
                $insert_job_query = "INSERT INTO tbljob (job, department_id) VALUES ('$custom_job', '$department_id')";
                $insert_job_result = mysqli_query($conn, $insert_job_query);

                if (!$insert_job_result) {
                    echo json_encode(array("status" => "error", "message" => "Error inserting custom job: " . mysqli_error($conn)));
                    exit();
                }

                $job_id = mysqli_insert_id($conn);
            }
        }

        if ($subjob_id === 0) {

            if (isset($_POST['customsubjob']) && $_POST['customsubjob']) {

                $custom_subjob = mysqli_real_escape_string($conn, $_POST['customsubjob']);

                $check_subjob_query = "SELECT subjob_id FROM tblsub_job WHERE subjob = '$custom_subjob'";
                $check_subjob_result = mysqli_query($conn, $check_subjob_query);

                if (mysqli_num_rows($check_subjob_result) > 0) {
                    $subjob_row = mysqli_fetch_assoc($check_subjob_result);
                    $subjob_id = $subjob_row['subjob_id'];
                } else {
                    $insert_subjob_query = "INSERT INTO tblsub_job (subjob) VALUES ('$custom_subjob')";
                    $insert_subjob_result = mysqli_query($conn, $insert_subjob_query);

                    if (!$insert_subjob_result) {
                        echo json_encode(array("status" => "error", "message" => "Error inserting custom subjob: " . mysqli_error($conn)));
                        exit();
                    }

                    $subjob_id = mysqli_insert_id($conn);
                }
            } else {
                $subjob_id = NULL;
            }
        }


        if ($ticketstatus_id == 4) {
            $updateQuery = "UPDATE tblticket SET job_id = '$job_id', task = '$task', subjob_id = '$subjob_id', note = '$note', employee_id = '$assign_id',
            lastmodified = '$lastmodified', deadline_start = '$deadline_start', deadline_end = '$deadline_end', duration_end = '$duration_end',
            ticketstatus_id = '$ticketstatus_id' 
            WHERE ticket_id = '$ticket_id'";
        } else {
            $updateQuery = "UPDATE tblticket SET job_id = '$job_id', task = '$task', subjob_id = '$subjob_id', note = '$note', employee_id = '$assign_id',
            lastmodified = '$lastmodified', date_accepted = '$date_accepted', deadline_start = '$deadline_start', deadline_end = '$deadline_end', duration_start = '$duration_start', duration_end = '$duration_end', 
            ticketstatus_id = '$ticketstatus_id' 
            WHERE ticket_id = '$ticket_id'";
        }

        $updateResult = mysqli_query($conn, $updateQuery);

        if ($updateResult) {
            $msg = "Ticket updated successfully!";
            $status = 1;
        } else {
            $msg = "Error updating ticket: " . mysqli_error($conn);
            $status = 0;
        }
        echo json_encode(array("status" => $status, "message" => $msg));
        exit();
    }
}

// NAG AADD NG TICKET
if (
    isset($_POST['jobdesc']) && isset($_POST['taskdesc']) && isset($_POST['subjobdesc']) && isset($_POST['assignname']) && isset($_POST['notedesc'])
    && isset($_POST['deadlinestart']) && isset($_POST['deadlineend']) && isset($_POST['durationstart']) && isset($_POST['durationend'])
    && isset($_POST['ticketstatus'])
) {
    $job_id = ($_POST['jobdesc']);
    $task = mysqli_real_escape_string($conn, $_POST['taskdesc']);
    $subjob_id = ($_POST['subjobdesc']);
    $note = ($_POST['notedesc']);
    $employee_id = intval($_POST['assignname']);
    $deadline_start = $_POST['deadlinestart'];
    $deadline_end = $_POST['deadlineend'];
    $duration_start = $_POST['durationstart'];
    $duration_end = $_POST['durationend'];

    $ticketstatus_id = 1;

    $ticket_id = 0;
    $status = 0;
    $msg = "error";
    $has_error = 0;

    // EMAIL
    $email_query = "SELECT employee_email FROM tblemployee WHERE employee_id = '$employee_id'";
    $email_result = mysqli_query($conn, $email_query);
    if (!$email_result) {
        echo json_encode(array("status" => "error", "message" => "Error fetching assigned employee's email: " . mysqli_error($conn)));
        exit();
    }
    $row = mysqli_fetch_assoc($email_result);
    $assigned_employee_email = $row['employee_email'];

    if ($job_id === '0') {
        $value = $_POST['customjob'];
        $sql = "INSERT INTO tbljob (job, department_id) VALUES ('$value', '$department_id')";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            $job_id = mysqli_insert_id($conn);
        } else {
            $msg = "Error inserting custom job: " . mysqli_error($conn);
            $has_error = 1;
        }
    }

    if ($subjob_id === '0') {
        $value = $_POST['customsubjob'];
        $sql = "INSERT INTO tblsub_job (subjob) VALUES ('$value')";
        $result = mysqli_query($conn, $sql);
        if (!$result) {
            $msg = "Error inserting custom job: " . mysqli_error($conn);
            $has_error = 1;
        } else {
            $subjob_id = mysqli_insert_id($conn);
        }
    }

    if (!$has_error) {
        $sql = "INSERT INTO tblticket(job_id, task, subjob_id, note, assign_id, employee_id, date_created, deadline_start, deadline_end, duration_start, duration_end, ticketstatus_id)
                VALUES ('$job_id', '$task', '$subjob_id', '$note', '$logged_in_employee_id', '$employee_id', NOW(), '$deadline_start', '$deadline_end', '$duration_start', '$duration_end', '$ticketstatus_id')";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            $msg = "Ticket Assigned Successfully!";
            $status = 1;
            $task = $task;
            $ticket_id = mysqli_insert_id($conn);

            $to = $assigned_employee_email;
            $subject = "#" . $ticket_id . ": " . $task;

            // Email message
            $message = "Dear Employee, A new ticket (ID: " . $ticket_id . ") has been assigned to you. \n\n";
            $ticketListURL = "https://jirattin.carinsurancesolution.ph/ticketlist.php?q=" . $ticket_id;
            $message .= "You can view the ticket details <a href='" . $ticketListURL . "'>here</a>.";

            if (!isset($_SESSION["employee_id"])) {
                $message .= "<br>Please log in to view your assigned tickets. Click here to Log in <a href='https://jirattin.carinsurancesolution.ph/index.php'>Click me</a>";
            }

            if (ENVIRONMENT == "production") {
                $base_path = $_SERVER["DOCUMENT_ROOT"];
                $path = $base_path . "/libraries/email.php";

                include($path);
                $obj_email = new Email();

                $parameter = array();
                $parameter["recipients"] = array($to);
                $parameter["sender"] = "inquiry@antz-insurance.com";
                $parameter["sender_name"] = "BOSS YARLIE";
                $parameter["subject"] = $subject;
                $parameter["content"] = $message;
                $parameter["attachments"] = array();

                $response = $obj_email->send($parameter);
            }

            if (isset($response) && $response->msg) {
                $status = 1;
            } else {
                $status = 0;
                $msg = "Email sending failed";
            }
        } else {
            $msg = "Error saving ticket " . mysqli_error($conn);
        }
    }

    echo json_encode(array("status" => $status, "message" => $msg, "ticket_id" => $ticket_id));

    exit();
}
