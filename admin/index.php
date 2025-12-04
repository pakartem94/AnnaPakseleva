<?php
// Redirect to login if not logged in, otherwise to dashboard
require_once 'config.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
} else {
    header('Location: login.php');
}
exit;


