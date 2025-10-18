<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Notebook", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/notebook/notebook-utils.php');
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/Parsedown.php';
    $userId = notebookUser();
    $notebook = findNotebook($userId);
    $parsedown = new Parsedown();
?>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>

<div class="container">
    <?php
        getSuccessOrFailureMessage();
    ?>
    <h1>Notebook</h1>
    <?php
        $pages = [];
        $notebooks = executeQuery("SELECT * FROM notebook WHERE user_id=$userId");
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['notebookId'])) {
                $notebook = $_POST['notebookId'];
                $_SESSION['notebookSelected'] = $notebook;
                setSuccessOrFailureMessage("success", "Notebook Changed");
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        }
        if(isset($notebook)) {
            $pages = executeQuery("SELECT * FROM notebook_page WHERE notebook_id = $notebook ORDER BY created_at DESC");
        }
    ?>
    <div class="text-center">
        <?php if(isset($notebook)) { ?>
            <div class="mt-2">
                <span>Notebook: </span>
                <form style="display:inline" action="" method="POST">
                    <select name="notebookId" id="notebookId">
                        <?php while($row = mysqli_fetch_assoc($notebooks)): ?>
                            <option value="<?php echo $row['id']; ?>" <?php echo ($row['id'] == $notebook) ? 'selected' : ''; ?>>
                                <?php echo $row['name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <input type="submit" value="Change" class="btn btn-sm btn-primary">
                </form>
                <a class="btn btn-sm btn-secondary" href="notebooks/view.php">Manage Notebooks</a>
            </div>
        <?php } ?>
    </div>
    <div class="mt-2 mb-2">
        <?php echo isset($notebook) ? '' : '<div class="text-danger mb-2"> No notebooks are available, <a href="notebooks/add.php">create a notebook </a> first!</div>' ?>
        <a href="add.php" class="btn btn-primary <?php echo isset($notebook) ? '' : 'disabled' ?>" >Add New Page</a>
    </div>
    <h4>Pages List</h4>
    <div class="p-2" style="overflow-x: auto; border: 1px solid #DBDADA; border-radius: 5px; ">
        <table id="pages-table" class="table">
            <thead>
                <tr>
                    <th>Page Name</th>
                    <th>Created</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    if(isset($pages)) {
                        foreach ($pages as $page) {
                ?>
                <tr>
                    <td><?php echo $page['page_name']; ?></td>
                    <td><?php echo date('M d, Y H:i', strtotime($page['created_at'])); ?></td>
                    <td>
                        <a href="view-page.php?id=<?php echo $page['id']; ?>" class="btn btn-info btn-sm m-1"><i class="bi bi-eye-fill"></i></a>
                        <a href="edit.php?id=<?php echo $page['id']; ?>" class="btn btn-warning btn-sm m-1"><i class="bi bi-pencil-fill"></i></a>
                        <a href="delete.php?operation=delete&id=<?php echo $page['id']; ?>" class="btn btn-danger btn-sm m-1" onclick="return confirmDelete();"><i class="bi bi-trash-fill"></i></a>
                    </td>
                </tr>
                <?php 
                        }
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#pages-table').DataTable({
            paging: false,
            "order": [[1, "desc"]],
            "columnDefs": [
                { "orderable": false, "targets": [2] }
            ],
            "bInfo": false,
        });
    });

    function confirmDelete() {
        return confirm("Are you sure you want to delete this page?");
    }
</script>

<?php
    initializePageFooter($rootPath, $moduleType);
?>
