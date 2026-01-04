<?php
    function findNotebook($userId){
        $selectedNotebook = null;
        if(isset($_SESSION['notebookSelected'])){
            return $_SESSION['notebookSelected'];
        }

        if(isset($_SESSION['appUserId'])){
            $dbNotebook = getUserDefaultBook('notebook');
            if($dbNotebook){
                $_SESSION['notebookSelected'] = $dbNotebook;
                return $dbNotebook;
            }
        }

        $notebooks = executeQuery("SELECT * FROM notebook WHERE user_id = $userId");
        if($notebooks && $notebooks->num_rows > 0){
            $selectedNotebook = $notebooks->fetch_assoc()["id"];
            $_SESSION['notebookSelected'] = $selectedNotebook;
        }
        return $selectedNotebook;
    }

    function clearSelectedNotebook(){
        if(isset($_SESSION['notebookSelected'])) {
            unset($_SESSION['notebookSelected']);
        }
        if(isset($_SESSION['appUserId'])){
            $conn = initDb();
            $stmt = $conn->prepare("DELETE FROM user_default_books WHERE app_user_id = ? AND tool = ?");
            if($stmt){
                $tool = 'notebook';
                $stmt->bind_param('is', $_SESSION['appUserId'], $tool);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    function notebookUser(){
        return $_SESSION['appUserId'];
    }

    function fetchNotebooks($userId) {
        $query = "SELECT id, name FROM notebook WHERE user_id=$userId";
        $result = executeQuery($query);
        if ($result === false) {
            return [];
        }
        $notebooks = [];
        while ($row = $result->fetch_assoc()) {
            $notebooks[$row['id']] = $row['name'];
        }
        return $notebooks;
    }
?>
