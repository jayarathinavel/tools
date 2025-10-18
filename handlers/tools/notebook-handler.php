<?php
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    require_once $rootPath . '/config/config.php';
    includePhpFileFromRoot($rootPath, '/pages/tools/notebook/notebook-utils.php');
    $userId = notebookUser();

    if (isset($_POST['id'])) {
        // Edit notebook
        try {
            $id = $_POST['id'];
            $name = $_POST['name'];
            executeQuery("UPDATE notebook SET name='$name' WHERE id=$id");
            setSuccessOrFailureMessage('success', 'Edited Successfully');
        } catch (Exception $e) {
            setSuccessOrFailureMessage('failure', 'Failed to Edit!' . ' ' . $e->getMessage());
        }
    } else {
        // Add new notebook
        try {
            $name = $_POST['name'];
            executeQuery("INSERT INTO notebook (name, user_id) VALUES ('$name', '$userId')");
            setSuccessOrFailureMessage('success', 'Added Successfully');
            clearSelectedNotebook();
        } catch (Exception $e) {
            setSuccessOrFailureMessage('failure', 'Failed to Add!' . ' ' . $e->getMessage());
        }
    }

    Database::getInstance()->closeConnection();
    header("Location: view.php");
    exit;
?>
