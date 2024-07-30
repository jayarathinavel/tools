<?php
$rootPath = $_SERVER['DOCUMENT_ROOT'];
$pageTitle = "Contact Submissions";
require_once $rootPath . '/config/config.php';
isLoggedIn();
$errorMsg = '';
try {
    $conn = initDb();
    $totalRows = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM contact_form"));
    // Define the number of records per page
    $recordsPerPage = 10;
    $totalPages = ceil($totalRows / $recordsPerPage);
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
    $startFrom = ($page - 1) * $recordsPerPage;
    $sql = "SELECT * FROM contact_form ORDER BY submission_timestamp DESC LIMIT $startFrom, $recordsPerPage";
    $result = mysqli_query($conn, $sql);
} catch (Exception $e) {
    $errorMsg = "An error occurred: " . $e->getMessage();
}
require_once $rootPath . '/pages/includes/admin-pages/header.php';
?>

<div class="container">
    <h4 class="text-center">Contact Form Submissions</h4>

    <?php
        if (!empty($errorMsg)) {
            echo '<div class="alert alert-danger mt-3">';
            echo $errorMsg;
            echo '</div>';
        }
    ?>
    <div class="table-responsive">
        <table class="table table-bordered">
            <caption class="display-none">Contact Form Submissions</caption>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Submission Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    if (empty($errorMsg)) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $row['id'] . "</td>";
                            echo "<td>" . $row['first_name'] . "</td>";
                            echo "<td>" . $row['last_name'] . "</td>";
                            echo "<td>" . $row['email'] . "</td>";
                            echo "<td>" . $row['subject'] . "</td>";
                            echo "<td>" . $row['message'] . "</td>";
                            echo "<td>" . $row['submission_timestamp'] . "</td>";
                            echo "</tr>";
                        }
                    }
                ?>
            </tbody>
        </table>
    </div>
    
    <?php
        if (mysqli_num_rows($result) == 0) {
            echo '<p class="text-center">No data available</p>';
        }
    ?>
    
    <!-- Pagination links -->
    <div class="text-center">
        <ul class="pagination">
            <?php if ($page > 1) : ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                </li>
            <?php endif; ?>

            <?php
            $startPage = max(1, $page - 1);
            $endPage = min($totalPages, $page + 1);

            if ($startPage > 1) :
            ?>
                <li class="page-item">
                    <a class="page-link" href="?page=1">1</a>
                </li>
                <?php if ($startPage > 2) : ?>
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                <?php endif; ?>
            <?php endif; ?>

            <?php for ($i = $startPage; $i <= $endPage; $i++) : ?>
                <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($endPage < $totalPages) : ?>
                <?php if ($endPage < $totalPages - 1) : ?>
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                <?php endif; ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $totalPages; ?>"><?php echo $totalPages; ?></a>
                </li>
            <?php endif; ?>

            <?php if ($page < $totalPages) : ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<?php
require_once $rootPath . '/pages/includes/admin-pages/footer.php';
?>
