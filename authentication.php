<?php

session_start();

if (!isset($_SESSION['itrack_user_id'])) header("location: login.html");

?>