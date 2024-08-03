<?php
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    require_once $rootPath. '/config/config.php';
    isLoggedIn();
    $pageTitle = "Welcome Admin";
    require_once $rootPath . '/pages/includes/admin-pages/header.php';
?>
<h4 class="text-center"> Welcome Admin </h4>
<div class="text-center">
    <a href="admin/contact-submissions.php">Contact Form Submissions </a> <br/>
    <a href="admin/change-theme.php">Change Theme</a> <br/>
    <h5> Settings </h5>
    <a href="/pages/auth/reset-password.php">Reset Password</a> <br>
    <a href="/pages/auth/logout.php">Logout</a>
</div>
<?php
    require_once $rootPath . '/pages/includes/admin-pages/footer.php';
?>
