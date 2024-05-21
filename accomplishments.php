<?php
date_default_timezone_set('Asia/Manila');

session_start();
$userlevel_id = $_SESSION['userlevel_id'] ?? null;
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

    <title>Accomplishments | Jirattin</title>
</head>

<body>

    <?php include "sidebar.php"; ?>

    <div class="main-contentticket">
        <?php include "header.php"; ?>

        <main>

            <!-- START YOUR CODE HERE -->

            <div class="recent-grid">
                <div class="ticketsss">
                    <div class="card-body">
                        <div class="filteremp">
                            <?php if ($userlevel_id == 1) { ?>
                                <select class="form-control select2" name="assignnametime" id="assignnametime">
                                    <option value="">Select Employee</option>
                                    <?php
                                    $select_query = mysqli_query($conn, "SELECT employee_name, employee_id FROM tblemployee ORDER BY employee_name ASC");
                                    while ($res = mysqli_fetch_array($select_query)) { ?>
                                        <option value="<?php echo $res['employee_id'] ?>">
                                            <?php echo $res['employee_name']; ?></option>
                                    <?php } ?>
                                </select>
                                <button class="btnemp empclick" id="btnFilterEmployee">Filter</button>
                                <button class="btnemp empclick" id="btnResetEmployee">Reset</button>
                            <?php } ?>

                            <div class="filterdones">
                                Start: <input id="startdate" type="date">
                                End: <input id="enddate" type="date">
                                <button class="startclick" onclick="filterDate()">Filter</button>
                                <button class="resetclick" onclick="resetDate()">Reset</button>

                                Day: <input type="date" id="datePicker">
                                <button class="startdayclick" onclick="filterDay()">Filter</button>
                                <button class="resetdayclick" onclick="resetDay()">Reset</button>
                            </div>
                        </div>

                        <div class="outer-wrapper">
                            <div class="table-wrap">
                                <table class="table table-bordered table-striped" id="ticketingsdone">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Accomplishment Works</th>
                                            <th>Employee</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ticketdone">
                                    </tbody>
                                </table>
                                <div class="bottons">
                                    <div id="paginationdone" class="paginationdone">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- END -->
        </main>

        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="js/script.js"></script>
</body>

</html>