<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Events & Anniversary", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/events-anniversary/events-anniversary-utils.php');
    $userId = eventsAnniversaryUser();
?>

<div class="container">
    <?php getSuccessOrFailureMessage(); ?>
    <h1>Events & Anniversary</h1>
    <div style="overflow-x:auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Original Date</th>
                    <th>Next Occurrence</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $events = executeQuery("SELECT * FROM events_anniversary WHERE user_id=$userId ORDER BY name ASC");
                    if ($events && $events->num_rows > 0):
                        while ($row = $events->fetch_assoc()):
                            $next = nextOccurrence($row['original_date']);
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst($row['type'])); ?></td>
                        <td><?php echo formatDisplayDate($row['original_date']); ?></td>
                        <td><?php echo formatDisplayDate($next); ?></td>
                        <td>
                            <a href="event.php?id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm m-1">View</a>
                            <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm m-1"><i class="bi bi-pencil-fill"></i></a>
                            <a href="delete.php?operation=delete&id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm m-1" onclick="return confirmDeleteEvent();"><i class="bi bi-trash-fill"></i></a>
                        </td>
                    </tr>
                <?php
                        endwhile;
                    else:
                ?>
                    <tr><td colspan="5">No events yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <a href="add.php" class="btn btn-primary mt-2">Add New Event</a>
</div>

<script>
    function confirmDeleteEvent() {
        return confirm("Deleting an event will also delete all its celebrations. Are you sure?");
    }
</script>

<?php
    initializePageFooter($rootPath, $moduleType);
?>
