<?php
session_start();

/*
 |---------------------------------------------------------
 | HBM Bank – Application Entry Point
 |---------------------------------------------------------
 | If user is logged in → redirect to dashboard
 | Else → redirect to login page
 */

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard/index.php");
    exit;
} else {
    header("Location: auth/login.php");
    exit;
}
