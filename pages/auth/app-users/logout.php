<?php
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    require_once $rootPath . '/config/config.php';
    startSession();
    // Remove remember-me tokens for user if present
    if(isset($_SESSION['appUserId']) && function_exists('deleteRememberTokensForUser')){
        deleteRememberTokensForUser($_SESSION['appUserId']);
    }
    $_SESSION = array();
    session_destroy();
    setSuccessOrFailureMessage("success", "You are Logged Out Successfully");
    header("location: /");
    exit;