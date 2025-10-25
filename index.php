<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
initializePage("Home", "main", null);
?>

<div class="container">
    <?php getSuccessOrFailureMessage(); ?>
    <div class="container">
        <div class="row justify-content-center" id="toolCards">
            <!-- Each card has a data-app attribute for identification -->
            <div class="col-md-6 col-lg-4 mb-4" data-app="expense-balance">
                <div class="card" style="width: 100%;">
                    <img src="resources/images/expense-balance.png" class="card-img-top" alt="Expense Balance">
                    <div class="card-body">
                        <h5 class="card-title">Expense Balance</h5>
                        <p class="card-text">Calculate and Balance Shared Expenses Among Friends</p>
                        <a href="/pages/tools/expense-balance/view.php" class="btn btn-primary app-link">Expense Balance</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4" data-app="vehicle-tracker">
                <div class="card" style="width: 100%;">
                    <img src="resources/images/vehicle-tracker.png" class="card-img-top" alt="Vehicle Tracker">
                    <div class="card-body">
                        <h5 class="card-title">Vehicle Tracker</h5>
                        <p class="card-text">Track Vehicle's Daily Distance, Mileage and Maintenance</p>
                        <a href="/pages/tools/vehicle-tracker/view.php" class="btn btn-primary app-link">Vehicle Tracker</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4" data-app="bill-split-tracker">
                <div class="card" style="width: 100%;">
                    <img src="resources/images/bill-split-tracker.png" class="card-img-top" alt="Bill Split Tracker">
                    <div class="card-body">
                        <h5 class="card-title">Bill Split Tracker</h5>
                        <p class="card-text">Track group bills, split expenses, and manage shared payments easily.</p>
                        <a href="/pages/tools/bill-split-tracker/view.php" class="btn btn-primary app-link">Bill Split Tracker</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4" data-app="events-tracker">
                <div class="card" style="width: 100%;">
                    <img src="resources/images/event-tracker.png" class="card-img-top" alt="Events Tracker">
                    <div class="card-body">
                        <h5 class="card-title">Events Tracker</h5>
                        <p class="card-text">Track Birthdays, Events and Anniversaries and their Celebrations</p>
                        <a href="/pages/tools/events-anniversary/view.php" class="btn btn-primary app-link">Events Tracker</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4" data-app="notebook">
                <div class="card" style="width: 100%;">
                    <img src="resources/images/notebook.png" class="card-img-top" alt="Notebook">
                    <div class="card-body">
                        <h5 class="card-title">Notebook</h5>
                        <p class="card-text">Digital Notebook to take notes and manage your thoughts</p>
                        <a href="/pages/tools/notebook/view.php" class="btn btn-primary app-link">Notebook</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // --- Manage app usage and ordering using localStorage ---
    document.addEventListener("DOMContentLoaded", () => {
        const container = document.getElementById("toolCards");
        const cards = Array.from(container.children);

        // Load usage counts from localStorage
        const usageData = JSON.parse(localStorage.getItem("appUsageCounts") || "{}");

        // Sort cards by usage count (descending)
        cards.sort((a, b) => {
            const appA = a.dataset.app;
            const appB = b.dataset.app;
            const countA = usageData[appA]?.count || 0;
            const countB = usageData[appB]?.count || 0;

            // If same count, use most recently used first
            const lastUsedA = usageData[appA]?.lastUsed || 0;
            const lastUsedB = usageData[appB]?.lastUsed || 0;

            if (countB === countA) return lastUsedB - lastUsedA;
            return countB - countA;
        });

        // Reorder cards
        cards.forEach(card => container.appendChild(card));

        // Track click events to update usage counts
        document.querySelectorAll(".app-link").forEach(link => {
            link.addEventListener("click", () => {
                const appKey = link.closest("[data-app]").dataset.app;
                const data = JSON.parse(localStorage.getItem("appUsageCounts") || "{}");

                if (!data[appKey]) {
                    data[appKey] = { count: 0, lastUsed: 0 };
                }

                data[appKey].count += 1;
                data[appKey].lastUsed = Date.now();

                localStorage.setItem("appUsageCounts", JSON.stringify(data));
            });
        });
    });
</script>

<?php
initializePageFooter($rootPath, $moduleType);
?>
