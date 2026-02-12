<?php
include "../config.php";
if ($_POST) {
mysqli_query($conn,"INSERT INTO rooms(title,location,price,description)
VALUES('{$_POST['title']}','{$_POST['location']}','{$_POST['price']}','{$_POST['description']}')");
}
?>
<form method="post">
Title:<input name="title"><br>
Location:<input name="location"><br>
Price:<input name="price"><br>
Description:<textarea name="description"></textarea><br>
<button>Add Room</button>
</form>