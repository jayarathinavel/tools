<?php
    class Database {
        private static $instance = null;
        private $conn;
    
        private function __construct() {
            $timer = new Timer('Database Connection');
            writeLog(Logger::DEBUG, "New Database Connection");
            $this->conn = mysqli_connect(DB_SERVERNAME, DB_USERNAME, DB_PASSWORD, DB_NAME);
            if (!$this->conn) {
                die("Connection failed: " . mysqli_connect_error());
            }
            $timer->stop();
        }
    
        /* Usage: Database::getInstance(); */
        public static function getInstance() {
            if (self::$instance == null) {
                self::$instance = new Database();
            }
            return self::$instance;  // Return the Database instance instead of the connection
        }

        /* Method to get the database connection */
        public function getConnection() {
            return $this->conn;
        }
    
        /* Usage: Database::getInstance()->closeConnection(); */
        public function closeConnection() {
            writeLog(Logger::DEBUG, "Closing Database Connection");
            if ($this->conn) {
                mysqli_close($this->conn);
                $this->conn = null;
            }
        }

        /* To close automatically when the script ends or when the object is destroyed
        public function __destruct() {
            $this->closeConnection();
        } */
        
    }
