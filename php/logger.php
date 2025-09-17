<?php
    function writeLog($action, $user = "sistema") {
        $logDir = __DIR__ . "/logs"; 
        if (!file_exists($logDir)) {
            mkdir($logDir, 0777, true);
        }
        $logFile = $logDir . "/system_" . date("Y-m-d") . ".log";
        $date = date("Y-m-d H:i:s");
        $ip   = $_SERVER['REMOTE_ADDR'] ?? "CLI";
        $msg  = "[$date] [Usuario: $user] [IP: $ip] - $action" . PHP_EOL;
        file_put_contents($logFile, $msg, FILE_APPEND | LOCK_EX);
    }
?>