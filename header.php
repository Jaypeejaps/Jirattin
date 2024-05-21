<?php
include "config.php";

if (isset($_SESSION["employee_nickname"])) {
    $userNickname = $_SESSION["employee_nickname"];

    $conn = open_connection();
    $query = "SELECT tblemployee.employee_nickname, tbldepartment.department FROM tblemployee
    LEFT JOIN tbldepartment ON tblemployee.department_id = tbldepartment.department_id
    WHERE employee_nickname = '$userNickname'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $userFullName = $row["employee_nickname"];
        $userDepartment = $row["department"];
    } else {
        $userFullName = "Guest";
        $userDepartment = "Unknown Department";
    }
} else {
    $userFullName = "Guest";
    $userDepartment = "Unknown Department";
}
?>

<header>
    <label for="nav-toggle">
        <span class="bi bi-list"></span>
    </label>
    <h2 id="page-title">Dashboard</h2>

    <div class="user-wrapper">
        <span class="bi bi-person"></span>
        <div>
            <h5><?php echo $userFullName; ?></h5>
            <small><?php echo $userDepartment; ?></small>
        </div>
    </div>
</header>