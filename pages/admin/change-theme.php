<?php
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    $pageTitle = "Change Theme";
    require_once $rootPath . '/config/config.php';
    require_once $rootPath . '/pages/includes/admin-pages/header.php';
    isLoggedIn();
    $successMessage = '';
    $errorMessage = '';
    try {
        $themeValue = fetchThemeValue();
        if (isset($_POST['theme']) && ($_POST['theme'] != $themeValue)) {
            $selectedTheme = $_POST['theme'];
            $sql = "INSERT INTO variables (`key`, `value`) VALUES ('theme', '$selectedTheme')
                    ON DUPLICATE KEY UPDATE `value` = '$selectedTheme'";
            $result = executeQuery($sql);

            if ($result) {
                setSuccessOrFailureMessage('success', 'Theme updated successfully.');
                setThemeToSession($selectedTheme);
            } else {
                setSuccessOrFailureMessage('failure', 'Failed to update the theme.');
            }
        }
    } catch (Exception $e) {
        setSuccessOrFailureMessage('failure', $e->getMessage());
    }
?>

<div class="container mt-5">
<h4 class="text-center">Select a theme</h4>
    <form action="#" method="post" class="text-center">
        <div class="form-group">
            <select class="form-control" name="theme">
                <?php
                    $folderPath = $rootPath . '/resources/bootswatch'; // Replace with the path to your folder
                    if (is_dir($folderPath)) {
                        $themes = scandir($folderPath);
                        echo "<option value='default'>Default</option>";
                        foreach ($themes as $theme) {
                            if ($theme != '.' && $theme != '..' && is_dir($folderPath . '/' . $theme)) {
                                $folderName = ucwords(str_replace('_', ' ', $theme));
                                echo "<option value='$theme'>$folderName</option>";
                            }
                        }
                    }
                ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
    <?php
        getSuccessOrFailureMessage();
    ?>
</div>

<!-- To set selected value -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const themeValue = "<?php echo $themeValue; ?>";
        const selectElement = document.querySelector("select[name='theme']");
        selectElement.value = themeValue;
    });
</script>

<?php
    require_once $rootPath . '/pages/includes/admin-pages/footer.php';
?>
