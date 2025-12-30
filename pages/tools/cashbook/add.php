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
<style>
    .scroll-x {
        overflow-x: auto;
        overflow-y: hidden;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 0.25rem;

        /* Hide scrollbar */
        scrollbar-width: none;       /* Firefox */
        -ms-overflow-style: none;    /* Edge / IE */
    }

    .scroll-x::-webkit-scrollbar {
        display: none;               /* Chrome / Safari / Edge */
    }

    .scroll-x .btn-group {
        display: inline-flex;
        flex-wrap: nowrap;
    }

    .scroll-x .btn {
        flex: 0 0 auto;
        white-space: nowrap;
        margin-right: 0.25rem;
    }

    /* Select placeholder (first option only) */
    .form-control:has(option[value=""]:checked) {
        color: #6c757d;
    }

</style>
<div class="container">
    <?php getSuccessOrFailureMessage(); ?>
    <?php if(!$book) echo '<div class="alert alert-danger">No book. Create one first.</div>'; ?>
    <form method="post" action="">
        <div class="form-group mb-2">
            <input required name="title" class="form-control" placeholder="Expense Name">
        </div>
        <div class="form-group mb-2">
            <select name="category_id" class="form-control">
                <option value="" hidden>Select a category</option>
                <?php foreach($categories as $id => $name): ?>
                    <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group mb-2">
            <input required name="amount" type="number" step="0.01" class="form-control" placeholder="Amount">
        </div>
        <div class="form-group mb-2">
            <div class="scroll-x">
                <div class="btn-group" role="group" id="typeSelector">
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

                        foreach ($types as $value => $label):
                            $active = ($selectedType === $value) ? 'active btn-primary' : 'btn-outline-primary';
                    ?>
                        <button
                            type="button"
                            class="btn btn-sm <?= $active ?>"
                            data-value="<?= $value ?>"
                        >
                            <?= $label ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
            <input type="hidden" name="type" id="typeInput" value="<?= $selectedType ?>" required>
        </div>
        <div class="form-group mb-2">
            <div class="scroll-x">
                <div class="btn-group" role="group" id="accountSelector">
                    <?php
                        $selectedAccount = $prefill['bank_account_id'] ?? null;
                        foreach ($accounts as $a):
                            $active = ($selectedAccount == $a['id']) ? 'active btn-success' : 'btn-outline-success';
                    ?>
                        <button
                            type="button"
                            class="btn btn-sm <?= $active ?>"
                            data-id="<?= $a['id'] ?>"
                        >
                            <?= htmlspecialchars($a['name']) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <input type="hidden" name="bank_account_id" id="accountInput" value="<?= htmlspecialchars($selectedAccount) ?>" required>
        </div>
        <div class="form-group mb-2 d-none" id="toAccountWrapper">
            <select name="to_account_id" class="form-control">
                <option hidden value="">Select Receiving account</option>
                <?php foreach($accounts as $a): ?>
                    <option value="<?= $a['id']; ?>"><?= $a['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group mb-2">
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
    document.addEventListener('DOMContentLoaded', () => {
        sortButtons(
            document.getElementById('typeSelector'),
            'cashbook_type_usage',
            'value'
        );

        sortButtons(
            document.getElementById('accountSelector'),
            'cashbook_account_usage',
            'id'
        );
    });
    document.querySelectorAll('#accountSelector button').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            saveUsage('cashbook_account_usage', id);

            document.querySelectorAll('#accountSelector button').forEach(b => {
                b.classList.remove('active','btn-success');
                b.classList.add('btn-outline-success');
            });

            btn.classList.add('active','btn-success');
            btn.classList.remove('btn-outline-success');

            document.getElementById('accountInput').value = id;
        });
    });

    document.querySelectorAll('#typeSelector button').forEach(btn => {
        btn.addEventListener('click', () => {
            const value = btn.dataset.value;
            saveUsage('cashbook_type_usage', value);

            // Toggle active state
            document.querySelectorAll('#typeSelector button').forEach(b => {
                b.classList.remove('active', 'btn-primary');
                b.classList.add('btn-outline-primary');
            });

            btn.classList.add('active','btn-primary');
            btn.classList.remove('btn-outline-primary');

            document.getElementById('typeInput').value = value;

            // Transfer logic
            const toAccount = document.getElementById('toAccountWrapper');
            if (value === 'transfer') {
                toAccount.classList.remove('d-none');
                toAccount.querySelector('select').required = true;
            } else {
                toAccount.classList.add('d-none');
                toAccount.querySelector('select').required = false;
            }
        });
    });

    document.querySelector('form').addEventListener('submit', () => {
        const type = document.getElementById('typeInput').value;
        const account = document.getElementById('accountInput').value;

        if (type) saveUsage('cashbook_type_usage', type);
        if (account) saveUsage('cashbook_account_usage', account);
    });

    function getUsage(key) {
        return JSON.parse(localStorage.getItem(key) || '{}');
    }

    function saveUsage(key, id) {
        const usage = getUsage(key);
        const now = Date.now();

        if (!usage[id]) {
            usage[id] = { count: 0, last: 0 };
        }

        usage[id].count++;
        usage[id].last = now;

        localStorage.setItem(key, JSON.stringify(usage));
    }

    function sortButtons(container, usageKey, dataAttr) {
        const usage = getUsage(usageKey);
        const buttons = Array.from(container.children);

        buttons.sort((a, b) => {
            const aKey = a.dataset[dataAttr];
            const bKey = b.dataset[dataAttr];

            const aUsage = usage[aKey] || { count: 0, last: 0 };
            const bUsage = usage[bKey] || { count: 0, last: 0 };

            // Most used first
            if (bUsage.count !== aUsage.count) {
                return bUsage.count - aUsage.count;
            }

            // Most recently used next
            return bUsage.last - aUsage.last;
        });

        buttons.forEach(btn => container.appendChild(btn));
    }

</script>
<?php
    initializePageFooter($rootPath, $moduleType);
?>