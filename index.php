<?php
    $pageTitle = "Home";
    require_once 'pages/includes/main-pages/header.php';
?>

<div class="container">
    <?php
        getSuccessOrFailureMessage();
    ?>
    <div class="card" style="width: 18rem;">
        <img src="resources/images/expense-balance.jpg" class="card-img-top" alt="...">
        <div class="card-body">
            <h5 class="card-title">Expense Balance</h5>
            <p class="card-text">Calculate and Balance Shared Expenses Among Friends</p>
            <a href="/pages/tools/expense-balance/view.php" class="btn btn-primary">Expense Balance</a>
        </div>
    </div>
    <?php
        if(isAppUserLoggedIn()) {
            echo'
                <div class="text-center p-2 mt-4" style="border: 1px solid #DBDADA; border-radius: 5px;">
                    <span class="fw-bold"> Settings</span><br>
                    <a href="/pages/auth/app-users/reset-password.php"> Reset Password</a>
                </div>
            ';
        }
    ?>

</div>

<?php
    require_once 'pages/includes/main-pages/footer.php';
?>
