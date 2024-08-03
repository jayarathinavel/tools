<?php
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    require_once $rootPath . '/config/config.php';
    startSession();
    $_SESSION = array();
    session_destroy();
    setSuccessOrFailureMessage("success", "You are Logged Out Successfully");
    header("location: /");
    exit;