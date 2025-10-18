<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Add Page", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/notebook/notebook-utils.php');
    $userId = notebookUser();
    $notebook = findNotebook($userId)
?>
<div class="container">
    <h1>Add Page</h1>
    <?php
        if (isset($_POST['page_name'])) {
            includePhpFileFromRoot($rootPath, '/handlers/tools/notebook-page-handler.php');
        }
        if(isset($notebook)) {
            $notebooks = fetchNotebooks($userId);
        }
        else {
            echo '<div class="alert alert-danger mb-3" role="alert">No Notebook is available, create a notebook first!</div>';
        }
        
    ?>
    <form action="" method="post">
        <div class="fw-bold mb-2">
            <?php
                foreach ($notebooks as $id => $name):
                    if($id == intval($notebook)) {
                        echo 'Add New Page to ' . $name;
                    }
                endforeach;
            ?>
        </div>
        <div class="form-group">
            <label for="page_name">Page Name:</label>
            <input type="text" id="page_name" name="page_name" class="form-control" required>
        </div>
        <!-- <div class="form-group">
            <label for="markdown_content">Markdown Content:</label>
            <textarea id="markdown_content" name="markdown_content" class="form-control" rows="10" required></textarea>
        </div> -->

        <div class="form-group">
            <label for="markdown_content">Content:</label>
            <div class="mb-2">
                <button type="button" id="toggleEditor" class="btn btn-secondary btn-sm">Switch to Markdown Preview</button>
            </div>
            <textarea id="markdown_content" name="markdown_content" class="form-control" rows="10" required></textarea>
            <div id="markdown_preview" class="border p-2" style="display:none; min-height:200px;"></div>
        </div>
        
        <input type="submit" value="Add Page" class="btn btn-primary mt-2" <?php echo isset($notebook) ? ' ' : 'disabled' ?>>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
    const toggleBtn = document.getElementById('toggleEditor');
    const textarea = document.getElementById('markdown_content');
    const preview = document.getElementById('markdown_preview');

    let isMarkdown = false;

    toggleBtn.addEventListener('click', () => {
        if (isMarkdown) {
            // Switch to textarea
            preview.style.display = 'none';
            textarea.style.display = 'block';
            toggleBtn.textContent = 'Switch to Markdown Preview';
        } else {
            // Switch to Markdown preview
            preview.innerHTML = marked.parse(textarea.value);
            preview.style.display = 'block';
            textarea.style.display = 'none';
            toggleBtn.textContent = 'Switch to Textarea';
        }
        isMarkdown = !isMarkdown;
    });

    // Optional: live preview while typing
    textarea.addEventListener('input', () => {
        if (isMarkdown) {
            preview.innerHTML = marked.parse(textarea.value);
        }
    });
</script>


<?php
    initializePageFooter($rootPath, $moduleType);
?>