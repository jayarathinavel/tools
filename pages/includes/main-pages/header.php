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

<!-- Header -->
<header class="bg-primary py-4">
    <div class="container">
        <div class="row d-flex align-items-center">
            <div class="col-md-6">
                <h4 class="text-light">Tools</h4>
            </div>
            <div class="col-md-6 text-end">
                <a href="/" class="btn btn-light">Home</a>
            </div>
        </div>
    </div>
</header>
