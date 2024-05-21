<?php

function open_connection()
{
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "vigantzjiraedition";


    $conn = mysqli_connect($servername, $username, $password, $dbname);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    return $conn;
}
