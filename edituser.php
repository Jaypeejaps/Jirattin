<?php
session_start();
include "config.php";
$conn = open_connection();
if (isset($_SESSION['employee_id'])) {

    $employee_id = $_SESSION['employee_id'];
    $query = "SELECT tblemployee.employee_id, tblemployee.employee_name, tblemployee.employee_nickname, tblemployee.employee_email, 
    tblemployee.employee_number, tbldepartment.department_id, tbldepartment.department, tblemployee.employee_password
    FROM tblemployee
    LEFT JOIN tbldepartment ON tblemployee.department_id = tbldepartment.department_id
    WHERE employee_id = '$employee_id'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $employee = mysqli_fetch_assoc($result);
    } else {
        die("Error retrieving employee data: " . mysqli_error($conn));
    }
} else {
    die("Error: employeeid not set in the session");
}

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
    <title>Edit Profile | Jirattin</title>
    <link rel="icon" type="image/x-icon" href="css/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.2.1/css/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div class="container-fluids">
        <form id="editForm" method="post" action="dbquery/editcredentials.php" class="mx-autoss">
            <h4 class="text-center">Edit Credentials (੭•͈ω•͈)੭</h4>
            <input type="hidden" id="employee_id" name="employee_id" value="<?= $employee['employee_id']; ?>">
            <div class="row mt-3">
                <div class="col-md-6">
                    <label for="empname" class="form-label"><i class="bi bi-person"></i> Name</label>
                    <input type="text" class="form-control" id="empname" name="empname" value="<?= $employee['employee_name'] ?>" placeholder="Enter your name •⩊•">
                </div>
                <div class="col-md-6">
                    <label for="empnickname" class="form-label"><i class="bi bi-person-circle"></i> Nickname</label>
                    <input type="text" class="form-control" id="empnickname" name="empnickname" value="<?= $employee['employee_nickname'] ?>" placeholder="Enter your desired nickname •⩊•">
                </div>
            </div>
            <div class="mb-3 mt-3">
                <label for="empemail" class="form-label"><i class="bi bi-envelope-at"></i> Email</label>
                <input type="email" class="form-control" id="empemail" name="empemail" value="<?= $employee['employee_email'] ?>" placeholder="Enter your email •⩊•">
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label for="empnumber" class="form-label"><i class="bi bi-telephone"></i> Number</label>
                    <input type="number" class="form-control" id="empnumber" name="empnumber" value="<?= $employee['employee_number'] ?>" placeholder="Enter your contact number •⩊•">
                </div>
                <div class="col-md-6">
                    <label for="empdepartment" class="form-label"><i class="bi bi-person-gear"></i> Department</label>
                    <select class="form-control" id="empdepartment" name="empdepartment">
                        <?php
                        $select_query = mysqli_query($conn, "SELECT department_id, department FROM tbldepartment");
                        while ($res = mysqli_fetch_array($select_query)) { ?>
                            <option value="<?php echo $res['department_id']; ?>" <?php echo ($res['department_id'] == $employee['department_id']) ? 'selected' : ''; ?>>
                                <?php echo $res['department']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="mb-3 mt-3">
                <label for="emppassword" class="form-label"><i class="bi bi-key"></i> Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="emppassword" name="emppassword" placeholder="Enter Password •⩊•" required>
                    <span class="input-group-text">
                        <i class="bi bi-eye" id="togglePassword"></i>
                    </span>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <button type="submit" name="btnUpdateEmp" id="btnUpdateEmp" class="btnUpdate btn-primary mt-5"><i class="bi bi-unlock"></i> Update</button>
                </div>
                <div class="col-md-6">
                    <a href="dashboard.php"><button type="button" class="btn btn-primary mt-5"><i class="bi bi-house"></i> Home</button></a>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="js/script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var passwordInput = document.getElementById('emppassword');
            var togglePassword = document.getElementById('togglePassword');

            togglePassword.addEventListener('click', function() {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                } else {
                    passwordInput.type = 'password';
                }
            });
        });
    </script>
</body>

</html>