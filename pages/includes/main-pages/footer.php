<div class="mt-4"></div>
<script>
    window.addEventListener('load', function () {
        const spinner = document.getElementById('breadcrumbSpinner');
        if (spinner) {
            spinner.style.display = 'none';
        }
    });

    //Alerts auto close
    document.addEventListener('DOMContentLoaded', () => {
        const alerts = document.querySelectorAll('.alert-dismissible');

        alerts.forEach(alertEl => {
            setTimeout(() => {
                const alert = bootstrap.Alert.getOrCreateInstance(alertEl);
                alert.close();
            }, 4000); // 4 seconds
        });
    });

</script>
<!-- Bootstrap JS and jQuery -->
<?php
    $currentURL = $_SERVER['REQUEST_URI'];
    echo !(strpos($currentURL, 'pages/tools/expense-balance/view.php')
            || strpos($currentURL, 'pages/tools/expense-balance/books/view.php')
            || strpos($currentURL, 'pages/tools/vehicle-tracker/odometer/view.php')
            || strpos($currentURL, 'pages/tools/vehicle-tracker/mileage/view.php')
            || strpos($currentURL, 'pages/tools/vehicle-tracker/maintenance/view.php')
            || strpos($currentURL, 'pages/tools/bill-split-tracker/view.php')) ?
        '<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>' : '';
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
