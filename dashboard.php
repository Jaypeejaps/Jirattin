<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION["is_login"])) {

    header('Location: ' . 'index.php');

    die();
}

function total_employee()
{
    $conn = open_connection();

    $sql = "SELECT COUNT(*) AS total FROM tblemployee";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $totalEmployees = $row["total"];
    } else {
        $totalEmployees = 0;
    }
    $conn->close();

    return $totalEmployees;
}

function total_tickets()
{

    $userlevel_id = $_SESSION['userlevel_id'];
    $employee_id = $_SESSION['employee_id'];

    $where  = "";
    if ($userlevel_id == 1) {
        $where  = "";
    } else {
        $where  = " AND tblticket.employee_id = '$employee_id'";
    }

    $conn = open_connection();

    $sql = "SELECT COUNT(*) AS total FROM tblticket WHERE 1 $where";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $totalTickets = $row["total"];
    } else {
        $totalTickets = 0;
    }

    return $totalTickets;
}

function load_recent_ticket()
{

    $conn = open_connection();

    $userlevel_id = $_SESSION['userlevel_id'];
    $employee_id = $_SESSION['employee_id'];

    $where  = "";
    if ($userlevel_id == 1) {
        $where  = "";
    } else {
        $where  = " AND tblticket.employee_id = '$employee_id'";
    }

    $recentQuery = "SELECT tblticket.ticket_id, tbljob.job, tblticket.task, tblsub_job.subjob, tblassign.employee_nickname AS assign_nickname,
    tblemployee.employee_name, tblticket_status.ticketstatus FROM tblticket
    LEFT JOIN tbljob ON tblticket.job_id = tbljob.job_id
    LEFT JOIN tblsub_job ON tblticket.subjob_id = tblsub_job.subjob_id
    LEFT JOIN tblemployee AS tblassign ON tblticket.assign_id = tblassign.employee_id
    LEFT JOIN tblemployee ON tblticket.employee_id = tblemployee.employee_id
    LEFT JOIN tblticket_status ON tblticket.ticketstatus_id = tblticket_status.ticketstatus_id
    WHERE 1 $where
    ORDER BY tblticket.ticket_id DESC LIMIT 6";

    $result = mysqli_query($conn, $recentQuery);

    if (mysqli_num_rows($result) <= 0) {
        $result = 0;
    }

    $conn->close();

    return $result;
}

function load_employee_list()
{
    $conn = open_connection();

    $userlevel_id = $_SESSION['userlevel_id'];
    $employee_id = $_SESSION['employee_id'];

    $where  = "";
    if ($userlevel_id == 1) {
        $where  = "";
    } else {
        $where  = " AND tblemployee.employee_id = '$employee_id'";
    }

    $showEmployee = "SELECT tblemployee.employee_name, tblemployee.employee_nickname, tblemployee.employee_number, 
    tbldepartment.department, tbluser_level.user_level FROM tblemployee
    LEFT JOIN tbldepartment ON tblemployee.department_id = tbldepartment.department_id
    LEFT JOIN tbluser_level ON tblemployee.userlevel_id = tbluser_level.userlevel_id
    WHERE 1 $where
    ORDER BY tblemployee.employee_id ASC";

    $result = mysqli_query($conn, $showEmployee);

    if (mysqli_num_rows($result) <= 0) {
        $result = 0;
    }

    $conn->close();

    return $result;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="css/favicon.ico">
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.2.1/css/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <title>Dashboard | Jirattin</title>
</head>

<body>

    <?php include "sidebar.php"; ?>

    <div class="main-content">
        <?php include "header.php"; ?>

        <main>
            <div class="cards">
                <div class="card-single">
                    <div>
                        <?php echo total_employee(); ?>
                        <h1 id="employeeModalContent"></h1>
                        <span>Registered Employees</span>
                    </div>
                    <div>
                        <span class="bi bi-people"></span>
                    </div>
                </div>

                <div class="card-single">
                    <div>
                        <?php echo total_tickets(); ?>
                        <div class="fontSizecount">
                            <h1 id="ticketCount"></h1>
                        </div>
                        <span>Tickets</span>
                    </div>
                    <div>
                        <span class="bi bi-list-task"></span>
                    </div>
                </div>

                <div class="card-single">
                    <div>
                        <?php
                        $userlevel_id = $_SESSION['userlevel_id'];
                        $employee_id = $_SESSION['employee_id'];

                        $where  = "";
                        if ($userlevel_id == 1) {
                            $where  = "";
                        } else {
                            $where  = " AND tblticket.employee_id = '$employee_id'";
                        }



                        $sql = "SELECT COUNT(*) AS total FROM tblticket
                        WHERE 1 $where AND ticketstatus_id = 4";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            $row = $result->fetch_assoc();
                            $totalTicketsdone = $row["total"];
                        } else {
                            $totalTicketsdone = 0;
                        }



                        echo $totalTicketsdone;
                        ?>
                        <h1 id="ticketCountdone"></h1>
                        <span>Completed
                            <!-- <div class="completetckt">
                                <a href="accomplishments.php">View completed task</a>
                            </div> -->
                        </span>
                    </div>
                    <div>
                        <span class="bi bi-check2-circle"></span>
                    </div>
                </div>
            </div>

            <div class="recent-grid">
                <div class="ticketsss">
                    <div class="card">
                        <div class="card-header">
                            <h2>Recent Tickets</h2>

                            <a href="ticketlist.php"><button>See All <span class="bi bi-arrow-right"></span></button></a>
                        </div>

                        <div class="card-body">
                            <table>
                                <thead>
                                    <tr>
                                        <td>Ticket ID</td>
                                        <td>Job</td>
                                        <td>Task</td>
                                        <td>SubJob</td>
                                        <td>Created By</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $result = load_recent_ticket();
                                    if ($result) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>
                                                    <td>" . $row['ticket_id'] . "</td>
                                                    <td>" . $row['job'] . "</td>
                                                    <td>" . $row['task'] . "</td>
                                                    <td>" . $row['subjob'] . "</td>
                                                    <td>" . $row['assign_nickname'] . "</td>
                                                </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='12'>No tickets found.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="employee-list">
                <div class="employeesss">
                    <div class="card-body">
                        <div class="table-responsive">

                            <table class="table table-bordered table-striped" id="employeelist">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Number</th>
                                        <th>Department</th>
                                        <th>Userlevel</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $result = load_employee_list();

                                    if ($result) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>
                                                <td>" . $row['employee_name'] . "</td>
                                                <td>" . $row['employee_number'] . "</td>
                                                <td>" . $row['department'] . "</td>
                                                <td>" . $row['user_level'] . "</td>
                                            </tr>";
                                        }
                                    } else {
                                        echo "<tr><colspan='5'>No Employee found.</tr>>";
                                    }
                                    ?>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </main>

        <!-- END -->

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

        <script src="js/script.js"></script>

        <script>
            jQuery(document).ready(function($) {
                var table = $('#employeelist');
                if (table.find('tbody tr').length > 0) {
                    new DataTable(table, {
                        responsive: true
                    });
                } else {
                    table.parent().html('<p>No records found.</p>');
                }
            });
        </script>
</body>

</html>