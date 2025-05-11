<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Health", null, null);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="/resources/stylesheet.css">
        <title>Health</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>

    <nav class="navbar navbar-expand-lg bg-success navbar-dark p-2 mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Tools</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="/">Home</a>
                </li>
            </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h4 class="text-center">Health</h4>
        <?php
            getSuccessOrFailureMessage();
        ?>
        <table class="table table-bordered table-responsive-md text-center">
        <caption style="display:none">Health Table</caption>
            <thead class="thead-dark">
                <tr>
                    <th>Name</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $versionValue = "N/A";
                    $dbStatus = "Down";

                    try {
                        $query = "SELECT `value` FROM variables WHERE `key` = 'version'";
                        $result = executeQuery($query);
                        if ($result->num_rows > 0) {
                            $row = $result->fetch_assoc();
                            $versionValue = $row['value'];
                            $dbStatus = "Up";
                        }
                    } catch (Exception $e) {
                        setSuccessOrFailureMessage("failure", $e->getMessage());
                    }
                ?>
                <tr>
                    <td>Version</td>
                    <td>
                        <?php echo $versionValue; ?>
                    </td>
                </tr>
                <tr class="<?php echo ($dbStatus == 'Up') ? '' : 'table-danger'; ?>">
                    <td>Database Status</td>
                    <td>
                        <?php echo $dbStatus; ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php
        initializePageFooter($rootPath, $moduleType);
    ?>
    <!-- Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
</html>
