<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Event Details", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/events-anniversary/events-anniversary-utils.php');

    $id = intval($_GET['id']);
    $event = executeQuery("SELECT * FROM events_anniversary WHERE id=$id")->fetch_assoc();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['celebration_date'])) {
        includePhpFileFromRoot($rootPath, '/handlers/tools/celebration-handler.php');
        // Will redirect back
    }

    $celebrations = executeQuery("SELECT * FROM events_anniversary_celebration WHERE event_id=$id ORDER BY date DESC");
    $next = nextOccurrence($event['original_date']);
?>
<div class="container">
    <?php getSuccessOrFailureMessage(); ?>
    <div class="d-flex justify-content-between align-items-center">
        <h1><?php echo htmlspecialchars($event['name']); ?></h1>
        <div class="d-flex">
            <a href="edit.php?id=<?php echo $event['id']; ?>" class="btn btn-warning btn-sm m-1"><i class="bi bi-pencil-fill"></i> Edit</a>
            <a href="delete.php?operation=delete&id=<?php echo $event['id']; ?>" class="btn btn-danger btn-sm m-1" onclick="return confirmDeleteEvent();"><i class="bi bi-trash-fill"></i> Delete</a>
        </div>
    </div>
    <div class="mt-2">
        <p><strong>Note:</strong> <?php echo nl2br(htmlspecialchars($event['note'])); ?></p>
        <p><strong>Original:</strong> <?php echo formatDisplayDate($event['original_date']); ?></p>
        <p><strong>Next Occurrence:</strong> <?php echo formatDisplayDate($next); ?></p>
        <p><strong>Added:</strong> <?php echo formatDisplayDate($event['added_date']); ?></p>
    </div>

    <hr>
    <h4>Celebrations</h4>
    <div style="overflow-x:auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Note</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($celebrations && $celebrations->num_rows > 0): ?>
                    <?php while($c = $celebrations->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo formatDisplayDate($c['date']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($c['note'])); ?></td>
                            <td><a href="delete.php?operation=delete-celebration&id=<?php echo $c['id']; ?>&event_id=<?php echo $event['id']; ?>" class="btn btn-danger btn-sm m-1" onclick="return confirmDeleteCelebration();"><i class="bi bi-trash-fill"></i></a></td>

                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="2">No celebrations added yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="mb-3">
        <h4>Add Celebration</h4>
        <form action="" method="post" onsubmit="return validateCelebrationForm();">
            <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
            <div class="form-group">
                <label for="celebration_date">Date:</label>
                <input type="date" id="celebration_date" name="celebration_date" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="celebration_note">Note/Description: <span class="fw-light">(Optional)</span></label>
                <textarea id="celebration_note" name="celebration_note" class="form-control" rows="2" placeholder="Short description"></textarea>
            </div>
            <input type="submit" value="Add Celebration" class="btn btn-primary mt-2">
        </form>
    </div>
</div>

<script>
    function validateCelebrationForm() {
        const date = document.getElementById('celebration_date').value;
        if (!date) {
            alert("Please select a celebration date.");
            return false;
        }
        return true;
    }

    function confirmDeleteEvent() {
        return confirm("Deleting an event will also delete all its celebrations. Are you sure?");
    }

    function confirmDeleteCelebration() {
        return confirm("Are you sure you want to delete this celebration?");
    }
</script>

<?php
    initializePageFooter($rootPath, $moduleType);
    setTodaysDateForForm();
?>
