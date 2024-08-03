<?php
    $pageTitle = "Home";
    require_once 'pages/includes/main-pages/header.php';
?>

<div class="container">
    <?php
        getSuccessOrFailureMessage();
    ?>
    <a href="/pages/tools/expense-balance/view.php"> Expense Balance</a> </br>
    <?php
        if(isAppUserLoggedIn()) {
            echo'
                <a href="/pages/auth/app-users/reset-password.php"> Reset Password</a>
            ';
        }
    ?>

</div>

<?php
    require_once 'pages/includes/main-pages/footer.php';
?>
