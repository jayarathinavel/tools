<?php
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    require_once $rootPath. '/pages/includes/PHPMailer/PHPMailer.php';
    require_once $rootPath. '/pages/includes/PHPMailer/SMTP.php';
    require_once $rootPath. '/pages/includes/PHPMailer/Exception.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    function includePhpFileFromRoot($rootPath, $path) {
        require_once $rootPath . $path;
    }

    function initDb() {
        return (Database::getInstance()->getConnection());
    }

    function executeQuery($sql) {
        $timer = new Timer('Database Query');
        $conn = initDb();
        // Allow only DML and DQL queries
        if (preg_match('/^\s*(CREATE|ALTER|DROP|TRUNCATE)\s+/i', $sql)) {
            die("SQL Error: DDL queries are not allowed.");
        }
    
        $result = $conn->query($sql);
    
        if ($result === false) {
            die("SQL Error: " . $conn->error);
        }
    
        if (preg_match('/^\s*(SELECT|SHOW|DESCRIBE|EXPLAIN)\s+/i', $sql)) {
            $timer->changeName("Select Query");
            $timer->stop();
            return $result;
        } else {
            $timer->changeName("Insert/Update/Detele Query");
            $timer->stop();
            return $conn->affected_rows > 0;
        }
    }

    function startSession(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    function isLoggedIn() {
        startSession();
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            header("location: /pages/auth");
            exit;
        }
    }

    function appUserLoginRequired($redirectTo) {
        startSession();
        if (!isset($_SESSION["appUserLoggedIn"]) || $_SESSION["appUserLoggedIn"] !== true) {
            $_SESSION['redirectTo'] = $redirectTo;
            header("location: /pages/auth/app-users/login.php");
            exit;
        }
    }

    function isAppUserLoggedIn() {
        startSession();
        $flag = false;
        if(isset($_SESSION["appUserLoggedIn"]) || $_SESSION["appUserLoggedIn"] === true){
            $flag = true;
        }
        return $flag;
    }

    function appUserLoginRequiredClose(){
        unset($_SESSION['redirectTo']);
    }

    function htmlDecode($data){
        return html_entity_decode($data, ENT_QUOTES, 'UTF-8');
    }

    function htmlEncode($data){
        return htmlentities($data, ENT_QUOTES, 'UTF-8');
    }

    function fetchThemeValue(){
        $themeValue = '';
        startSession();
        if(isset($_SESSION["theme"])){
            $themeValue = $_SESSION["theme"];
        } else{
            $sql = "SELECT `value` FROM variables WHERE `key` = 'theme'";
            $result = executeQuery($sql);
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $themeValue = $row['value'];
                setToSession("theme", $themeValue);
            }
        }
        return $themeValue;
    }

    function setToSession($sessionVariable, $value){
        startSession();
        $_SESSION[$sessionVariable] = $value;
    }

    function getFromSession($sessionVariable){
        startSession();
        return $_SESSION[$sessionVariable];
    }

    function printAssociativeArray($array) {
        foreach ($array as $key => $value) {
            echo "<br/>$key: $value";
        }
    }

    function setSuccessOrFailureMessage($status, $message) {
        startSession();
        $_SESSION['status'] = $status;
        $_SESSION['message'] = $message;
    }

    function getSuccessOrFailureMessage(){
        startSession();
        if (isset($_SESSION['status']) && isset($_SESSION['message'])) {
            $status = $_SESSION['status'];
            $message = $_SESSION['message'];
            
            $alertClass = 'alert-secondary';
            if ($status == 'success') {
                $alertClass = 'alert-success';
            } elseif ($status == 'failure') {
                $alertClass = 'alert-danger';
            } elseif ($status == 'warning') {
                $alertClass = 'alert-warning';
            }

            echo '
            <div class="alert ' . $alertClass . ' alert-dismissible fade show mb-3" role="alert">
                ' . $message . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';

            unset($_SESSION['status']);
            unset($_SESSION['message']);
        }
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

    function getNextDayDate($dateString) {
        // Assuming $dateString is in 'YYYY-MM-DD' format
        $date = new DateTime($dateString);
        $date->modify('+1 day');
        // Get the new date as a string in 'YYYY-MM-DD' format
        return $date->format('Y-m-d');
    }

    function formatDate($dateString) {
        $date = new DateTime($dateString);
        return $date->format('d-M-Y'); // Outputs: '08-Aug-2024'
    }

    function initializePage($pageTitleParam, $moduleTypeParam, $redirectToAfterAuth) {
        global $rootPath;
        $rootPath = $_SERVER['DOCUMENT_ROOT'];

        global $pageTitle;
        $pageTitle = $pageTitleParam;

        global $moduleType;
        $moduleType = $moduleTypeParam;

        if(isset($moduleType)) {
            require_once $rootPath . '/pages/includes/'.$moduleType.'-pages/header.php';
        }
        if(isset($redirectToAfterAuth)){
            appUserLoginRequired($redirectToAfterAuth);
        }
    }

    function initializePageFooter($rootPath, $moduleType) {
        require_once $rootPath . '/config/footer-config.php';
        if(isAppUserLoggedIn()){
            appUserLoginRequiredClose();
        }
        if(isset($moduleType)) {
            require_once $rootPath . '/pages/includes/'.$moduleType.'-pages/footer.php';
        }
    }

    class Timer {
        private $startTime;
        private $name;

        public function __construct($name) {
            $this->name = $name;
            $this->startTime = microtime(true);
        }

        public function changeName($name) {
            $this->name = $name;
        }

        public function stop() {
            if ($this->startTime) {
                $endTime = microtime(true);
                $executionTime = ($endTime - $this->startTime) * 1000;  // Calculate time in milliseconds
                writeLog(Logger::DEBUG, "$this->name took " . number_format($executionTime, 2) . " ms");

                $this->startTime = null;
            } else {
                writeLog(Logger::ERROR, "Timer for $this->name has already been stopped or not started");
            }
        }
    }