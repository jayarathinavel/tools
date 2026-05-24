<?php
    function findBookCashbook($userId){
        $selectedBook = null;
        // if book passed via GET, prefer it and persist
        if(isset($_GET['book_id'])) {
            $selectedBook = intval($_GET['book_id']);
            $_SESSION['cashbookSelectedBook'] = $selectedBook;
            if (isset($_SESSION['appUserId'])) {
                setUserDefaultBook('cashbook', $selectedBook);
            }
            return $selectedBook;
        }

        if(isset($_SESSION['cashbookSelectedBook'])){
            return $_SESSION['cashbookSelectedBook'];
        }

        // Try persisted default from DB
        if(isset($_SESSION['appUserId'])){
            $dbBook = getUserDefaultBook('cashbook');
            if($dbBook) {
                $_SESSION['cashbookSelectedBook'] = $dbBook;
                return $dbBook;
            }
        }

        // Fallback to first available book
        $books = executeQuery("SELECT * FROM cashbook_book WHERE user_id = $userId");
        if($books && $books->num_rows > 0){
            $selectedBook = $books->fetch_assoc()["id"];
            $_SESSION['cashbookSelectedBook'] = $selectedBook;
        }
        return $selectedBook;
    }
    function clearSelectedBookCashbook(){
        if(isset($_SESSION['cashbookSelectedBook'])) {
            unset($_SESSION['cashbookSelectedBook']);
        }
        if(isset($_SESSION['appUserId'])){
            $conn = initDb();
            $stmt = $conn->prepare("DELETE FROM user_default_books WHERE app_user_id = ? AND tool = ?");
            if($stmt){
                $tool = 'cashbook';
                $stmt->bind_param('is', $_SESSION['appUserId'], $tool);
                $stmt->execute();
                $stmt->close();
            }
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
        $userId = executeQuery("SELECT user_id FROM cashbook_book WHERE id=(SELECT cashbook_book_id FROM cashbook_entry WHERE id=$id)")->fetch_assoc()['user_id'];
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

    function findCashbookDetails($id) {
        $q = executeQuery("SELECT * FROM cashbook_book WHERE id=$id");
        return $q->fetch_assoc();
    }
