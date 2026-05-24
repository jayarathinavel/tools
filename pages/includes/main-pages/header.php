<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
        error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING &~E_DEPRECATED);
        $rootPath = $_SERVER['DOCUMENT_ROOT'];

        try {
            $themeValue = fetchThemeValue();
            $cssFilePath = $rootPath . '/resources/bootswatch/' . $themeValue . '/bootstrap.min.css';
            if (isset($themeValue) && !empty($themeValue) && $themeValue != "default" && file_exists($cssFilePath)) {
                echo '
                    <link rel="stylesheet" href="/resources/bootswatch/' . $themeValue . '/bootstrap.min.css">
                ';
            } else {
                if(empty($themeValue)) {
                    throw new UnexpectedValueException("Theme value not found !");
                }
                elseif(!file_exists($cssFilePath)) {
                    throw new Exception("CSS File does not exist in the location " . $cssFilePath);
                }
                echo '
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
                        rel="stylesheet">
                ';
            }
        } catch (Exception $e) {
            echo '
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
            ';
            echo '
                <script>console.error("' . addslashes($e->getMessage()) . '");</script>
            ';
        }
    ?>
    <link rel="stylesheet" href="/resources/stylesheet.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="/resources/favicon.svg">
    <title><?php echo (isset($pageTitle) && !empty($pageTitle)) ? $pageTitle : "Tools" ?></title>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-primary navbar-dark p-2 mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="/"> <i class="bi bi-tools"></i> Tools</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
            <a class="nav-link" href="/pages/tools/cashbook/view.php">
                <i class="bi bi-cash-stack me-2"></i>Cashbook
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/pages/tools/vehicle-tracker/view.php">
                <i class="bi bi-truck me-2"></i>Vehicle Tracker
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/pages/tools/expense-balance/view.php">
                <i class="bi bi-wallet2 me-2"></i>Expense Balance
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/pages/tools/bill-split-tracker/view.php">
                <i class="bi bi-receipt-cutoff me-2"></i>Bill Split Tracker
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/pages/tools/notebook/view.php">
                <i class="bi bi-journal-text me-2"></i>Notebook
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/pages/tools/events-anniversary/view.php">
                <i class="bi bi-calendar-event me-2"></i>Events Tracker
            </a>
        </li>
        <?php
            if(isAppUserLoggedIn()){
                echo'
                    <li class="nav-item dropstart">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        '.getFromSession("username").'
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/pages/auth/app-users/reset-password.php">Rest Password</a></li>
                        <li><a class="dropdown-item" href="/pages/auth/app-users/logout.php">Logout</a></li>
                    </ul>
                    </li>
                ';
            }
        ?>
      </ul>
    </div>
  </div>
</nav>
<?php
    function generateBreadcrumbs() {
        $path = $_SERVER['REQUEST_URI'];
        $path = trim($path, '/');
        $pathArray = explode('/', $path);

        $breadcrumbs = '
        <nav aria-label="breadcrumb" class="d-flex justify-content-between align-items-center">
            <ol class="breadcrumb">
        ';
        $breadcrumbs .= '<li class="breadcrumb-item"><a href="/">Home</a></li>';

        $currentPath = '';
        foreach ($pathArray as $key => $value) {
            $currentPath .= '/' . $value;

            // Skip "pages" and "tools" from the breadcrumb trail
            if ($value !== 'pages' && $value !== 'tools'
                    && $value !== 'auth' && $value !== 'app-users') {
                // Remove ".php" extension and handle "id" query parameters
                $fileName = pathinfo($value, PATHINFO_FILENAME);
                $label = ucfirst(str_replace('-', ' ', $fileName));

                if ($key < count($pathArray) - 1) {
                    $breadcrumbs .= '<li class="breadcrumb-item"><a href="' . $currentPath . '/view.php">' . $label . '</a></li>';
                } else {
                    $breadcrumbs .= '<li class="breadcrumb-item active" aria-current="page">' . $label . '</li>';
                }
            }
        }

        $breadcrumbs .= '
            </ol>
            <div id="breadcrumbSpinner" class="spinner-border spinner-border-sm text-primary mb-auto" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </nav>';
        return $breadcrumbs;
    }
?>

<div class="container">
    <?php echo generateBreadcrumbs(); ?>
</div>
