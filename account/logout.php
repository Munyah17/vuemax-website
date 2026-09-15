<?php
require_once __DIR__ . '/inc.php';
unset($_SESSION['cust_id'], $_SESSION['cust_name']);
header('Location: login.php');
exit;
