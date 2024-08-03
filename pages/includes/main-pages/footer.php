<!-- Footer -->
<!-- <footer class="bg-secondary py-3">
    <div class="container text-center">
        &copy; 2023 My Website. All rights reserved.
    </div>
</footer> -->

<!-- Bootstrap JS and jQuery -->
<?php
    $currentURL = $_SERVER['REQUEST_URI'];
    echo !(strpos($currentURL, 'pages/tools/expense-balance/view.php')
            || strpos($currentURL, 'pages/tools/expense-balance/books/view.php')) ?
        '<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>' : '';
?>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
