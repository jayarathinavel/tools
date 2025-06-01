<?php
    function findBookBillSplit($userId){
        $selectedBook = null;
        if(isset($_SESSION['billSplitSelectedBook'])){
            $selectedBook = $_SESSION['billSplitSelectedBook'];
        } else{
            $books = executeQuery("SELECT * FROM bill_split_book WHERE user_id = $userId");
            if($books->num_rows > 0){
                $selectedBook = $books->fetch_assoc()["id"];
                $_SESSION['billSplitSelectedBook'] = $selectedBook;
            }
        }
        return $selectedBook;
    }

    function clearSelectedBookBillSplit(){
        if(isset($_SESSION['billSplitSelectedBook'])) {
            unset($_SESSION['billSplitSelectedBook']);
        }
    }

    function fetchPersonsFromBillSplitBook($book){
        $billBookDetails = executeQuery("SELECT * FROM bill_split_book WHERE id=$book");
        $persons = $billBookDetails->fetch_assoc()["persons"];
        return array_map('trim', explode("," , $persons));
    }

    function billSplitUser(){
        return $_SESSION['appUserId'];
    }

    function fetchBillSplitBooks($userId) {
        $query = "SELECT id, name FROM bill_split_book WHERE user_id=$userId";
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

    function calculateBillSplitSummary($bookId) {
        $book = executeQuery("SELECT * FROM bill_split_book WHERE id=$bookId")->fetch_assoc();
        $persons = fetchPersonsFromBillSplitBook($bookId);
        $bills = executeQuery("SELECT * FROM bill_split WHERE bill_split_book_id = $bookId");
        
        $summary = [];
        foreach ($persons as $person) {
            $summary[$person] = [
                'totalPaid' => 0,
                'totalOwes' => 0,
                'balance' => 0
            ];
        }
        
        while ($bill = $bills->fetch_assoc()) {
            // Add to total paid for the person who paid
            $summary[$bill['paid_by']]['totalPaid'] += $bill['total_amount'];
            
            // Calculate splits and add to total owes
            $splits = json_decode($bill['splits'], true);
            foreach ($splits as $person => $amount) {
                if (isset($summary[$person])) {
                    $summary[$person]['totalOwes'] += $amount;
                }
            }
        }
        
        // Calculate balance
        foreach ($summary as $person => &$data) {
            $data['balance'] = $data['totalPaid'] - $data['totalOwes'];
        }
        
        return $summary;
    }
?>
    