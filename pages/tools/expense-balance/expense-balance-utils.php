<?php
    function findBookExpenseBalance($userId){
        $selectedBook = null;
        if(isset($_SESSION['expenseBalanceSelectedBook'])){
            $selectedBook = $_SESSION['expenseBalanceSelectedBook'];
        } else{
            $books = executeQuery("SELECT * FROM expense_balance_book WHERE user_id = $userId");
            if($books->num_rows > 0){
                $selectedBook = $books->fetch_assoc()["id"];
                $_SESSION['expenseBalanceSelectedBook'] = $selectedBook;
            }
        }
        return $selectedBook;
    }

    function clearSelectedBook(){
        if(isset($_SESSION['expenseBalanceSelectedBook'])) {
            unset($_SESSION['expenseBalanceSelectedBook']);
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