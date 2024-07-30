<?php
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    $pageTitle = "Appointments";
    require_once $rootPath . '/config/config.php';
    require_once $rootPath . '/pages/includes/admin-pages/header.php';
    $conn = initDb();
?>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>

    <div class="container mt-5 mb-5">
        <?php
            if (isset($_POST['delete'])) {
                $idToDelete = $_POST['appointment_id'];
                $deleteQuery = "DELETE FROM appointments WHERE id = ?";
                $stmt = $conn->prepare($deleteQuery);
                
                if ($stmt === false) {
                    die("Error preparing the delete statement: " . $mysqli->error);
                }
                $stmt->bind_param("i", $idToDelete);
                
                if ($stmt->execute()) {
                    echo "<div class='alert alert-success' role='alert'>Appointment deleted successfully.</div>";
                } else {
                    echo "<div class='alert alert-danger' role='alert'>Error deleting appointment: " . $stmt->error . "</div>";
                }
                $stmt->close();
            }

            if (isset($_POST['add-disable-date'])) {
                // Retrieve the selected date from the form
                $date = $_POST["date"];
            
                $sql = "INSERT INTO appointments_disabled_dates (disabled_date) VALUES (?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $date);
            
                if ($stmt->execute()) {
                    echo "<div class='alert alert-success' role='alert'>Date disabled successfully.</div>";
                } else {
                    echo "<div class='alert alert-danger' role='alert'>Error adding date: " . $stmt->error . "</div>";
                }
            
                $stmt->close();
            }

            if (isset($_POST['delete_date'])) {
                // Handle date deletion
                $dateToDelete = $_POST['delete_date'];
                $deleteSql = "DELETE FROM appointments_disabled_dates WHERE disabled_date = '$dateToDelete'";
                if ($conn->query($deleteSql) === TRUE) {
                    echo '<div class="alert alert-success" role="alert">Date deleted successfully!</div>';
                } else {
                    echo '<div class="alert alert-danger" role="alert">Error deleting date: ' . $conn->error . '</div>';
                }
            }
        ?>
        <h4>Appointments</h4>
        <table id="appointmentsTable" class="table table-bordered">
        <caption class="display-none">Appointments</caption>
            <thead>
                <tr>
                    <th>Appointment Date</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Company Details</th>
                    <th>Session</th>
                    <th>Created At</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $totalRows = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM appointments"));
                $recordsPerPage = 10;
                $totalPages = ceil($totalRows / $recordsPerPage);
                $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
                $startFrom = ($page - 1) * $recordsPerPage;
                $sql = "SELECT * FROM appointments ORDER BY appointment_date DESC,
                            session LIMIT $startFrom, $recordsPerPage";
                $result = mysqli_query($conn, $sql);
                
                if ($result->num_rows > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $backgroundColor = '';
                        $backgroundColor = '';
                        switch ($row['status']) {
                            case 'Done':
                                $backgroundColor = 'background-color: #d7f8d7;'; // Lighter Green
                                break;
                            case 'Cancelled':
                                $backgroundColor = 'background-color: #f7d7d7;'; // Lighter Red
                                break;
                            case 'Rescheduled':
                                $backgroundColor = 'background-color: #f9f4d7;'; // Lighter Yellow
                                break;
                            case 'DNP':
                                $backgroundColor = 'background-color: #f0f0f0;'; // Lighter Grey
                                break;
                            default:
                        }
                        echo "<tr style='$backgroundColor'>";
                        echo "<td>" . $row['appointment_date'] . "</td>";
                        echo "<td>" . $row['name'] . "</td>";
                        echo "<td>" . $row['phone'] . "</td>";
                        echo "<td>" . $row['email'] . "</td>";
                        echo "<td>" . $row['company_details'] . "</td>";
                        echo "<td>" . $row['session'] . "</td>";
                        echo "<td>" . $row['created_at'] . "</td>";
                        echo "<td class='text-center'>";
                        echo $row['status'] . "<br/><span class='fw-lighter small'>" . $row['status_message'] . "</span>";
                        echo "</td>";
                        echo "<td style='text-align: center;'>";
                        echo "<div class='btn-group-vertical' role='group'>";
                        echo "<button class='btn btn-success btn-sm' onclick='openStatusPopup(" . $row["id"] . ", \"Done\")'>Mark as Done</button>";
                        echo "<button class='btn btn-warning btn-sm' onclick='openStatusPopup(" . $row["id"] . ", \"Rescheduled\")'>Reschedule</button>";
                        echo "<button class='btn btn-danger btn-sm' onclick='openStatusPopup(" . $row["id"] . ", \"Cancelled\")'>Cancel</button>";
                        echo "<button class='btn btn-secondary btn-sm' onclick='openStatusPopup(" . $row["id"] . ", \"DNP\")'>DNP</button>";
                        echo "<button type='submit' form='delete-appointment' class='btn btn-danger btn-sm' name='delete'>Delete</button>";
                        echo "</div>";
                        echo "<form method='POST' id='delete-appointment' onsubmit='return confirmDelete()'>";
                        echo "<input type='hidden' name='appointment_id' value='" . $row["id"] . "'>";
                        echo "</form>";
                        
                        echo "</td>";
                        echo "</tr>";
                        
                    }
                } else {
                    echo "<tr><td colspan='7'>No appointments found</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <div class="text-center mt-2">
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

        <h4 class="mt-5">Disabled Dates</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $sql = "SELECT disabled_date FROM appointments_disabled_dates";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["disabled_date"] . "</td>";
                            echo '<td>
                                    <form method="post" onsubmit="return confirmDelete()">
                                        <input type="hidden" name="delete_date" value="' . $row["disabled_date"] . '">
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                  </td>';
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2'>No disabled dates found.</td></tr>";
                    }
                ?>
            </tbody>
        </table>

        <h4 class ="mt-5">Disable a Date</h4>
        <form method="post">
            <label for="date">Select Date:</label>
            <input type="date" id="date" name="date" required>
            <button type="submit" name ="add-disable-date">Add Date</button>
        </form>


    </div>
    <script>
        $(document).ready(function() {
            $('#appointmentsTable').DataTable({
                paging: false,
                "order": [[0, "desc"], [6, "desc"]],
                "columnDefs": [
                    { "orderable": false, "targets": [1, 2, 3, 4, 8] }
                ],
                "bInfo": false,
            });
        });

        function confirmDelete() {
            return confirm("Are you sure you want to delete this ?");
        }

        function openStatusPopup(appointmentId, action) {
            var statusMessage = prompt("Enter status message for " + action + ":");

            if (statusMessage !== null) {
                var confirmed = confirm("Are you sure you want to set the status to " + action + "?");

                if (confirmed) {
                    fetch('/handlers/appointment/update_status.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            appointmentId: appointmentId,
                            action: action,
                            statusMessage: statusMessage,
                        }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                            // alert("Status set to " + action + " with message: " + statusMessage);
                        } else {
                            alert("Error updating status. Please try again.");
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                }
            }
        }

    </script>
<?php
$conn->close();
require_once $rootPath . '/pages/includes/admin-pages/footer.php';
?>
