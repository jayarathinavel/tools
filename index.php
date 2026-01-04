<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
initializePage("Home", "main", null);
includePhpFileFromRoot($rootPath, '/pages/tools/events-anniversary/events-anniversary-utils.php');
$userId = $_SESSION['appUserId'] ?? null;
?>

<div class="container">
    <?php getSuccessOrFailureMessage(); ?>
    
    <!-- Added upcoming events reminders section -->
    <?php if ($userId): ?>
        <?php
            $upcomingEvents = executeQuery("SELECT id, name, original_date, type FROM events_anniversary WHERE user_id = $userId ORDER BY original_date ASC");
            $eventsToRemind = [];
            if ($upcomingEvents && $upcomingEvents->num_rows > 0) {
                while ($row = $upcomingEvents->fetch_assoc()) {
                    $nextOccurrence = nextOccurrence($row['original_date']);
                    $daysUntil = getDaysUntilEvent($nextOccurrence);
                    
                    // Only show events within 7 days
                    if ($daysUntil >= 0 && $daysUntil <= 7) {
                        $eventsToRemind[] = [
                            'id' => $row['id'],
                            'name' => $row['name'],
                            'type' => $row['type'],
                            'nextDate' => $nextOccurrence,
                            'daysUntil' => $daysUntil
                        ];
                    }
                }
            }
            
            if (!empty($eventsToRemind)):
        ?>
            <div class="mb-4">
                <h5 class="mb-3">Upcoming Events</h5>
                <?php foreach ($eventsToRemind as $event): ?>
                    <?php $alertColor = getAlertColor($event['daysUntil']); ?>
                    <a href="/pages/tools/events-anniversary/event.php?id=<?php echo $event['id']; ?>" style="text-decoration: none;">
                        <div class="alert alert-<?php echo $alertColor; ?> mb-2" role="alert" style="cursor: pointer; transition: opacity 0.2s;">
                            <strong><?php echo htmlspecialchars($event['name']); ?></strong> 
                            <span class="badge bg-dark ms-2"><?php echo ucfirst($event['type']); ?></span>
                            <br>
                            <small>
                                <?php echo formatDisplayDate($event['nextDate']); ?> 
                                (<?php echo $event['daysUntil']; ?> day<?php echo $event['daysUntil'] !== 1 ? 's' : ''; ?> away)
                            </small>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="container mb-3 text-end">
            <a href="/pages/auth/app-users/login.php" class="btn btn-sm btn-primary">Login</a>
        </div>
    <?php endif; ?>

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
            <div class="col-md-6 col-lg-4 mb-4" data-app="cashbook">
                <div class="card" style="width: 100%;">
                    <img src="resources/images/cashbook.png" class="card-img-top" alt="Cashbook">
                    <div class="card-body">
                        <h5 class="card-title">Cashbook</h5>
                        <p class="card-text">Manage your personal finances with ease using Cashbook</p>
                        <a href="/pages/tools/cashbook/view.php" class="btn btn-primary app-link">Cashbook</a>
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
