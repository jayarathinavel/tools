<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
        error_reporting(E_ALL & ~E_WARNING);
        $rootPath = $_SERVER['DOCUMENT_ROOT'];

        try {
            require_once $rootPath . '/config/config.php';
            $conn = initDb();
            $themeValue = fetchThemeValue($conn);
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
    <title><?php echo (isset($pageTitle) && !empty($pageTitle)) ? $pageTitle : "Tools" ?></title>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-primary navbar-dark p-2 mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Tools</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
            <button class="nav-link back-button active" onclick="goBack()"> < Back</button>
        </li>
        <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="/">Home</a>
        </li>
        <!-- <li class="nav-item">
            <a class="nav-link" href="#">Page</a>
        </li>
        <li class="nav-item">
            <a class="nav-link disabled" aria-disabled="true">Disabled</a>
        </li> -->
      </ul>
    </div>
  </div>
</nav>
