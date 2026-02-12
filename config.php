<?php
session_start();

$host = "sql309.infinityfree.com";
$user = "if0_41072872";
$pass = "nUs4zeg2iLl";
$db   = "if0_41072872_pg_rental_db";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Database connection failed");
}
?>