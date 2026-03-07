<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Edit Cashbook Entry", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/cashbook/cashbook-utils.php');

    $id = intval($_GET['id']);
    canEditTransaction($id);
    $entry = executeQuery("SELECT * FROM cashbook_entry WHERE id=$id")->fetch_assoc();
    if (!$entry) {
        echo '<div class="alert alert-danger">Entry not found.</div>';
        exit;
    }

    // Use entry's book to fetch accounts/categories
    $bookId = intval($entry['cashbook_book_id']);
    $accounts = fetchBankAccountsFromCashbook($bookId);
    $categories = fetchCategoriesFromCashbook($bookId);

    if (isset($_POST['title'])) {
        includePhpFileFromRoot($rootPath, '/handlers/tools/cashbook-handler.php');
    }
?>
<div class="container">
    <form method="post" action="">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <div class="form-group">
            <label>Title</label>
            <input required name="title" class="form-control" value="<?php echo htmlspecialchars($entry['title']); ?>">
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($entry['description'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label>Amount</label>
            <input required name="amount" type="number" step="0.01" class="form-control" value="<?php echo $entry['amount']; ?>">
        </div>
        <div class="form-group">
            <label>Type</label>
            <select name="type" class="form-control">
                <option value="expense" <?php echo ($entry['type'] === 'expense') ? 'selected' : ''; ?>>Expense</option>
                <option value="income" <?php echo ($entry['type'] === 'income') ? 'selected' : ''; ?>>Income</option>
                <option value="lend" <?php echo ($entry['type'] === 'lend') ? 'selected' : ''; ?>>Lend</option>
                <option value="lend_repayment" <?php echo ($entry['type'] === 'lend_repayment') ? 'selected' : ''; ?>>Lend Repayment</option>
                <option value="transfer_in" <?php echo ($entry['type'] === 'transfer_in') ? 'selected' : ''; ?>>Transfer In</option>
                <option value="transfer_out" <?php echo ($entry['type'] === 'transfer_out') ? 'selected' : ''; ?>>Transfer Out</option>
                <option value="investment" <?php echo ($entry['type'] === 'investment') ? 'selected' : ''; ?>>Investment</option>
            </select>
        </div>
        <div class="form-group">
            <label>Category</label>
            <select name="category_id" class="form-control">
                <option value="" hidden>Select a category</option>
                <?php foreach($categories as $cid => $cname): ?>
                    <option value="<?php echo $cid; ?>" <?php echo ($entry['category_id'] == $cid) ? 'selected' : ''; ?>><?php echo htmlspecialchars($cname); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Bank Account</label>
            <select name="bank_account_id" class="form-control">
                <option value="" hidden>Select an account</option>
                <?php foreach($accounts as $a): ?>
                    <option value="<?php echo $a['id']; ?>" <?php echo ($entry['bank_account_id'] == $a['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($a['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Date & Time</label>
            <input type="datetime-local" name="date" class="form-control" value="<?php echo ($entry['date']) ? date('Y-m-d\TH:i', strtotime($entry['date'])) : date('Y-m-d\TH:i'); ?>">
        </div>
        <button class="btn btn-primary mt-2" type="submit">Save</button>
        <a class="btn btn-secondary mt-2" href="view.php">Cancel</a>
    </form>
</div>
<?php
    initializePageFooter($rootPath, $moduleType);
?>