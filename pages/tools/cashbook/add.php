<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Add Cashbook Entry", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/cashbook/cashbook-utils.php');
    $userId = cashbookUser();
    $book = findBookCashbook($userId);
    if (isset($_POST['title'])) {
        includePhpFileFromRoot($rootPath, '/handlers/tools/cashbook-handler.php');
    }
    $accounts = $book ? fetchBankAccountsFromCashbook($book) : [];
    $categories = $book ? fetchCategoriesFromCashbook($book) : [];
    $prefill = $_SESSION['cashbook_prefill'] ?? [];
?>
<div class="container">
    <h1>Add Entry</h1>
    <?php getSuccessOrFailureMessage(); ?>
    <?php if(!$book) echo '<div class="alert alert-danger">No book. Create one first.</div>'; ?>
    <form method="post" action="">
        <div class="form-group">
            <label>Title</label>
            <input required name="title" class="form-control">
        </div>
        <div class="form-group">
            <label>Amount</label>
            <input required name="amount" type="number" step="0.01" class="form-control">
        </div>
        <div class="form-group">
            <label>Type</label>
            <select name="type" id="entryType" class="form-control" required>
                <?php
                    $types = [
                        'expense' => 'Expense',
                        'income' => 'Income',
                        'repayment' => 'Credit Card Repayment',
                        'lend' => 'Lend',
                        'lend_repayment' => 'Lend Repayment',
                        'transfer' => 'Transfer',
                        'investment' => 'Investment'
                    ];

                    $selectedType = $prefill['type'] ?? 'expense';

                    foreach ($types as $value => $label) {
                        $selected = ($selectedType === $value) ? 'selected' : '';
                        echo "<option value=\"$value\" $selected>$label</option>";
                    }
                ?>
            </select>

        </div>
        <div class="form-group">
            <label>Category <small><a href="categories/view.php">View Categories</a></small></label>
            <select name="category_id" class="form-control">
                <option value="" hidden>Select a category</option>
                <?php foreach($categories as $id => $name): ?>
                    <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Bank Account <small><a href="banks/view.php">View Accounts</a></small></label>
            <select name="bank_account_id" class="form-control" required>
                <?php foreach ($accounts as $a): ?>
                    <option value="<?= $a['id'] ?>"
                        <?= (($prefill['bank_account_id'] ?? '') == $a['id']) ? 'selected' : '' ?>>
                        <?= $a['name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group d-none" id="toAccountWrapper">
            <label>To Bank Account</label>
            <select name="to_account_id" class="form-control">
                <option hidden value="">Select account</option>
                <?php foreach($accounts as $a): ?>
                    <option value="<?= $a['id']; ?>"><?= $a['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Date & Time</label>
            <input type="datetime-local" name="date" class="form-control" value="<?= htmlspecialchars($prefill['date'] ?? date('Y-m-d\TH:i')) ?>">
        </div>
        <input type="hidden" name="cashbook_book_id" value="<?php echo $book; ?>">
        <button type="submit" name="action" value="save" class="btn btn-primary mt-2">
            Save
        </button>
        <button type="submit" name="action" value="save_new" class="btn btn-outline-primary ms-2 mt-2">
            Save & Add New
        </button>
    </form>
</div>
<?php unset($_SESSION['cashbook_prefill']); ?>
<script>
    document.getElementById('entryType').addEventListener('change', function () {
        const toAccount = document.getElementById('toAccountWrapper');

        if (this.value === 'transfer') {
            toAccount.classList.remove('d-none');
            toAccount.querySelector('select').setAttribute('required', true);
        } else {
            toAccount.classList.add('d-none');
            toAccount.querySelector('select').removeAttribute('required');
        }
    });
</script>
<?php
    initializePageFooter($rootPath, $moduleType);
?>