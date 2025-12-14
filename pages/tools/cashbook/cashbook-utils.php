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

    function canEditBook($id) {
        $userId = executeQuery("SELECT user_id FROM cashbook_book WHERE id=$id")->fetch_assoc()['user_id'];
        if($userId != $_SESSION['appUserId']) {
            alert("You do not have permission to edit this book.");
            exit;
        }
    }

    function canEditCategory($id) {
        $userId = executeQuery("SELECT user_id FROM cashbook_book WHERE id=(SELECT cashbook_book_id FROM cashbook_category WHERE id=$id)")->fetch_assoc()['user_id'];
        if($userId != $_SESSION['appUserId'] && $userId != 0) {
            alert("You do not have permission to edit this category.");
            exit;
        }
    }

    function canEditAccount($id) {
        $userId = executeQuery("SELECT user_id FROM cashbook_book WHERE id=(SELECT cashbook_book_id FROM cashbook_bank_account WHERE id=$id)")->fetch_assoc()['user_id'];
        if($userId != $_SESSION['appUserId']) {
            alert("You do not have permission to edit this account.");
            exit;
        }
    }

    function canEditTransaction($id) {
        $userId = executeQuery("SELECT user_id FROM cashbook_book WHERE id=(SELECT book_id FROM cashbook_transaction WHERE id=$id)")->fetch_assoc()['user_id'];
        if($userId != $_SESSION['appUserId']) {
            alert("You do not have permission to edit this transaction.");
            exit;
        }
    }

    function canViewBook($id) {
        $userId = executeQuery("SELECT user_id FROM cashbook_book WHERE id=$id")->fetch_assoc()['user_id'];
        if($userId != $_SESSION['appUserId']) {
            alert("You do not have permission to view this book.");
            exit;
        }
    }
