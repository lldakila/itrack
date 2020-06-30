<?php

if (session_status() == PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['itrack_user_id'])) header("location: login.php");

?>