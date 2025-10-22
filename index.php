<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Home", "main", null);
?>

<div class="container">
    <?php
        getSuccessOrFailureMessage();
    ?>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card" style="width: 100%;">
                    <img src="resources/images/expense-balance.png" class="card-img-top" alt="Expense Balance">
                    <div class="card-body">
                        <h5 class="card-title">Expense Balance</h5>
                        <p class="card-text">Calculate and Balance Shared Expenses Among Friends</p>
                        <a href="/pages/tools/expense-balance/view.php" class="btn btn-primary">Expense Balance</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card" style="width: 100%;">
                    <img src="resources/images/vehicle-tracker.png" class="card-img-top" alt="Vehicle Tracker">
                    <div class="card-body">
                        <h5 class="card-title">Vehicle Tracker</h5>
                        <p class="card-text">Track Vehicle's Daily Distance, Mileage and Maintenance</p>
                        <a href="/pages/tools/vehicle-tracker/view.php" class="btn btn-primary">Vehicle Tracker</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card" style="width: 100%;">
                    <img src="resources/images/bill-split-tracker.png" class="card-img-top" alt="Bill Split Tracker">
                    <div class="card-body">
                        <h5 class="card-title">Bill Split Tracker</h5>
                        <p class="card-text">Track group bills, split expenses, and manage shared payments easily.</p>
                        <a href="/pages/tools/bill-split-tracker/view.php" class="btn btn-primary">Bill Split Tracker</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card" style="width: 100%;">
                    <img src="resources/images/event-tracker.png" class="card-img-top" alt="Events Tracker">
                    <div class="card-body">
                        <h5 class="card-title">Events Tracker</h5>
                        <p class="card-text">Track Birthdays, Events and Anniversaries and their Celebrations</p>
                        <a href="/pages/tools/events-anniversary/view.php" class="btn btn-primary">Events Tracker</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card" style="width: 100%;">
                    <img src="resources/images/notebook.png" class="card-img-top" alt="Notebook">
                    <div class="card-body">
                        <h5 class="card-title">Notebook</h5>
                        <p class="card-text">Digital Notebook to take notes and manage your thoughts</p>
                        <a href="/pages/tools/Notebook/view.php" class="btn btn-primary">Notebook</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    initializePageFooter($rootPath, $moduleType);
?>
