<?php
session_start();
$_SESSION= [];
session_destroy();
header('Location: ../Pages/login.html');
exit();
?>