<?php
    include_once "/home/admin/domains/iasianunta.info/public_html/php_files/DBManager.php";    
    include_once '/home/admin/domains/iasianunta.info/public_html/php_files/ErrorHandler.php';
        
    echo("activate . " . $_GET['activate_code']);
    $dbMan = new DBManager();
    
    print_r($dbMan->activateUser($_GET['activate_code']));
?>