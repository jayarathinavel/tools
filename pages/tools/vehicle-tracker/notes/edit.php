<?php
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    $pageTitle = "Edit Note";
    require_once $rootPath . '/pages/includes/main-pages/header.php';
    appUserLoginRequired($_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/vehicle-tracker/vehicle-tracker-utils.php');
    $id = $_GET['id'];
    $note = $conn->query("SELECT * FROM vt_notes WHERE id=$id")->fetch_assoc();
?>

<div class="container">
    <h1>Edit Note</h1>
    <?php
    if (isset($_POST['date'])) {
        includePhpFileFromRoot($rootPath, '/handlers/tools/vehicle-tracker-notes-handler.php');
    }
    ?>
    <form action="" method="post">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <div class="form-group">
            <label for="note">Date:</label>
            <input type="date" id="date" name="date" class="form-control" value="<?php echo $note['date']; ?>" required>
        </div>
        <div class="form-group">
            <label for="note">Note:</label>
            <textarea id="note" name="note" class="form-control" required><?php echo $note['note']; ?></textarea>
        </div>
        <input type="submit" value="Update Note" class="btn btn-primary mt-2">
    </form>
</div>

<?php
    appUserLoginRequiredClose();
    require_once $rootPath . '/pages/includes/main-pages/footer.php';
?>
