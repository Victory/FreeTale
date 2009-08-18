<?php
session_start();
$_SESSION['logged_in']="No I Am Not";
header("Location: ./login.php");
?>