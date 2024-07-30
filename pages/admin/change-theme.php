<?php
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    $pageTitle = "Change Theme";
    require_once $rootPath . '/config/config.php';
    require_once $rootPath . '/pages/includes/admin-pages/header.php';
    isLoggedIn();
    $successMessage = '';
    $errorMessage = '';
    try {
        $conn = initDb();
        $themeValue = fetchThemeValue($conn);
        if (isset($_POST['theme']) && ($_POST['theme'] != $themeValue)) {
            $selectedTheme = $_POST['theme'];
            $sql = "INSERT INTO variables (`key`, `value`) VALUES ('theme', ?)
                    ON DUPLICATE KEY UPDATE `value` = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $selectedTheme, $selectedTheme);

            if ($stmt->execute()) {
                $successMessage = 'Theme updated successfully.';
                $themeValue = fetchThemeValue($conn);
            } else {
                $errorMessage = 'Failed to update the theme.';
            }
            $stmt->close();
        }
    } catch (Exception $e) {
        $errorMessage = 'An error occurred: ' . $e->getMessage();
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
                                // Convert folder name to sentence case
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
    if ($successMessage) {
        echo '<div class="alert alert-success mt-3">' . $successMessage . '</div>';
    }
    if ($errorMessage) {
        echo '<div class="alert alert-danger mt-3">' . $errorMessage . '</div>';
    }
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
$conn->close();
require_once $rootPath . '/pages/includes/admin-pages/footer.php';
?>
