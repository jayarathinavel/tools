<?php
    function findBookExpenseBalance($userId){
        $selectedBook = null;
        if(isset($_SESSION['expenseBalanceSelectedBook'])){
            return $_SESSION['expenseBalanceSelectedBook'];
        }

        // Try persisted default from DB
        if(isset($_SESSION['appUserId'])){
            $dbBook = getUserDefaultBook('expense_balance');
            if($dbBook) {
                $_SESSION['expenseBalanceSelectedBook'] = $dbBook;
                return $dbBook;
            }
        }

        // Fallback to first available book
        $books = executeQuery("SELECT * FROM expense_balance_book WHERE user_id = $userId");
        if($books && $books->num_rows > 0){
            $selectedBook = $books->fetch_assoc()["id"];
            $_SESSION['expenseBalanceSelectedBook'] = $selectedBook;
        }
        return $selectedBook;
    }

    function clearSelectedBook(){
        if(isset($_SESSION['expenseBalanceSelectedBook'])) {
            unset($_SESSION['expenseBalanceSelectedBook']);
        }
        if(isset($_SESSION['appUserId'])){
            $conn = initDb();
            $stmt = $conn->prepare("DELETE FROM user_default_books WHERE app_user_id = ? AND tool = ?");
            if($stmt){
                $tool = 'expense_balance';
                $stmt->bind_param('is', $_SESSION['appUserId'], $tool);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    function fetchPersonsFromExpenseBalanceBook($book){
        $expenseBookDetails = executeQuery("SELECT * FROM expense_balance_book WHERE id=$book");
        $persons = $expenseBookDetails->fetch_assoc()["persons"];
        return array_map('trim', explode("," , $persons));
    }

    function expenseBalanceUser(){
        return $_SESSION['appUserId'];
    }

    function fetchBooks($userId) {
        $query = "SELECT id, name FROM expense_balance_book WHERE user_id=$userId";
        $result = executeQuery($query);
        if ($result === false) {
            return [];
        }
        $books = [];
        while ($row = $result->fetch_assoc()) {
            $books[$row['id']] = $row['name'];
        }
        return $books;
    }