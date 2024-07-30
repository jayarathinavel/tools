<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="/resources/stylesheet.css">
    <title><?php echo (isset($pageTitle) && !empty($pageTitle)) ? $pageTitle : "Admin Dashboard" ?></title>
</head>
<body>

<!-- Header -->
<header class="bg-dark text-white py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h4>Admin</h4>
            </div>
            <div class="col-md-6 text-right">
                <a href="/pages/admin" class="btn btn-light">Home</a>
                <a href="/pages/admin/help.php" class="btn btn-light">Help</a>
            </div>
        </div>
    </div>
</header>
