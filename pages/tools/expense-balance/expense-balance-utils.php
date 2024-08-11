<?php
    function findBookExpenseBalance($userId){
        $selectedBook = null;
        if(isset($_SESSION['expenseBalanceSelectedBook'])){
            $selectedBook = $_SESSION['expenseBalanceSelectedBook'];
        } else{
            $conn = initDb();
            $books = $conn->query("SELECT * FROM expense_balance_book WHERE user_id = $userId");
            $conn->close();
            if($books->num_rows > 0){
                $selectedBook = $books->fetch_assoc()["id"];
                $_SESSION['expenseBalanceSelectedBook'] = $selectedBook;
            }
        }
        return $selectedBook;
    }

    function fetchPersonsFromExpenseBalanceBook($book){
        $conn = initDb();
        $expenseBookDetails = $conn->query("SELECT * FROM expense_balance_book WHERE id=$book");
        $persons = $expenseBookDetails->fetch_assoc()["persons"];
        return array_map('trim', explode("," , $persons));
    }

    function expenseBalanceUser(){
        return $_SESSION['appUserId'];
    }

    function fetchBooks($userId) {
        $conn = initDb();
        $query = "SELECT id, name FROM expense_balance_book WHERE user_id=$userId";
        $result = $conn->query($query);
        if ($result === false) {
            return [];
        }
        $books = [];
        while ($row = $result->fetch_assoc()) {
            $books[$row['id']] = $row['name'];
        }
        return $books;
    }