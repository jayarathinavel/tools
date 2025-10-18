<?php
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    require_once $rootPath . '/config/config.php';
    includePhpFileFromRoot($rootPath, '/pages/tools/notebook/notebook-utils.php');
    $userId = notebookUser();
    
    if (isset($_POST['id'])) {
        // Edit page
        try {
            $id = $_POST['id'];
            $page_name = $_POST['page_name'];
            $markdown_content = $_POST['markdown_content'];
            executeQuery("UPDATE notebook_page SET page_name='$page_name', markdown_content='$markdown_content' WHERE id=$id");
            setSuccessOrFailureMessage('success', 'Edited Successfully');
        } catch (Exception $e) {
            setSuccessOrFailureMessage('failure', 'Failed to Edit!' . ' ' . $e->getMessage());
        }
    } else {
        // Add new page
        try {
            $page_name = $_POST['page_name'];
            $markdown_content = $_POST['markdown_content'];
            $notebook = findNotebook($userId);
            executeQuery("INSERT INTO notebook_page (page_name, markdown_content, notebook_id)
                VALUES ('$page_name', '$markdown_content', '$notebook')");
            setSuccessOrFailureMessage('success', 'Added Successfully');
        } catch (Exception $e) {
            setSuccessOrFailureMessage('failure', 'Failed to Add!' . ' ' . $e->getMessage());
        }
    }
    Database::getInstance()->closeConnection();
    header("Location: view.php");
    exit;
?>
