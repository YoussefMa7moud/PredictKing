<?php
session_start();

if (isset($_SESSION['UserID'])) {
    if (isset($_SESSION['Role']) && $_SESSION['Role'] === 'admin') {
        header("Location: Admin.php");
    } else {
        header("Location: Home.php");
    }
} else {
    header("Location: Login.php");
}
exit();
?>