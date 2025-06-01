<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Bill Split Summary", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/bill-split-tracker/bill-split-utils.php');
    $userId = billSplitUser();
    $book = findBookBillSplit($userId)
?>

<div class="container">
    <?php
        getSuccessOrFailureMessage();
    ?>
    <h1>Bill Split Summary</h1>
    <?php
        $billSplitBooks = executeQuery("SELECT * FROM bill_split_book WHERE user_id=$userId");
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['billSplitBookId'])) {
                $book = $_POST['billSplitBookId'];
                $_SESSION['billSplitSelectedBook'] = $book;
                setSuccessOrFailureMessage("success", "Book Changed");
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        }
        if(isset($book)) {
            $bills = executeQuery("SELECT * FROM bill_split WHERE bill_split_book_id = $book");
            $persons = fetchPersonsFromBillSplitBook($book);
            $summary = calculateBillSplitSummary($book);
            $totalAmount = 0;
            $billCount = 0;
            foreach ($bills as $bill) {
                $totalAmount += $bill['total_amount'];
                $billCount++;
            }
        }
    ?>
    
    <div class="text-center mb-4">
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
        <?php } else { ?>
            <div class="text-danger mb-2"> No books are available, <a href="books/add.php">create a book </a> first!</div>
        <?php } ?>
    </div>

    <?php if(isset($book)): ?>
        <div class="row mb-4 justify-content-center">
            <div class="col-auto">
                <a class="btn btn-sm btn-info" href="view.php">View Bills</a>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Total Bills</h5>
                        <h3 class="text-primary"><?php echo $billCount; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Total Amount</h5>
                        <h3 class="text-success">₹<?php echo number_format($totalAmount, 2); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Average per Person</h5>
                        <h3 class="text-info">₹<?php echo count($persons) > 0 ? number_format($totalAmount / count($persons), 2) : '0.00'; ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Balance Summary -->
        <div class="card mb-4">
            <div class="card-header">
                <h4>Balance Summary for <?php echo htmlspecialchars(executeQuery("SELECT name FROM bill_split_book WHERE id=$book")->fetch_assoc()['name']); ?></h4>
            </div>
            <div class="card-body">
                <?php if (empty($summary)): ?>
                    <div class="text-center py-4">
                        <p class="text-muted">No bills found for this book.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Person</th>
                                    <th>Total Paid</th>
                                    <th>Total Owes</th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Sort by balance (highest to lowest)
                                uasort($summary, function($a, $b) {
                                    return $b['balance'] <=> $a['balance'];
                                });
                                
                                foreach ($summary as $person => $data): 
                                ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($person); ?></strong></td>
                                    <td>₹<?php echo number_format($data['totalPaid'], 2); ?></td>
                                    <td>₹<?php echo number_format($data['totalOwes'], 2); ?></td>
                                    <td>
                                        <span class="<?php echo $data['balance'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                            <?php if ($data['balance'] > 0): ?>
                                                <i class="bi bi-arrow-up"></i>
                                            <?php elseif ($data['balance'] < 0): ?>
                                                <i class="bi bi-arrow-down"></i>
                                            <?php else: ?>
                                                <i class="bi bi-dash"></i>
                                            <?php endif; ?>
                                            ₹<?php echo number_format(abs($data['balance']), 2); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($data['balance'] > 0): ?>
                                            <span class="badge bg-success">Gets Back</span>
                                        <?php elseif ($data['balance'] < 0): ?>
                                            <span class="badge bg-danger">Owes</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Settled</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Settlement Suggestions -->
        <?php if (!empty($summary)): ?>
            <div class="card">
                <div class="card-header">
                    <h4>Settlement Suggestions</h4>
                </div>
                <div class="card-body">
                    <?php
                    $debtors = [];
                    $creditors = [];
                    $allSettled = true;
                    
                    foreach ($summary as $person => $data) {
                        if ($data['balance'] < -0.01) {
                            $debtors[] = ['person' => $person, 'amount' => abs($data['balance'])];
                            $allSettled = false;
                        } elseif ($data['balance'] > 0.01) {
                            $creditors[] = ['person' => $person, 'amount' => $data['balance']];
                            $allSettled = false;
                        }
                    }
                    
                    if ($allSettled): ?>
                        <div class="alert alert-success text-center">
                            <i class="bi bi-check-circle"></i> <strong>All settled!</strong> Everyone is even.
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($debtors as $debtor): ?>
                                <?php 
                                $creditor = null;
                                foreach ($creditors as $c) {
                                    if ($c['amount'] >= $debtor['amount']) {
                                        $creditor = $c;
                                        break;
                                    }
                                }
                                if ($creditor): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="alert alert-info">
                                            <strong><?php echo htmlspecialchars($debtor['person']); ?></strong> should pay 
                                            <strong><?php echo htmlspecialchars($creditor['person']); ?></strong>
                                            <span class="badge bg-primary ms-2">₹<?php echo number_format($debtor['amount'], 2); ?></span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php
    initializePageFooter($rootPath, $moduleType);
?>
