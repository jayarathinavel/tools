<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    includePhpFileFromRoot($rootPath, '/pages/tools/events-anniversary/events-anniversary-utils.php');

    try {
        if (isset($_GET['operation']) && $_GET['operation'] === 'delete') {
            $id = intval($_GET['id']);
            executeQuery("DELETE FROM events_anniversary_celebration WHERE event_id=$id");
            executeQuery("DELETE FROM events_anniversary WHERE id=$id");
            setSuccessOrFailureMessage('success', 'Deleted Successfully');
        }
        if (isset($_GET['operation']) && $_GET['operation'] === 'delete-celebration') {
            $id = intval($_GET['id']);
            executeQuery("DELETE FROM events_anniversary_celebration WHERE id=$id");
            setSuccessOrFailureMessage('success', 'Deleted Successfully');
            header(header: "Location: event.php?id=" . $_GET['event_id']);
            exit;
        }
    } catch (Exception $e) {
        setSuccessOrFailureMessage('failure', 'Failed to Delete! ' . $e->getMessage());
    }
    header(header: "Location: view.php");
    exit;
