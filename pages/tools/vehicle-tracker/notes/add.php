<?php
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    $pageTitle = "Add Note";
    require_once $rootPath . '/pages/includes/main-pages/header.php';
    appUserLoginRequired($_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/vehicle-tracker/vehicle-tracker-utils.php');
?>

<div class="container">
    <h1>Add Note</h1>
    <?php
    if (isset($_POST['date'])) {
        includePhpFileFromRoot($rootPath, '/handlers/tools/vehicle-tracker-notes-handler.php');
    }
    ?>
    <form action="" method="post">
        <div class="form-group">
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="note">Note:</label>
            <textarea id="note" name="note" class="form-control" required></textarea>
        </div>
        <input type="submit" value="Add Note" class="btn btn-primary mt-2">
    </form>
</div>

<?php
    appUserLoginRequiredClose();
    require_once $rootPath . '/pages/includes/main-pages/footer.php';
    setTodaysDateForForm();
?>
