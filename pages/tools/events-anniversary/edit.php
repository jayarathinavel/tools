<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Edit Event", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/events-anniversary/events-anniversary-utils.php');

    $id = intval($_GET['id']);
    $event = executeQuery("SELECT * FROM events_anniversary WHERE id=$id")->fetch_assoc();
?>
<div class="container">
    <h1>Edit Event</h1>
    <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
            includePhpFileFromRoot($rootPath, '/handlers/tools/events-anniversary-handler.php');
        }
    ?>
    <form action="" method="post" onsubmit="return validateEventForm();">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <div class="form-group">
            <label for="name">Name: <span class="fw-light">(Required)</span></label>
            <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($event['name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="original_date">Original Date: <span class="fw-light">(Required)</span></label>
            <input type="date" id="original_date" name="original_date" class="form-control" value="<?php echo $event['original_date']; ?>" required>
        </div>
        <div class="form-group">
            <label for="type">Type: <span class="fw-light">(Required)</span></label>
            <select id="type" name="type" class="form-control" required onchange="toggleCustomTypeInput()">
                <option value="">Select a type</option>
                <option value="birthday" <?php echo ($event['type'] === 'birthday') ? 'selected' : ''; ?>>Birthday</option>
                <option value="anniversary" <?php echo ($event['type'] === 'anniversary') ? 'selected' : ''; ?>>Anniversary</option>
                <option value="memory" <?php echo ($event['type'] === 'memory') ? 'selected' : ''; ?>>Memory</option>
                <option value="custom" <?php echo ($event['type'] === 'custom') ? 'selected' : ''; ?>>Custom</option>
            </select>
        </div>
        <div class="form-group" id="customTypeGroup" style="display: <?php echo ($event['type'] === 'custom') ? 'block' : 'none'; ?>;">
            <label for="custom_type">Custom Type: <span class="fw-light">(Required)</span></label>
            <input type="text" id="custom_type" name="custom_type" class="form-control" placeholder="Enter custom event type" value="<?php echo ($event['type'] === 'custom') ? htmlspecialchars($event['name']) : ''; ?>">
        </div>
        <div class="form-group">
            <label for="note">Note:</label>
            <textarea id="note" name="note" class="form-control" rows="3"><?php echo htmlspecialchars($event['note']); ?></textarea>
        </div>
        <input type="submit" value="Update Event" class="btn btn-primary mt-2">
    </form>
</div>

<script>
    function toggleCustomTypeInput() {
        const typeSelect = document.getElementById('type');
        const customTypeGroup = document.getElementById('customTypeGroup');
        const customTypeInput = document.getElementById('custom_type');
        
        if (typeSelect.value === 'custom') {
            customTypeGroup.style.display = 'block';
            customTypeInput.required = true;
        } else {
            customTypeGroup.style.display = 'none';
            customTypeInput.required = false;
            customTypeInput.value = '';
        }
    }

    function validateEventForm() {
        const name = document.getElementById('name').value.trim();
        const date = document.getElementById('original_date').value;
        const type = document.getElementById('type').value;
        const customType = document.getElementById('custom_type').value.trim();
        
        if (!name) {
            alert("Please enter a name.");
            return false;
        }
        if (!date) {
            alert("Please select an original date.");
            return false;
        }
        if (!type) {
            alert("Please select an event type.");
            return false;
        }
        if (type === 'custom' && !customType) {
            alert("Please enter a custom event type.");
            return false;
        }
        return true;
    }

    // Initialize on page load
    window.addEventListener('load', function() {
        toggleCustomTypeInput();
    });
</script>

<?php
    initializePageFooter($rootPath, $moduleType);
?>
