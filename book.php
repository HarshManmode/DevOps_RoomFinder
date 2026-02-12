<?php
include "config.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_POST) {
    $user_id = $_SESSION['user_id'];
    $room_id = intval($_POST['room_id']);
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    
    // Validate input
    if ($room_id <= 0) {
        header("Location: index.php");
        exit();
    }
    
    // Validate dates
    if (strtotime($from_date) >= strtotime($to_date)) {
        header("Location: room.php?id=$room_id&error=date");
        exit();
    }
    
    // Check if room exists
    $room_check = mysqli_query($conn, "SELECT * FROM rooms WHERE id=$room_id");
    if (mysqli_num_rows($room_check) == 0) {
        header("Location: index.php");
        exit();
    }
    // booking validation update

    // Check for existing bookings
    $check_booking = mysqli_query($conn, "SELECT * FROM bookings WHERE room_id=$room_id AND 
        ((from_date <= '$from_date' AND to_date > '$from_date') OR 
         (from_date < '$to_date' AND to_date >= '$to_date') OR
         (from_date >= '$from_date' AND to_date <= '$to_date'))");
    
    if (mysqli_num_rows($check_booking) > 0) {
        header("Location: room.php?id=$room_id&error=booked");
        exit();
    }
    
    // Insert booking
    $insert_query = "INSERT INTO bookings(user_id, room_id, from_date, to_date) 
                   VALUES($user_id, $room_id, '$from_date', '$to_date')";
    
    if (mysqli_query($conn, $insert_query)) {
        header("Location: room.php?id=$room_id&message=success");
        exit();
    } else {
        header("Location: room.php?id=$room_id&error=db");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>
