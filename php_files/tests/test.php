<?php    
    
    include_once "/home/admin/domains/iasianunta.info/public_html/php_files/DBManager.php";
    include_once '/home/admin/domains/iasianunta.info/public_html/php_files/Console.php';
    include_once '/home/admin/domains/iasianunta.info/public_html/php_files/ErrorHandler.php';
    include_once '/home/admin/domains/iasianunta.info/public_html/php_files/User.php';
    include_once '/home/admin/domains/iasianunta.info/public_html/php_files/Comment.php';
    $dbMan = new DBManager();
    
    //print_r($dbMan->getCategories());
    //$user = new User("Ungureanu Liviu", "liviu2", "test", "smartliviu@gmail.com");
    //print_r($dbMan->createUser($user));
    //print_r($dbMan->updateCommentRating(4, -1));
    //print_r($dbMan->getAllCommentsForUser(1));
    //print_r($dbMan->getTotalPages(99));
//    print_r($dbMan->updateViews(136, 1));
//    return;
//    for($id = 11; $i < 20; $i++){
//        $comment = new Comment();
//        $comment->setTitle("Comment title " . $i);
//        $comment->setContent("Comment content " . $i);
//        $comment->setAdId(1);
//        $comment->setOwnerUserId(1);
//        $comment->setDate(time());    
//        print_r($dbMan->addComment($comment));
//        print_r($dbMan->removeComment(3, 1));
//    }
    
    $submit_url = "http://iasianunta.info/API/session/login/"; 

    $curl = curl_init();    
    
    $params = array("client" => "android", "user_name" => "liviu2", "user_password" => "test");
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC ) ; 
    curl_setopt($curl, CURLOPT_USERPWD, "liviu2:test");     
    curl_setopt($curl, CURLOPT_HEADER, true); 
    curl_setopt($curl, CURLOPT_POST, true); 
    curl_setopt($curl, CURLOPT_POSTFIELDS, $params ); 
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)"); 
    curl_setopt($curl, CURLOPT_URL, $submit_url); 

    $result = split("application/json", curl_exec($curl) ); 
    
    print_r($result[1]); 
    curl_close($curl);     
?>
