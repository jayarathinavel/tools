<?php
    function findNotebook($userId){
        $selectedNotebook = null;
        if(isset($_SESSION['notebookSelected'])){
            $selectedNotebook = $_SESSION['notebookSelected'];
        } else{
            $notebooks = executeQuery("SELECT * FROM notebook WHERE user_id = $userId");
            if($notebooks->num_rows > 0){
                $selectedNotebook = $notebooks->fetch_assoc()["id"];
                $_SESSION['notebookSelected'] = $selectedNotebook;
            }
        }
        return $selectedNotebook;
    }

    function clearSelectedNotebook(){
        if(isset($_SESSION['notebookSelected'])) {
            unset($_SESSION['notebookSelected']);
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
