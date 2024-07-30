<?php
    $pageTitle = "Health";
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    require_once $rootPath . '/config/config.php';

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

        <header class="bg-success py-4">
            <div class="container">
                <div class="row d-flex align-items-center">
                    <div class="col-md-6">
                        <h4>Health</h4>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="/" class="btn btn-light">Home</a>
                    </div>
                </div>
            </div>
        </header>

        <div class="container mt-5">
            <h4 class="text-center">Health</h4>
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
                        $conn = initDb();
                        $query = "SELECT `value` FROM variables WHERE `key` = 'version'";
                        $result = mysqli_query($conn, $query);
                        if ($result->num_rows > 0) {
                            $row = $result->fetch_assoc();
                            $versionValue = $row['value'];
                            $dbStatus = "Up";
                        }
                    } catch (Exception $e) {
                        $error_message = $e->getMessage();
                    }

                    try {
                        $smtpStatus = checkSMTPStatus(SMTP_SERVER, 587, SMTP_USERNAME, SMTP_PASSWORD);
                    } catch (Exception $e) {
                        $error_message = $error_message . "<br/>" . $e ->getMessage();
                    }
                    try {
                        $urlToCheck = "youtube.com";
                        $certificateInfo = checkCertificateValidity($urlToCheck);
                    } catch(Exception $e) {
                        $error_message = $error_message . "<br/>" . $e ->getMessage();
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
                    <tr class="<?php echo ($smtpStatus) ? '' : 'table-danger'; ?>">
                        <td>Mail Server Status</td>
                        <td>
                            <?php echo ($smtpStatus) ? 'Up' : 'Down'; ?>
                        </td>
                    </tr>
                    <tr class="display-none <?php echo ($certificateInfo['is_valid']) ? '' : 'table-danger'; ?>">
                        <td>HTTPS Certificate Status</td>
                        <td>
                            <b>Status:</b> <?php echo ($certificateInfo['is_valid']) ? 'Valid' : 'Invalid'; ?> <br />
                            <b>Subject:</b> <?php echo isset($certificateInfo['subject'])
                                ? printAssociativeArray($certificateInfo['subject']) : 'N/A'; ?> <br />
                            <b>Issuer:</b> <?php echo isset($certificateInfo['issuer'])
                                ? printAssociativeArray($certificateInfo['issuer']) : 'N/A'; ?> <br />
                            <b>Valid From:</b> <?php echo isset($certificateInfo['valid_from'])
                                ? $certificateInfo['valid_from'] : 'N/A'; ?> <br />
                            <b>Valid To:</b> <?php echo isset($certificateInfo['valid_to'])
                                ? $certificateInfo['valid_from'] : 'N/A'; ?> <br />

                        </td>
                    </tr>
                </tbody>
            </table>
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger" role="alert">
                    <strong>Error:</strong> <br/>
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
        </div>
        <!-- Bootstrap JS and jQuery -->
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
</html>
