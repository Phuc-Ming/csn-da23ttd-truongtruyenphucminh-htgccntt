<?php
if (empty($_SESSION['user_id'])) {
    header("Location: ../2auth/login.php");
    exit();
}
?>