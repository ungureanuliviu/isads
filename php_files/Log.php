<?php
/**
 * Logging class:
 * - contains lfile, lopen and lwrite methods
 * - lfile sets path and name of log file
 * - lwrite will write message to the log file
 * - first call of the lwrite will open log file implicitly
 * - message is written with the following format: hh:mm:ss (script name) message
 */
class Log{
    // define default log file
    private $log_file = '/home/admin/domains/iasianunta.info/public_html/logs/api.log';
    // define file pointer
    private $fp = null;
    // set log file (path and name)
    
    function  __construct(){
        date_default_timezone_set("Europe/Bucharest");
    }
    public function lfile($path) {
        $this->log_file = $path;
    }
    // write message to the log file
    public function lwrite($user_message){
        // if file pointer doesn't exist, then open log file
        $message = "IP: " . $_SERVER['REMOTE_ADDR'] . " > " . $user_message;
        if (!$this->fp) $this->lopen();
        // define script name
        $script_name = ($_SERVER['PHP_SELF']);
        // define current time
        $time = date('Y-m-d H:i:s');
        // write current time, script name and message to the log file
        fwrite($this->fp, "$time ($script_name) $message\n");
    }
    // open log file
    private function lopen(){
        // define log file path and name
        $lfile = $this->log_file;
        // define the current date (it will be appended to the log file name)
        $today = date('Y-m-d');
        // open log file for writing only; place the file pointer at the end of the file
        // if the file does not exist, attempt to create it
        $this->fp = fopen($lfile, 'a') or exit("Can't open $lfile!");
    }
}
?>