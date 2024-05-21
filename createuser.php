<?php
include "config.php";
$conn = open_connection();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Jirattin</title>
    <link rel="icon" type="image/x-icon" href="css/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.2.1/css/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    
    <div class="container-fluids">
        <form id="createForm" method="post" class="mx-autoss">
            <h4 class="text-center">Create User (੭•͈ω•͈)੭</h4>
            <div class="row mt-3">
                <div class="col-md-6">
                    <label for="empname" class="form-label"><i class="bi bi-person"></i> Name</label>
                    <input type="text" class="form-control" id="empname" name="empname" placeholder="Enter your name •⩊•" required>
                </div>
                <div class="col-md-6">
                    <label for="empnickname" class="form-label"><i class="bi bi-person-circle"></i> Nickname</label>
                    <input type="text" class="form-control" id="empnickname" name="empnickname" placeholder="Enter your desired nickname •⩊•" required>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <label for="empemail" class="form-label"><i class="bi bi-envelope-at"></i> Email</label>
                    <input type="email" class="form-control" id="empemail" name="empemail" placeholder="Enter your email •⩊•" required>
                </div>
                <div class="col-md-6">
                    <label for="empnumber" class="form-label"><i class="bi bi-telephone"></i> Number</label>
                    <input type="number" class="form-control" id="empnumber" name="empnumber" placeholder="Enter your contact number •⩊•" required>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <label for="empdepartment" class="form-label"><i class="bi bi-person-gear"></i> Department</label>
                    <select class="form-control select2" name="empdepartment" id="empdepartment" required>
                        <option value="">Select Department •⩊•</option>
                        <?php
                        $select_query = mysqli_query($conn, "SELECT department, department_id FROM tbldepartment ORDER BY department ASC");
                        while ($res = mysqli_fetch_array($select_query)) { ?>
                            <option value="<?php echo $res['department_id'] ?>"><?php echo $res['department']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="empuserlevel" class="form-label"><i class="bi bi-person-up"></i> User Level</label>
                    <select class="form-control select2" name="empuserlevel" id="empuserlevel" required>
                        <option value="">User Level •⩊•</option>
                        <?php
                        $select_query = mysqli_query($conn, "SELECT user_level, userlevel_id FROM tbluser_level ORDER BY user_level ASC");
                        while ($res = mysqli_fetch_array($select_query)) { ?>
                            <option value="<?php echo $res['userlevel_id'] ?>"><?php echo $res['user_level']; ?></option>
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
                    <button type="submit" name="btnCreateuser" id="btnCreateuser" class="btnCreateuser btn-primary mt-5"><i class="bi bi-person-add"></i> Create</button>
                </div>
                <div class="col-md-6">
                    <a href="index.php"><button type="button" class="btn btn-primary mt-5"><i class="bi bi-box-arrow-left"></i> Login</button></a>
                </div>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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