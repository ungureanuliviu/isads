<?php
    include_once "Console.php";
    
    class ErrorHandler {
    function __construct() {
        // nothing here
    }
    
    public static function handle($data){
        Console::debug("\n\n" . print_r($data, true) . "\n----------------------------------------\n\n");
    }       
}
?>