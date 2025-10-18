<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Add Event", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/events-anniversary/events-anniversary-utils.php');
?>
<div class="container">
    <h1>Add Event</h1>
    <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
            includePhpFileFromRoot($rootPath, '/handlers/tools/events-anniversary-handler.php');
        }
    ?>
    <form action="" method="post" onsubmit="return validateEventForm();">
        <div class="form-group">
            <label for="name">Name: <span class="fw-light">(Required)</span></label>
            <input type="text" id="name" name="name" class="form-control" placeholder="Birthday - Alice" required>
        </div>
        <div class="form-group">
            <label for="original_date">Original Date: <span class="fw-light">(Required)</span></label>
            <input type="date" id="original_date" name="original_date" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="note">Note: <span class="fw-light">(Optional)</span></label>
            <textarea id="note" name="note" class="form-control" rows="3" placeholder="Any additional info"></textarea>
        </div>
        <input type="submit" value="Add Event" class="btn btn-primary mt-2">
    </form>
</div>

<script>
    function validateEventForm() {
        const name = document.getElementById('name').value.trim();
        const date = document.getElementById('original_date').value;
        if (!name) {
            alert("Please enter a name.");
            return false;
        }
        if (!date) {
            alert("Please select an original date.");
            return false;
        }
        return true;
    }
</script>

<?php
    initializePageFooter($rootPath, $moduleType);
    setTodaysDateForForm(); // Will set today's date for <input type="date"> if you rely on a helper
?>
