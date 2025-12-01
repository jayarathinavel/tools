<?php
    function findBookCashbook($userId){
        $selectedBook = null;
        if($_GET['book_id']) {
            $_SESSION['cashbookSelectedBook'] = intval(value: $_GET['book_id']);
        }
        if(isset($_SESSION['cashbookSelectedBook'])){
            $selectedBook = $_SESSION['cashbookSelectedBook'];
        } else{
            $books = executeQuery("SELECT * FROM cashbook_book WHERE user_id = $userId");
            if($books->num_rows > 0){
                $selectedBook = $books->fetch_assoc()["id"];
                $_SESSION['cashbookSelectedBook'] = $selectedBook;
            }
        }
        return $selectedBook;
    }
    function clearSelectedBookCashbook(){
        if(isset($_SESSION['cashbookSelectedBook'])) {
            unset($_SESSION['cashbookSelectedBook']);
        }
    }
    function fetchBankAccountsFromCashbook($book){
        $q = executeQuery("SELECT * FROM cashbook_bank_account WHERE cashbook_book_id=$book");
        $accounts = [];
        while($r = mysqli_fetch_assoc($q)) $accounts[$r['id']] = $r;
        return $accounts;
    }
    function fetchCategoriesFromCashbook($book){
        $q = executeQuery("SELECT * FROM cashbook_category WHERE cashbook_book_id=$book or cashbook_book_id = 0");
        $cats = [];
        while($r = mysqli_fetch_assoc($q)) $cats[$r['id']] = $r['name'];
        return $cats;
    }
    function cashbookUser(){
        return $_SESSION['appUserId'];
    }
    function fetchCashbookBooks($userId) {
        $q = executeQuery("SELECT * FROM cashbook_book WHERE user_id=$userId ORDER BY creation_timestamp DESC");
        $arr = [];
        while($r = mysqli_fetch_assoc($q)) $arr[$r['id']] = $r['name'];
        return $arr;
    }