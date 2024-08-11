<?php
$rootPath = $_SERVER['DOCUMENT_ROOT'];
require_once $rootPath . '/config/config.php';
includePhpFileFromRoot($rootPath, '/pages/tools/vehicle-tracker/vehicle-tracker-utils.php');
$conn = initDb();

try {
    if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
        $id = $_GET['id'];
        $id = $conn->real_escape_string($id);
        $query = "DELETE FROM vehicles WHERE id='$id'";
        if ($conn->query($query)) {
            setSuccessOrFailureMessage('success', 'Deleted Successfully');
        } else {
            throw new Exception('Error executing query: ' . $conn->error);
        }
    }
} catch (Exception $e) {
    setSuccessOrFailureMessage('failure', 'Failed to Delete! ' . $e->getMessage());
}

header("Location: view.php");
exit;
