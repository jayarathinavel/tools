<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Add Bill", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/bill-split-tracker/bill-split-utils.php');
    $userId = billSplitUser();
    $book = findBookBillSplit($userId)
?>

<div class="container">
    <h1>Add Bill</h1>
    <?php
        if (isset($_POST['bill_name'])) {
            includePhpFileFromRoot($rootPath, '/handlers/tools/bill-split-handler.php');
        }
        if(isset($book)) {
            $persons = fetchPersonsFromBillSplitBook($book);
            $books = fetchBillSplitBooks($userId);
        } else {
            echo '<div class="alert alert-danger mb-3" role="alert">No Book is available, create a book first!</div>';
        }
    ?>
    
    <form action="" method="post" id="billForm">
        <?php if(isset($book)): ?>
        <div class="fw-bold mb-2">
            <?php
                foreach ($books as $id => $name):
                    if($id == intval($book)) {
                        echo 'Add New Bill to ' . $name;
                    }
                endforeach;
            ?>
        </div>
        
        <div class="form-group">
            <label for="bill_name">Bill Name:</label>
            <input type="text" id="bill_name" name="bill_name" class="form-control" placeholder="e.g., Dinner at Restaurant" required>
        </div>
        
        <div class="form-group">
            <label for="total_amount">Total Amount (₹):</label>
            <input type="number" step="0.01" id="total_amount" name="total_amount" class="form-control" placeholder="0.00" required>
        </div>
        
        <div class="form-group">
            <label for="paid_by">Paid By:</label>
            <select id="paid_by" name="paid_by" class="form-control" required>
                <option value="">Select who paid</option>
                <?php foreach ($persons as $person): ?>
                    <option value="<?php echo htmlspecialchars($person); ?>"><?php echo htmlspecialchars($person); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label>Split Type:</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="split_type" id="equal_split" value="equal" checked onchange="toggleSplitType()">
                <label class="form-check-label" for="equal_split">
                    Equal Split
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="split_type" id="custom_split" value="custom" onchange="toggleSplitType()">
                <label class="form-check-label" for="custom_split">
                    Custom Split
                </label>
            </div>
        </div>
        
        <div id="equal_preview" class="form-group" style="display: none;">
            <label>Equal Split Preview:</label>
            <div id="equal_split_preview" class="bg-light p-3 rounded">
                <!-- Equal split preview will be shown here -->
            </div>
        </div>
        
        <div id="custom_splits" class="form-group" style="display: none;">
            <label>Custom Split Amounts:</label>
            <?php foreach ($persons as $index => $person): ?>
            <div class="row mb-2">
                <div class="col-md-3">
                    <label for="split_<?php echo $index; ?>"><?php echo htmlspecialchars($person); ?>:</label>
                </div>
                <div class="col-md-9">
                    <input type="number" step="0.01" id="split_<?php echo $index; ?>" name="splits[<?php echo htmlspecialchars($person); ?>]" class="form-control custom-split-input" placeholder="0.00" onchange="updateCustomTotal()">
                </div>
            </div>
            <?php endforeach; ?>
            <div class="mt-2">
                <small class="text-muted">Total: ₹<span id="custom_total">0.00</span> / ₹<span id="bill_total">0.00</span></small>
                <div id="split_validation" class="text-danger" style="display: none;"></div>
            </div>
        </div>
        
        <input type="hidden" id="splits_json" name="splits_json">
        
        <input type="submit" value="Add Bill" class="btn btn-primary mt-2" id="submit_btn">
        <a href="view.php" class="btn btn-secondary mt-2">Cancel</a>
        <?php endif; ?>
    </form>
</div>

<script>
    // Set today's date as default
    document.getElementById('date').value = new Date().toISOString().split('T')[0];
    
    function toggleSplitType() {
        const equalSplit = document.getElementById('equal_split').checked;
        const equalPreview = document.getElementById('equal_preview');
        const customSplits = document.getElementById('custom_splits');
        
        if (equalSplit) {
            equalPreview.style.display = 'block';
            customSplits.style.display = 'none';
            updateEqualSplitPreview();
        } else {
            equalPreview.style.display = 'none';
            customSplits.style.display = 'block';
            updateCustomTotal();
        }
    }
    
    function updateEqualSplitPreview() {
        const totalAmount = parseFloat(document.getElementById('total_amount').value) || 0;
        const persons = <?php echo json_encode($persons ?? []); ?>;
        const splitAmount = totalAmount / persons.length;
        
        let preview = '';
        persons.forEach(person => {
            preview += `<div class="d-flex justify-content-between"><span>${person}:</span><span>₹${splitAmount.toFixed(2)}</span></div>`;
        });
        
        document.getElementById('equal_split_preview').innerHTML = preview;
        document.getElementById('bill_total').textContent = totalAmount.toFixed(2);
    }
    
    function updateCustomTotal() {
        const customInputs = document.querySelectorAll('.custom-split-input');
        let total = 0;
        const splits = {};
        
        customInputs.forEach(input => {
            const amount = parseFloat(input.value) || 0;
            total += amount;
            const person = input.name.match(/\[(.*?)\]/)[1];
            splits[person] = amount;
        });
        
        document.getElementById('custom_total').textContent = total.toFixed(2);
        document.getElementById('splits_json').value = JSON.stringify(splits);
        
        const billTotal = parseFloat(document.getElementById('total_amount').value) || 0;
        document.getElementById('bill_total').textContent = billTotal.toFixed(2);
        
        const validation = document.getElementById('split_validation');
        const submitBtn = document.getElementById('submit_btn');
        
        if (Math.abs(total - billTotal) > 0.01 && billTotal > 0) {
            validation.textContent = 'Split amounts must equal the total bill amount';
            validation.style.display = 'block';
            submitBtn.disabled = true;
        } else {
            validation.style.display = 'none';
            submitBtn.disabled = false;
        }
    }
    
    // Event listeners
    document.getElementById('total_amount').addEventListener('input', function() {
        if (document.getElementById('equal_split').checked) {
            updateEqualSplitPreview();
        } else {
            updateCustomTotal();
        }
    });
    
    document.getElementById('billForm').addEventListener('submit', function(e) {
        const splitType = document.querySelector('input[name="split_type"]:checked').value;
        
        if (splitType === 'equal') {
            const totalAmount = parseFloat(document.getElementById('total_amount').value) || 0;
            const persons = <?php echo json_encode($persons ?? []); ?>;
            const splitAmount = totalAmount / persons.length;
            const splits = {};
            
            persons.forEach(person => {
                splits[person] = splitAmount;
            });
            
            document.getElementById('splits_json').value = JSON.stringify(splits);
        }
    });
    
    // Initialize
    toggleSplitType();
</script>

<?php
    initializePageFooter($rootPath, $moduleType);
?>
