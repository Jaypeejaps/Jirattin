<?php
date_default_timezone_set('Asia/Manila');
session_start();
$currentTime = time();

$deadlineEndTime = $currentTime + (2 * 60 * 60);

$defaultDeadlineEndTime = date("Y-m-d\TH:i:s", $deadlineEndTime);

if (!isset($_SESSION["is_login"])) {

    header('Location: ' . 'index.php');

    die();
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
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">



    <title>My Assigned | Jirattin</title>
</head>

<body>

    <?php include "sidebar.php"; ?>

    <div class="main-contentticket">
        <?php include "header.php"; ?>

        <main>

            <!-- START YOUR CODE HERE -->

            <div class="statusIDs" id="statusIDs">
                <?php

                $conn = open_connection();
                $userlevel_id = $_SESSION['userlevel_id'];
                $employee_id = $_SESSION['employee_id'];

                $where  = "";
                if ($userlevel_id == 1) {
                    $where  = "";
                } else {
                    $where  = " AND tblticket.assign_id = '$employee_id'";
                }

                //Open 
                $sql = "SELECT ticketstatus_id FROM `tblticket` WHERE 1 $where";
                $result = $conn->query($sql);
                $total_open = 0;

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        if ($row['ticketstatus_id'] == 1) {
                            $total_open++;
                        }
                    }
                }

                //To Do
                $sql = "SELECT ticketstatus_id FROM `tblticket` WHERE 1 $where";
                $result = $conn->query($sql);
                $total_todo = 0;

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        if ($row['ticketstatus_id'] == 2) {
                            $total_todo++;
                        }
                    }
                }

                // In Progress
                $sql = "SELECT ticketstatus_id FROM `tblticket` WHERE 1 $where";
                $result = $conn->query($sql);
                $total_inprog = 0;

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        if ($row['ticketstatus_id'] == 3) {
                            $total_inprog++;
                        }
                    }
                }

                // Done
                $sql = "SELECT ticketstatus_id FROM `tblticket` WHERE 1 $where";
                $result = $conn->query($sql);
                $total_done = 0;

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        if ($row['ticketstatus_id'] == 4) {
                            $total_done++;
                        }
                    }
                }

                // Waiting
                $sql = "SELECT ticketstatus_id FROM `tblticket` WHERE 1 $where";
                $result = $conn->query($sql);
                $total_waiting = 0;

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        if ($row['ticketstatus_id'] == 5) {
                            $total_waiting++;
                        }
                    }
                }

                // Abandoned
                $sql = "SELECT ticketstatus_id FROM `tblticket` WHERE 1 $where";
                $result = $conn->query($sql);
                $total_abandoned = 0;

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        if ($row['ticketstatus_id'] == 6) {
                            $total_abandoned++;
                        }
                    }
                }

                // Declined
                $sql = "SELECT ticketstatus_id FROM `tblticket` WHERE 1 $where";
                $result = $conn->query($sql);
                $total_declined = 0;

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        if ($row['ticketstatus_id'] == 7) {
                            $total_declined++;
                        }
                    }
                }

                // Total All
                $sql = "SELECT COUNT(*) as total_all FROM `tblticket` WHERE 1 $where";
                $result = $conn->query($sql);
                $total_all = ($result->num_rows > 0) ? $result->fetch_assoc()['total_all'] : 0;

                // My Assign
                $sql = "SELECT COUNT(*) as total_assigned FROM `tblticket` WHERE tblticket.assign_id = '$employee_id'";
                $result = $conn->query($sql);
                $total_assigned = ($result->num_rows > 0) ? $result->fetch_assoc()['total_assigned'] : 0;
                ?>

                <div class="buttonsss" id="buttonsss">
                    <ul>
                        <?php if ($userlevel_id == 1) { ?>
                            <li class="btnstatuss btn-warning" id="myassigntickets" data-status="8" style="position:relative" style="border: 1px solid black">
                                <span class="">My Assigned</span>
                                <span class="icons"> <?php echo $total_assigned; ?></span>
                            </li>
                        <?php } ?>
                        <li class="btnstatuss btn-warning" id="allcount" data-status="0" style="position:relative" style="border: 1px solid black" ; onclick>
                            <span class="">All</span>
                            <span class="icons"> <?php echo $total_all; ?></span>
                        </li>
                        <li class="btnstatuss btn-warning active" id="opencount" data-status="1" style="position:relative;">
                            <span class="">Open</span>
                            <span class="icons"><?php echo $total_open; ?></span>
                        </li>
                        <li class="btnstatuss btn-warning" id="todocount" data-status="2" style="position:relative;">
                            <span>To Do</span>
                            <span class="icons"><?php echo $total_todo; ?></span>
                        </li>
                        <li class="btnstatuss btn-warning" id="inprogcount" data-status="3" style="position:relative;">
                            <span>In Progress</span>
                            <span class="icons"><?php echo $total_inprog; ?></span>
                        </li>
                        <li class="btnstatuss btn-warning" id="donecount" data-status="4" style="position:relative;">
                            <span>Done</span>
                            <span class="icons"><?php echo $total_done; ?></span>
                        </li>
                        <li class="btnstatuss btn-warning" id="waitingcount" data-status="5" style="position:relative;">
                            <span>Waiting</span>
                            <span class="icons"><?php echo $total_waiting; ?></span>
                        </li>
                        <li class="btnstatuss btn-warning" id="abandonedcount" data-status="6" style="position:relative;">
                            <span>Abandoned</span>
                            <span class="icons"><?php echo $total_abandoned; ?></span>
                        </li>
                        <li class="btnstatuss btn-warning" id="declinecount" data-status="7" style="position:relative;">
                            <span>Declined</span>
                            <span class="icons"><?php echo $total_declined; ?></span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="recent-grid">
                <div class="ticketsss">
                    <div class="card-body">

                        <div class="search-bar">
                            <input type="text" class="form-control" id="searchInputs" placeholder="Type to search...">
                            <button class="btn-clear-searchh" onclick="clearSearchInputs()"><i class="bi bi-x"></i></button>
                            <button class="btnsearchh btn-warning"><i class="bi bi-search"></i></button>
                        </div><br>

                        <div class="outer-wrapper">
                            <div class="table-wrap">
                                <table class="table table-bordered table-striped" id="ticketings">
                                    <thead>
                                        <tr>
                                            <th>Ticket ID</th>
                                            <th>Ticket Details</th>
                                            <th>Employee</th>
                                            <th>Deadline</th>
                                            <th>Duration</th>
                                            <th>Status</th>
                                            <th>Option</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ticketdataa">
                                    </tbody>
                                </table>
                                <div id="paginations" class="paginations">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form class="formticket" id="insertForm" method="post">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel"></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <input type="hidden" id="ticket_id" name="ticket_id" value="">
                                </div>
                                <div class="mb-3">
                                    <input type="hidden" id="department_id" name="department_id" value="">
                                </div>
                                <div class="mb-3">
                                    <label for="">Assigned to:</label>
                                    <select class="form-control select2" name="assignname" id="assignname" required>
                                        <option value="">Select Employee</option>
                                        <?php
                                        $select_query = mysqli_query($conn, "SELECT employee_name, employee_id, department_id FROM tblemployee ORDER BY employee_name ASC");
                                        while ($res = mysqli_fetch_array($select_query)) { ?>
                                            <option department_id="<?php echo $res['department_id'] ?>" value="<?php echo $res['employee_id'] ?>">
                                                <?php echo $res['employee_name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="">Job:</label>
                                    <select class="form-control select2" name="jobdesc" id="jobdesc">
                                        <option value="">Select Job</option>
                                    </select>
                                    <div id="customJobField"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="">Task:</label>
                                    <input type="text" class="form-control" placeholder="Task Description" id="taskdesc" name="taskdesc">
                                </div>
                                <div class="mb-3">
                                    <label for="">Sub Job:</label>
                                    <select class="form-control select2" name="subjobdesc" id="subjobdesc">
                                        <option value="">Select Sub Job</option>

                                    </select>
                                    <div id="customSubJobField"></div>
                                </div>
                                <div class="mb-3">
                                    <input type="hidden" id="date_created" name="date_created" value="">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="">Deadline Start:</label>
                                        <input type="datetime-local" class="form-control" name="deadlinestart" id="deadlinestart" value="<?php echo date("Y-m-d H:i:s", time()); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="">Deadline End:</label>
                                        <input type="datetime-local" class="form-control" name="deadlineend" id="deadlineend" value="<?php echo $defaultDeadlineEndTime; ?>">
                                    </div>

                                </div>
                                <div class="hiddeninputs" style="display:none;">
                                    <div class="row" hidden>
                                        <div class="col-md-6 mb-3">
                                            <label for="">Duration Start:</label>
                                            <input type="datetime-local" class="form-control" name="durationstart" id="durationstart">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="">Duration End:</label>
                                            <input type="datetime-local" class="form-control" name="durationend" id="durationend">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="ticketstatus">Ticket Status:</label>
                                    <select class="form-control" id="ticketstatus" name="ticketstatus">
                                        <?php
                                        $select_query = mysqli_query($conn, "SELECT ticketstatus_id, ticketstatus FROM tblticket_status");
                                        while ($res = mysqli_fetch_array($select_query)) { ?>
                                            <option value="<?php echo $res['ticketstatus_id'] ?>">
                                                <?php echo $res['ticketstatus']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" id="btnCancelTicket" class="btnClose btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" id="btnsaveticket" class="btnAdd btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- END -->

            <div class="modal fade" id="ticketModal" tabindex="-1" role="dialog" aria-labelledby="ticketModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="ticketModalLabel">Ticket Details</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" id="ticketDetails">
                        </div>
                    </div>
                </div>
            </div>

        </main>

        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="js/script.js"></script>

</body>

</html>