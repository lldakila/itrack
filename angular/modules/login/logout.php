<?php

session_start();

if (isset($_SESSION['id'])) unset($_SESSION['id']);
if (isset($_SESSION['group'])) unset($_SESSION['group']);
if (isset($_SESSION['office'])) unset($_SESSION['office']);

echo "Logout Successful";

header("location: ../index.html");

?>