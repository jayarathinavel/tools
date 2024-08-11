<?php
    $pageTitle = "Home";
    require_once 'pages/includes/main-pages/header.php';
?>

<div class="container">
    <?php
        getSuccessOrFailureMessage();
    ?>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card" style="width: 100%;">
                    <img src="resources/images/expense-balance.jpg" class="card-img-top" alt="Expense Balance">
                    <div class="card-body">
                        <h5 class="card-title">Expense Balance</h5>
                        <p class="card-text">Calculate and Balance Shared Expenses Among Friends</p>
                        <a href="/pages/tools/expense-balance/view.php" class="btn btn-primary">Expense Balance</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card" style="width: 100%;">
                    <img src="resources/images/vehicle-tracker.jpg" class="card-img-top" alt="Vehicle Tracker">
                    <div class="card-body">
                        <h5 class="card-title">Vehicle Tracker</h5>
                        <p class="card-text">Track Vehicle's Daily Distance, Mileage and Maintenance</p>
                        <a href="/pages/tools/vehicle-tracker/view.php" class="btn btn-primary">Vehicle Tracker</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    require_once 'pages/includes/main-pages/footer.php';
?>
