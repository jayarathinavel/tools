<?php
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    require_once $rootPath. '/pages/includes/PHPMailer/PHPMailer.php';
    require_once $rootPath. '/pages/includes/PHPMailer/SMTP.php';
    require_once $rootPath. '/pages/includes/PHPMailer/Exception.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require_once 'constants.php';
    function initDb() {
        $servername = DB_SERVERNAME;
        $username = DB_USERNAME;
        $password = DB_PASSWORD;
        $dbname = DB_NAME;

        $conn = mysqli_connect($servername, $username, $password, $dbname);

        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
        return $conn;
    }

    function isLoggedIn() {
        session_start();
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            header("location: /pages/auth");
            exit;
        }
    }

    function htmlDecode($data){
        return html_entity_decode($data, ENT_QUOTES, 'UTF-8');
    }

    function htmlEncode($data){
        return htmlentities($data, ENT_QUOTES, 'UTF-8');
    }

    function fetchThemeValue($conn){
        $themeValue = '';
        $sql = "SELECT `value` FROM variables WHERE `key` = 'theme'";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $themeValue = $row['value'];
        }
        return $themeValue;
    }

    function checkSMTPStatus($host, $port, $username, $password) {
        $status = false;
        try {
            $smtpTest = new PHPMailer();
            $smtpTest->isSMTP();
            $smtpTest->Host = $host;
            $smtpTest->Port = $port;
            $smtpTest->SMTPAuth = true;
            $smtpTest->Username = $username;
            $smtpTest->Password = $password;
            $smtpTest->SMTPSecure = 'tls'; // Use 'ssl' or 'tls' as needed
            $smtpTest->Timeout = 5; // Set a timeout for the connection
            $smtpTest->SMTPDebug = 0; // Set to 2 for debugging
    
            if ($smtpTest->smtpConnect()) {
                $status = true;
                $smtpTest->smtpClose();
            }
        } catch (Exception $e) {
            throw new Exception($e -> getMessage());
        }
        return $status;
    }
    function checkCertificateValidity($url) {
        $context = stream_context_create(['ssl' => ['capture_peer_cert' => true]]);
        $stream = stream_socket_client("ssl://$url:443", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context);
        if ($stream) {
            $params = stream_context_get_params($stream);
            $certificate = openssl_x509_parse($params['options']['ssl']['peer_certificate']);
    
            if ($certificate) {
                $validFrom = date('Y-m-d', $certificate['validFrom_time_t']);
                $validTo = date('Y-m-d', $certificate['validTo_time_t']);
                $currentDate = date('Y-m-d');
    
                $isValid = ($currentDate >= $validFrom && $currentDate <= $validTo);
                return [
                    'subject' => $certificate['subject'],
                    'issuer' => $certificate['issuer'],
                    'valid_from' => $validFrom,
                    'valid_to' => $validTo,
                    'is_valid' => $isValid,
                ];
            }
        }
        return null;
    }

    function printAssociativeArray($array) {
        foreach ($array as $key => $value) {
            echo "<br/>$key: $value";
        }
    }
    
    function initDbPdo() {
        $servername = DB_SERVERNAME;
        $username = DB_USERNAME;
        $password = DB_PASSWORD;
        $dbname = DB_NAME;
        try {
            $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    function sessionStart(){
        session_start();
    }
    function successAndFailureMessage($status, $message) {
        $_SESSION['status'] = $status;
        $_SESSION['message'] = $message;
    }

    function setTodaysDateForForm(){
        echo "
            <script>
                window.onload = function() {
                    var today = new Date().toISOString().split('T')[0];
                    document.getElementById('date').value = today;
                };
            </script>
        ";
    }

    function expenseBalanceFindBook($userId){
        $selectedBook = null;
        if(isset($_SESSION['expenseBalanceSelectedBook'])){
            $selectedBook = $_SESSION['expenseBalanceSelectedBook'];
        } else{
            $conn = initDb();
            $books = $conn->query("SELECT * FROM expense_balance_book WHERE user_id = $userId");
            $conn->close();
            $selectedBook = $books->fetch_assoc()["id"];
            $_SESSION['expenseBalanceSelectedBook'] = $selectedBook;
        }
        return $selectedBook;
    }