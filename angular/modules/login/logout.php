<?php

session_start();

if (isset($_SESSION['itrack_user_id'])) unset($_SESSION['itrack_user_id']);
if (isset($_SESSION['group'])) unset($_SESSION['group']);
if (isset($_SESSION['office'])) unset($_SESSION['office']);

echo "Logout Successful";

header("location: ../../../login.html");

?>