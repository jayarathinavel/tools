<?php
    require_once 'constants.php';
    require_once 'database.php';
    require_once 'util.php';
    date_default_timezone_set('Asia/Kolkata');

    class Logger {
        private $logFile;
        private $logLevel;
    
        const LEVEL_ALL = 1;
        const LEVEL_ERROR_ONLY = 2;

        const INFO = 'INFO';
        const ERROR = 'ERROR';
        const DEBUG = 'DEBUG';
        const WARNING = 'WARNING';
    
        public function __construct($logFile, $logLevel = self::LEVEL_ALL) {
            $this->logFile = $logFile;
            $this->logLevel = $logLevel;
        }
    
        public function setLogLevel($logLevel) {
            $this->logLevel = $logLevel;
        }
    
        public function log($type, $message) {
            if (!$this->shouldLog($type)) {
                return;
            }
            $time = $this->getCurrentTimestamp();
            $logEntry = sprintf("[%s] [%s]: %s" . PHP_EOL, $time, strtoupper($type), $message);
            file_put_contents($this->logFile, $logEntry, FILE_APPEND);
        }
    
        private function getCurrentTimestamp() {
            $microtime = microtime(true);
            $milliseconds = sprintf("%03d", ($microtime - floor($microtime)) * 1000);
            return date("Y-m-d H:i:s") . ".$milliseconds";
        }
    
        private function shouldLog($type) {
            if ($this->logLevel === self::LEVEL_ERROR_ONLY && strtoupper($type) !== 'ERROR') {
                return false;
            }
            return true;
        }
    }

    function writeLog($type, $message){
        $logger = new Logger($_SERVER['DOCUMENT_ROOT'] . '/logs.log');
        /* Logging Levels: LEVEL_ERROR_ONLY and LEVEL_ALL */
        $logger->setLogLevel(Logger::LEVEL_ALL);
        $logger->log($type, $message);
    }
