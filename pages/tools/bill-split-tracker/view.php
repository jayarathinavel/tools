<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Bill Split", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/bill-split-tracker/bill-split-utils.php');
    $userId = billSplitUser();
    $book = findBookBillSplit($userId)
?>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>

<div class="container">
    <?php
        getSuccessOrFailureMessage();
    ?>
    <h1>Bill Split Tracker</h1>
    <?php
        $bills = [];
        $billSplitBooks = executeQuery("SELECT * FROM bill_split_book WHERE user_id=$userId");
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['billSplitBookId'])) {
                $book = intval($_POST['billSplitBookId']);
                $_SESSION['billSplitSelectedBook'] = $book;
                if (isset($_SESSION['appUserId'])) {
                    setUserDefaultBook('bill_split', $book);
                }
                setSuccessOrFailureMessage("success", "Book Changed");
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        }
        if(isset($book)) {
            $bills = executeQuery("SELECT * FROM bill_split WHERE bill_split_book_id = $book ORDER BY date DESC, id DESC");
            $persons = fetchPersonsFromBillSplitBook($book);
            $summary = calculateBillSplitSummary($book);
        }
    ?>
    <div class="text-center">
        <?php if(isset($book)) { ?>
            <div class="mt-2">
                <span>Book: </span>
                <form style="display:inline" action="" method="POST">
                    <select name="billSplitBookId" id="billSplitBookId">
                        <?php while($row = mysqli_fetch_assoc($billSplitBooks)): ?>
                            <option value="<?php echo $row['id']; ?>" <?php echo ($row['id'] == $book) ? 'selected' : ''; ?>>
                                <?php echo $row['name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <input type="submit" value="Change" class="btn btn-sm btn-primary">
                </form>
                <a class="btn btn-sm btn-secondary" href="books/view.php">Manage Books</a>
            </div>
        <?php } ?>
        
        <?php if (isset($summary) && !empty($summary)): ?>
            <div class="mt-3">
                <h5>Quick Balance Overview</h5>
                <div class="row">
                    <?php foreach ($summary as $person => $data): ?>
                        <div class="col-md-3 mb-2">
                            <div class="card <?php echo $data['balance'] > 0 ? 'border-success' : ($data['balance'] < 0 ? 'border-danger' : 'border-secondary'); ?>">
                                <div class="card-body text-center p-2">
                                    <h6 class="card-title mb-1"><?php echo $person; ?></h6>
                                    <p class="card-text mb-0">
                                        <?php if ($data['balance'] > 0): ?>
                                            <span class="text-success">Gets ₹<?php echo number_format(abs($data['balance']), 2); ?></span>
                                        <?php elseif ($data['balance'] < 0): ?>
                                            <span class="text-danger">Owes ₹<?php echo number_format(abs($data['balance']), 2); ?></span>
                                        <?php else: ?>
                                            <span class="text-secondary">Settled</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <a class="btn btn-sm btn-info" href="summary.php">View Summary</a>
        <?php endif; ?>
    </div>
    
    <div class="mt-2 mb-2">
        <?php echo isset($book) ? '' : '<div class="text-danger mb-2"> No books are available, <a href="books/add.php">create a book </a> first!</div>' ?>
        <a href="add.php" class="btn btn-primary <?php echo isset($book) ? '' : 'disabled' ?>" >Add New Bill</a>
    </div>
    
    <h4>Bills List</h4>
    <div class="p-2" style="overflow-x: auto; border: 1px solid #DBDADA; border-radius: 5px; ">
        <table id="bills-table" class="table">
            <thead>
                <tr>
                    <th>Bill Name</th>
                    <th>Description</th>
                    <th>Total Amount</th>
                    <th>Paid By</th>
                    <th>Split Type</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    if (isset($bills)) {
                        foreach ($bills as $bill) {
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($bill['bill_name']); ?></td>
                    <td><?php echo htmlspecialchars($bill['description']); ?></td>
                    <td>₹<?php echo number_format($bill['total_amount'], 2); ?></td>
                    <td><?php echo htmlspecialchars($bill['paid_by']); ?></td>
                    <td>
                        <span class="badge <?php echo $bill['split_type'] == 'equal' ? 'bg-primary' : 'bg-secondary'; ?>">
                            <?php echo ucfirst($bill['split_type']); ?> Split
                        </span>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($bill['date'])); ?></td>
                    <td>
                        <a href="edit.php?id=<?php echo $bill['id']; ?>" class="btn btn-warning btn-sm m-1"><i class="bi bi-pencil-fill"></i></a>
                        <a href="delete.php?operation=delete&id=<?php echo $bill['id']; ?>" class="btn btn-danger btn-sm m-1" onclick="return confirmDelete();"><i class="bi bi-trash-fill"></i></a>
                    </td>
                </tr>
                <?php 
                        }
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#bills-table').DataTable({
            paging: false,
            "order": [[5, "desc"]],
            "columnDefs": [
                { "orderable": false, "targets": [6] },
                { width: "220px", targets: [0, 1] }
            ],
            "bInfo": false,
        });
    });

    function confirmDelete() {
        return confirm("Are you sure you want to delete this bill?");
    }
</script>

<?php
    initializePageFooter($rootPath, $moduleType);
?>
