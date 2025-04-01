<?php
    session_start();
    session_unset();
    session_destroy();
    header('Location: musicle_home.php');
    exit();
    
?>