<?php
    $pageTitle = "Expense Balance";
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    require_once $rootPath . '/pages/includes/main-pages/header.php';
    appUserLoginRequired($_SERVER['REQUEST_URI']);
?>

<div class="container">
    <a href="odometer/view.php">
        Odometer
    </a>
    <br>
    <a href="vehicles/view.php">
        Vehicles
    </a>
</div>
<?php
    appUserLoginRequiredClose();
    require_once $rootPath . '/pages/includes/main-pages/footer.php';
?>
