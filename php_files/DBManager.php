<?php
    include_once "Ad.php";
    include_once "User.php";
    include_once 'Comment.php';
    include_once 'ErrorHandler.php';
    include_once "Log.php";
//     Duplicates check
//     SELECT t1.* FROM ads t1 INNER JOIN ( 
//     SELECT content,COUNT(*) FROM ads GROUP BY content HAVING COUNT(*)>1) as t2 
//     ON t1.content = t2.content \G        
    
    class DBManager {
        
        /*
                case 100: return "Imobiliare";
                case 40 : return "Vanzari";
                case 41 : return "Cumparari";
                case 46 : return "Diverse";
                case 53 : return "Schimburi";
                case 50 : return "Inchirieri";
                case 56 : return "Servicii";
                case 64 : return "Locuri de munca";
                case 82 : return "Meditatii";  
        
        */
        private $connection;
        private $categories;
        public static $CATEGORY_DEFAULT     = "default";
        public static $CATEGORY_IMOBILIARE  = "Imobiliare";
        public static $CATEGORY_VANZARI     = "Vanzari";
        public static $CATEGORY_CUMPARARI   = "Cumparari";
        public static $CATEGORY_DIVERSE     = "Diverse";
        public static $CATEGORY_SCHIMBURI   = "Schimburi";
        public static $CATEGORY_INCHIRIERI  = 'Inchirieri';
        public static $CATEGORY_SERVICII    = "Servicii";
        public static $CATEGORY_LOCURI_DE_MUNCA = "Locuri de munca";
        public static $CATEGORY_MEDITATII   = "Meditatii";
        private $log;
        private $mAdNotifier;


        // data
        private $CONFIG;
        function __construct() {       
            $this->CONFIG = array();

            $this->CONFIG['db']['username'] = "admin_liviu";
            $this->CONFIG['db']['password'] = "atxbvu_tyger1988";
            $this->CONFIG['db']['dbname']   = "admin_iasianuntadb";
            $this->CONFIG['db']['server']   = 'localhost';        
            $this->categories               = array();
            $this->log = new Log();       
            // connect to mysql server        
            $this->connection = mysql_connect($this->CONFIG['db']['server'], $this->CONFIG['db']['username'], $this->CONFIG['db']['password']);
            if(!$this->connection){
                die(ErrorHandler::handle(var_dump(debug_backtrace())));
            }         
            mysql_select_db($this->CONFIG['db']['dbname']);    
    }
    
    public function setAdNotifier($pAdNotifier){
        $this->log->lwrite("set notifier " . print_r($pAdNotifier, true));
        $this->mAdNotifier = $pAdNotifier;
        return $this;
    }


    public function getLastUpdateDate($pCatName, $pSource){
        
        $selectQuery  = "SELECT date FROM ads WHERE cat_id = (SELECT c.id FROM categories c where c.name='". mysql_real_escape_string($pCatName) . "' LIMIT 1) AND source='".mysql_real_escape_string($pSource) ."' order by date DESC LIMIT 1";
        $result       = mysql_query($selectQuery);
        Console::debug("\ngetLastUpdateDate query " . $selectQuery . "\n");
        if(mysql_num_rows($result) == 0){
            Console::debug("getLastUpdateDate " . $pCatName . " time: 0\n");
            return 0;
        }
        else {
            $row = mysql_fetch_assoc($result);            
            Console::debug("getLastUpdateDate " . $pCatName . " time: ". $row['date'] . "\n");
            return $row['date'];
        }
    }   


    public function addAd($pAd){        
        if(empty ($this->categories)){
            $this->categories = $this->getCategories();
        }
        $catId = 0;
        foreach ($this->categories['categories']  as $category) {            
            if(strcmp($category['name'], $pAd->getCategoryName()) == 0){
                echo("\ncategory details found: " . print_r($category, true) . "\n");
                $catId = $category['id'];
                $pAd->setCategoryId($catId);
            }
        }
        
        $insertQuery = "INSERT INTO ads(title, content, phone, email, address, cat_id, date, source, user_id) VALUES ".
                        "('". mysql_real_escape_string($pAd->getTitle()) ."','".mysql_real_escape_string($pAd->getContent())."','". mysql_real_escape_string($pAd->getPhone()) ."','" . mysql_real_escape_string($pAd->getEmail()) . "','" . mysql_real_escape_string($pAd->getAddress()) . "'," . $catId . "," . $pAd->getDate() .",'". mysql_real_escape_string($pAd->getSource()) ."',".$pAd->getUserId(). ")";        
        if(!mysql_query($insertQuery)){
            ErrorHandler::handle(mysql_error() . "\n Query with error " . $insertQuery . "\n");
        }
        
        if(isset ($this->mAdNotifier)){            
            $this->mAdNotifier->onAdAdded($pAd);
        } else{
            $this->log->lwrite("no ad notifier specified");
        }
        return $pAd;
    }        
    
    public function addAdWithParams($pTitle, $pContent, $pPrice, $pAddress, $pCategoryId, $pPhone, $pEmail, $pUserId, $pSource, $pImages, $pCurrency){                
        if(strlen($pCurrency) == 0)
            $pCurrency = "LEI";
        
        $insertQuery = "INSERT INTO ads(title, content, phone, email, address, cat_id, date, source, user_id, price, currency) VALUES ".
                        "('". mysql_real_escape_string($pTitle) ."','".mysql_real_escape_string($pContent)."','".
                        mysql_real_escape_string($pPhone) ."','" . mysql_real_escape_string($pEmail) . "','" .
                        mysql_real_escape_string($pAddress) . "'," . $pCategoryId. "," . time() .",'". 
                        mysql_real_escape_string($pSource) ."',".$pUserId. ",'" . mysql_real_escape_string($pPrice). "', '" .
                        mysql_real_escape_string($pCurrency) . "')";        
        
        $result     = mysql_query($insertQuery) or ErrorHandler::handle(mysql_error() . "\n Query with error " . $insertQuery . "\n");
        $retArray   = array("is_success" => 0, "ad" => NULL);
        $newAdId    = mysql_insert_id();        
        $images     = "";
        if($newAdId > 0){
            if(!is_null($pImages) && !empty ($pImages)){
                for($i = 0; $i < count($pImages); $i++){
                     $q = 'INSERT INTO images(ad_id, name, url) VALUES (' . $newAdId . ',"'.mysql_real_escape_string($pImages[$i]['name']).'","' . mysql_real_escape_string($pImages[$i]['url']) . '")';
                     $images.= "Q: " . $q . "\n";
                     mysql_query($q) or ($images.= mysql_error() . "\n\n\n\n");
                }
            }
            $this->log->lwrite(print_r($pImages, true) . " \n images: " . $images);
            $retArray['is_success'] = 1;
            $retArray['ad'] = array(
                                    "id" => $newAdId, 
                                    "title" => $pTitle,
                                    "content" => $pContent,
                                    "price"=>$pPrice,
                                    "address" => $pAddress,
                                    "cat_id" => $pCategoryId,
                                    "user_id" => $pUserId,
                                    "comments" => array(),
                                    "total_comments" => 0,
                                    "views" => 0,
                                    "source" => $pSource,
                                    "email" => $pEmail,
                                    "phone" => $pPhone,
                                    "price" => $pPrice,
                                    "currency" => $pCurrency,
                                    "images" => $pImages);
        }
        
        if(isset($this->mAdNotifier)){            
            $this->mAdNotifier->onAdAdded($retArray);
        } else{
            $this->log->lwrite("no ad notifier specified");
        }        
        
        return $retArray;
    }            
    
    public function getAdImages($adId){
        $selectImages = "SELECT * FROM images WHERE ad_id = " . $adId;
        $results          = mysql_query($selectImages) or (ErrorHandler::handle(mysql_error() . "\n Query with error " . $insertQuery . "\n "));
        $returnArray      = array("is_success" => 1, "images" => NULL);
        
        if(mysql_num_rows($results) == 0){
            $returnArray['images'] = array();                        
        } else{ 
            $images = array();
            $index      = 0;
            while($row = mysql_fetch_assoc($results)){
                $images[$index++] = $row;
            }
            $returnArray['images'] = $images;
        }
        
        return $returnArray;
    }
    
    public function getCategories(){
        $selectCategories = "SELECT * FROM categories";
        $results          = mysql_query($selectCategories) or (ErrorHandler::handle(mysql_error() . "\n Query with error " . $insertQuery . "\n "));
        $returnArray      = array("is_success" => 1);
        
        if(mysql_num_rows($results) == 0){
            $returnArray['categories'] = array();            
            
        } else{ 
            $categories = array();
            $index      = 0;
            while($row = mysql_fetch_assoc($results)){
                $categories[$index++] = $row;
            }
            $returnArray['categories'] = $categories;
        }
        
        return $returnArray;
    }
        
    public function addCategory($catName){
        $insertQuery = "INSERT INTO categories(name) VALUES('" . mysql_real_escape_string($catName) . "')";
        $result      = mysql_query($insertQuery) or ErrorHandler::handle(mysql_error() . "\n Query with error " . $insertQuery . "\n ");
        $returnArray = array("is_success" => 0);
        
        if($result != false){
            $newId = mysql_insert_id();
            $returnArray['is_success'] = 1;
            $returnArray['category'] = array("name" => $catName, "id" => $newId);
        } else{
            $returnArray['category'] = array("name" => $catName, "id" => -1);
        }
        
        return $returnArray;
    }
    
    public function getAdsByCategory($catId){
        $selectQ     = "SELECT * FROM ads WHERE cat_id = " . $catId;
        $results     = mysql_query($selectQ) or ErrorHandler::handle(mysql_error() . "\n Query with error " . $insertQuery . "\n ");
        $returnArray = array("is_success" => 0);
        
        if(mysql_num_rows($results) == 0){
            $returnArray['ads'] = array();
        } else{
            $ads    = array();
            $index  = 0;
            while($row = mysql_fetch_assoc($results)){
                $ads[$index++] = $row;
            }
            $returnArray['ads'] = $ads;            
            $returnArray['is_success'] = 1;
        }
        echo(json_encode($returnArray));
        return $returnArray;
    }
    
    public function createUser($user){
        $activate_code = md5($user->getName().$user->getAuthName().$user->getPassword().time());
        $user->setActivateCode($activate_code);
        $createUserQ = "INSERT INTO users(name, password, authname, is_active, activate_code, email) VALUES ('".
                        mysql_real_escape_string($user->getName()) . "','". mysql_real_escape_string($user->getPassword()) . "','" . mysql_real_escape_string($user->getAuthName()) . "',0,'" .
                        mysql_real_escape_string($user->getActivateCode()) . "','" . mysql_real_escape_string($user->getEmail()) . "')";
        $result      = mysql_query($createUserQ);
        $returnArray = array("is_success" => 0, "user" => NULL);
        if($result != false){
            $returnArray['is_success'] = 1;
            $id = mysql_insert_id();
            $user->setId($id);
            $returnArray['user'] = $user->toArray();
        }        
        return $returnArray;                
    }
    
    public function activateUser($activateCode){
        $updateQ = "UPDATE users SET is_active = 1 WHERE activate_code ='" . mysql_real_escape_string($activateCode) . "'";
        $results = mysql_query($updateQ) or ErrorHandler::handle(mysql_error() . "\n Query with error " . $updateQ . "\n ");
        $returnArray = array("is_success" => 0);
        if(mysql_affected_rows() > 0){
            $returnArray['is_success'] = 1;
        }
        
        return $returnArray;
    }
    /*
     *     mysql> CREATE TABLE comments(
        -> id INT NOT NULL PRIMARY KEY auto_increment,
        -> title VARCHAR(50) NOT NULL,
        -> content VARCHAR(200) NOT NULL,
        -> ad_id INT NOT NULL,
        -> owner_user_id INT NOT NULL,
        -> date INT NOT NULL,
        -> rating INT NOT NULL DEFAULT 0);        
     */
    
    public function addComment($pComment){
        $insertQ = "INSERT INTO comments(title, content, ad_id, owner_user_id, date) VALUES (" .
                   "'" . mysql_real_escape_string($pComment->getTitle()) . "','" . mysql_real_escape_string($pComment->getContent()) . "',".
                   $pComment->getAdId() . "," . $pComment->getOwnerUserId() . ",". $pComment->getDate() . ")";
        $result = mysql_query($insertQ)  or ErrorHandler::handle(mysql_error() . "\n Query with error " . $insertQ. "\n ");
        $returnArray = array("is_success" => 0, "comment" => NULL);
        
        if($result != false){
            $returnArray['is_success'] = 1;
            $pComment->setId(mysql_insert_id());
            $returnArray['comment'] = $pComment->toArray();
        }
        
        return $returnArray;
    }
    
    public function removeComment($pCommentId, $pOwnerId){
        $deleteQ = "DELETE FROM comments WHERE (id = " . $pCommentId . " AND owner_user_id = " . $pOwnerId . ")";
        $result  = mysql_query($deleteQ)  or ErrorHandler::handle(mysql_error() . "\n Query with error " . $deleteQ. "\n ");
        $returnArray = array("is_success" => 0, "comment_id" => $pCommentId, "owner_user_id" => $pOwnerId);
        
        if(mysql_affected_rows() > 0){
            $returnArray['is_success'] = 1;
        }                
        return $returnArray;
    }
    
    public function getAllCommentsForAd($pAdId){
        $selectQ = "SELECT c.*, u.name as user_name, u.id as user_id FROM comments c, users u WHERE c.ad_id = " . $pAdId . " AND u.id = c.owner_user_id";        
        $results = mysql_query($selectQ)  or ErrorHandler::handle(mysql_error() . "\n Query with error " . $selectQ. "\n ");
        $returnArray = array("is_success" => 1, "comments" => array());
        
        if(mysql_num_rows($results) > 0){
            while($row = mysql_fetch_assoc($results)){
                $returnArray['comments'][count($returnArray['comments'])] = $row;
            }
            $returnArray['is_success'] = 1;
        }                
        return $returnArray;        
    }
    
    public function getAllCommentsForUser($pUserId){
        $selectQ = "SELECT c.*, u.name, u.id as user_id FROM comments c, users u WHERE c.owner_user_id = " . $pUserId . " ORDER BY c.date DESC";
        echo($selectQ);
        $results = mysql_query($selectQ)  or ErrorHandler::handle(mysql_error() . "\n Query with error " . $selectQ. "\n ");
        $returnArray = array("is_success" => 0, "comments" => array(), "user_id" => $pUserId);
        
        if(mysql_num_rows($results) > 0){
            while($row = mysql_fetch_assoc($results)){
                $returnArray['comments'][count($returnArray['comments'])] = $row;
            }
            $returnArray['is_success'] = 1;
        }                
        return $returnArray;        
    }    
    
    public function updateCommentRating($pCommentId, $status/*+1 or -1 */){
        $localStatus = 0;
        if($status > 0)
            $localStatus = 1;
        else
            $localStatus = -1;
            
        $updateQ = "UPDATE comments SET rating = rating + (" . $localStatus . ") WHERE id =" .$pCommentId;
        echo($updateQ);
        $results = mysql_query($updateQ) or ErrorHandler::handle(mysql_error() . "\n Query with error " . $updateQ . "\n ");
        $returnArray = array("is_success" => 0, "comment_id" => $pCommentId, "new_rating" => 0);
        if(mysql_affected_rows() > 0){
            $returnArray['is_success'] = 1;
            // get new rating
            $rResult = mysql_query("SELECT rating FROM comments WHERE id = " . $pCommentId);
            $row = mysql_fetch_assoc($rResult);
            $returnArray['new_rating'] = $row['rating'];
        }        
        return $returnArray;
    }    
    
    public function updateViews($pAdId, $status/*+1 or -1 */){
        $localStatus = 0;
        if($status > 0)
            $localStatus = 1;
        else
            $localStatus = -1;
            
        $updateQ = "UPDATE ads SET views = views + (" . $localStatus . ") WHERE id =" .$pAdId;
        echo($updateQ);
        $results = mysql_query($updateQ) or ErrorHandler::handle(mysql_error() . "\n Query with error " . $updateQ . "\n ");
        $returnArray = array("is_success" => 0, "ad_id" => $pAdId, "views" => 0);
        if(mysql_affected_rows() > 0){
            $returnArray['is_success'] = 1;
            // get new rating
            $rResult = mysql_query("SELECT views FROM ads WHERE id = " . $pAdId);
            $row = mysql_fetch_assoc($rResult);
            $returnArray['views'] = $row['views'];
        }        
        return $returnArray;
    }     
    
    public function getTotalPages($categoryId, $adsPerPage){
        if($categoryId == 1)
            $selectQ = "SELECT count(*) as total_ads from ads";
        else
            $selectQ = "SELECT count(*) as total_ads from ads where cat_id = " . $categoryId;
        $result = mysql_query($selectQ)or ErrorHandler::handle(mysql_error() . "\n Query with error " . $selectQ . "\n ");
        
        if(mysql_num_rows($result) > 0){
            $row = mysql_fetch_assoc($result);
            $totalAds = intval($row['total_ads']);            
            if($totalAds % $adsPerPage != 0)
                return intval ($totalAds / $adsPerPage) + 1;
            else
                return intval ($totalAds / $adsPerPage);
        }
        
        return 0;
    }
    
    public function getPageAds($page, $categoryId, $adsPerPage){
        $limit1 = $page * $adsPerPage;
        $limit2 = $adsPerPage;
        if($categoryId == 1){
             $selectQ = " SELECT ads.id,
                     ads.title,
                     ads.content,
                     ads.phone,
                     ads.email,
                     ads.address,
                     ads.date,
                     ads.source,
                     ads.currency,
                     ads.cat_id,
                     ads.views,
                     ads.user_id,
                     (select name from categories where categories.id = ads.cat_id) as cat_name,
                     (select name from users where users.id = ads.user_id) as user_name,
                     comments.id as com_id,
                     comments.title as com_title,
                     comments.ad_id as com_ad_id,
                     comments.owner_user_id as com_user_id,
                     comments.content as com_content,
                     comments.date as com_date,
                     count(comments.id) as total_comments,
                     comments.rating as com_rating,                     
                     (select users.name from users where com_user_id = users.id) as com_username
                     FROM ads LEFT JOIN comments ON
                     ads.id = comments.ad_id group by ads.id ORDER BY ads.date DESC LIMIT " . $limit1 . "," . $limit2.";";                    
        } else{
        
            $selectQ = " SELECT ads.id,
                     ads.title,
                     ads.currency,
                     ads.content,
                     ads.phone,
                     ads.email,
                     ads.address,
                     ads.date,
                     ads.source,
                     ads.cat_id,
                     ads.views,
                     ads.user_id,
                     (select name from categories where categories.id = ads.cat_id) as cat_name,
                     (select name from users where users.id = ads.user_id) as user_name,
                     comments.id as com_id,
                     comments.title as com_title,
                     count(comments.id) as total_comments,
                     comments.content as com_content,
                     comments.ad_id as com_ad_id,
                     comments.owner_user_id as com_user_id,
                     comments.date as com_date,
                     comments.rating as com_rating,
		     (select users.name from users where com_user_id = users.id) as com_username
                     FROM ads LEFT JOIN comments ON
                     ads.id = comments.ad_id WHERE ads.cat_id = " . $categoryId . " group by ads.id ORDER BY ads.date DESC LIMIT " . $limit1 . "," . $limit2.";";        
        }
		// SELECT ads.id,ads.title,ads.content,ads.phone,ads.email,ads.address,ads.date,ads.cat_id,ads.views,ads.user_id,comments.id as com_id,comments.title as com_title,comments.ad_id as com_ad_id,comments.owner_user_id as com_user_id,comments.date as com_date,comments.rating as com_rating,
		//			 (select users.name from comments,users where comments.owner_user_id = users.id) as username
        //             FROM ads LEFT JOIN comments ON
        //             ads.id = comments.ad_id group by ads.id ORDER BY ads.date
        $results     = mysql_query($selectQ) or ErrorHandler::handle(mysql_error() . "\n Query with error " . $insertQuery . "\n ");
        $this->log->lwrite("getPageAds query: " . $selectQ . "\n count:" . mysql_num_rows($results));
        $returnArray = array("is_success" => 0);        
        if(mysql_num_rows($results) == 0){
            $returnArray['ads'] = array();
        } else{
            $ads    = array();
            $index  = 0;
            while($row = mysql_fetch_assoc($results)){                                
                $ads[$index] = $row;
                $ads[$index]['comments'] = array();
                $ads[$index]['comments'][0] = array("id"=>$row['com_id'], "title"=>$row['com_title'], "content"=>$row['com_content'], "owner_user_id"=>$row['com_user_id'],
                                                     "date"=> $row['com_date'], "rating"=>$row['com_rating'], "user_name"=>$row['com_username']);     
                $images = $this->getAdImages($row['id']);
                $ads[$index]['images']  = $images['images'];
                
                // unset some things
                unset ($ads[$index]['com_id']);
                unset ($ads[$index]['com_title']);
                unset ($ads[$index]['com_content']);
                unset ($ads[$index]['com_user_id']);
                unset ($ads[$index]['com_date']);
                unset ($ads[$index]['com_rating']);
                unset ($ads[$index]['com_username']);                

                $index++;
            }
            $returnArray['ads'] = $ads;            
            $returnArray['is_success'] = 1;
        }        
        return $returnArray;        
    }
    
    /*
        SELECT count(ads.title) as count_ads, ads.*, comments.* FROM ads left join comments on ads.id = comments.ad_id order by ads.date DESC \G
        SELECT ads.id as ad_id,
                 ads.title as ad_title,
                 ads.content as ad_content,
                 ads.phone as ad_phone,
                 ads.email as ad_email,
                 ads.address as ad_address,
                 ads.date as ad_date,
                 ads.source ad_source,
                 ads.cat_id as ad_cat_id,
                  ads.user_id as ad_user_id,

                 comments.id as com_id,
                 comments.title as com_title,
             comments.ad_id as com_ad_id,
             comments.owner_user_id as com_user_id,
             comments.date as com_date,
             comments.rating as com_rating

             FROM ads LEFT JOIN comments ON
             ads.id IN (SELECT id comments  WHERE comments.id == ads.id) \G        
     * 
     */
    
    // user oriented methods
    public function login($userName, $userPassword){
        $loginQ     = "UPDATE users SET is_logged_in = 1 WHERE authname = '" . mysql_real_escape_string($userName) . "' AND password = '" . mysql_real_escape_string($userPassword) . "'";
        $retArray   =  array("is_success" => 0, "user" => NULL , "message" => "Login failed.");
        
        if(strlen($userName) == 0 || strlen($userPassword) == 0)
            return $retArray;
        
        $result = mysql_query($loginQ) or ErrorHandler::handle(mysql_error() . "\n Query with error " . $loginQ . "\n ");        
        $userDetails = $this->getUserByName($userName, $userPassword);        
        if(mysql_affected_rows() > 0 ){
            $retArray['is_success'] = 1;            
            $retArray['user'] = $userDetails;
            $retArray['messsage'] = "Login success.";
            // we do not need his password
            unset($retArray['user']['password']);
        } else{
            if($userDetails != NULL){
                if($userDetails['is_logged_in'] == 1){
                    $retArray['is_success'] = 1;            
                    $retArray['user'] = $userDetails;
                    // we do not need his password
                    unset($retArray['user']['password']);      
                    $retArray['message'] = "Login success.";
                }
            }
        }
        
        return $retArray;
    }
    
    public function logout($userId){
        $logoutQ     = "UPDATE users SET is_logged_in = 0 WHERE id = " . $userId;
        $retArray   =  array("is_success" => 0, "userId" => NULL, "message" => "Logout failed.");
        
        if(!isset ($userId))
            return $retArray;
        
        $result = mysql_query($logoutQ) or ErrorHandler::handle(mysql_error() . "\n Query with error " . $logoutQ . "\n ");                
        $userDetails = $this->getUserById($userId);
        if(mysql_affected_rows() > 0 ){
            $retArray['is_success'] = 1;            
            $retArray['userId'] = $userId;             
            $retArray['message'] = "Logout success.";
        } else{
            if($userDetails != NULL){
                if($userDetails['is_logged_in'] == 0){
                    $retArray['is_success'] = 1;            
                    $retArray['userId'] = $userId;                                        
                    $retArray['message'] = "Logout success.";
                }
            }
        }
        
        return $retArray;
    }    
    
    public  function getUserByName($userName, $userPassword){
        $selectQ = "SELECT * FROM users WHERE authname = '" .mysql_real_escape_string($userName) . "' AND password = '" . mysql_real_escape_string($userPassword) . "' LIMIT 1";
                
        $result  = mysql_query($selectQ) or ErrorHandler::handle(mysql_error() . "\n Query with error " . $selectQ . "\n ");
        if(mysql_num_rows($result) > 0){
           $row = mysql_fetch_assoc($result);
           return $row;
        } else {
            return NULL;
        }            
    }
    
    public  function getUserById($userId){
        $selectQ = "SELECT * FROM users WHERE id = " . $userId . " LIMIT 1";
                
        $result  = mysql_query($selectQ) or ErrorHandler::handle(mysql_error() . "\n Query with error " . $selectQ . "\n ");
        if(mysql_num_rows($result) > 0){
           $row = mysql_fetch_assoc($result);
           return $row;
        } else {
            return NULL;
        }            
    }    
    
    public  function getAdById($adId){
        $selectQ = "SELECT * FROM ads WHERE id = " . $adId . " LIMIT 1";                
        $result  = mysql_query($selectQ) or ErrorHandler::handle(mysql_error() . "\n Query with error " . $selectQ . "\n ");
        $retArray = array("is_success" => 0, "ad"=> NULL);
        if(mysql_num_rows($result) > 0){
           $row = mysql_fetch_assoc($result);
           $retArray['is_success'] = 1;
           $retArray['ad'] = $row;
        } 
        
        return $retArray;
    }

    // Alerts
    public function addAlert($alertTitle, $alertFilters, $alertUserId, $alertCategoryId){
        $now = time();
        $insertQ = "INSERT INTO alerts(title, filters, user_id, cat_id, added_date, last_checked_date) VALUES ('" . mysql_real_escape_string($alertTitle) . "', '" .mysql_real_escape_string($alertFilters) . "'," . $alertUserId	. "," . $alertCategoryId . "," . $now . "," . $now . ")";
        $result = mysql_query($insertQ)  or ErrorHandler::handle(mysql_error() . "\n Query with error " . $insertQ. "\n ");
        $returnArray = array("is_success" => 0, "alert" => NULL);

        if($result != false){
            $returnArray['alert']  = array("id"=>  mysql_insert_id(), "title" => $alertTitle, "filters" => split(",", $alertFilters), "user_id" => $alertUserId, "cat_id" => $alertCategoryId, "added_date"=>$now, "last_checked_date"=>$now);
            $returnArray['is_success'] = 1;
        }
        
        return $returnArray;
    }
    
    // Alerts
    public function getAllAlerts($userId){
        $selectQ        = "SELECT * FROM alerts WHERE user_id = " . $userId . " ORDER BY total_ads_since_last_check DESC"; 
        $results        = mysql_query($selectQ)  or ErrorHandler::handle(mysql_error() . "\n Query with error " . $selectQ. "\n ");
        $returnArray    = array("is_success" => 0, "alerts" => NULL);
        
        if(mysql_num_rows($results) >= 0){
            $index = 0;
            while($row = mysql_fetch_assoc($results)){
                $row['filters'] = json_decode(stripslashes($row['filters']), true);
                $returnArray['alerts'][$index] = $row;
                $index++;
            }
            $returnArray['is_success'] = 1;            
        } else{
            $returnArray['alerts'] = array(); 
        }        
        
        return $returnArray;
    }    
    
    // Alerts
    public function removeAlert($alertId, $alertUserId){        
        $deleteQ = "DELETE FROM alerts WHERE id = " . $alertId . " AND user_id = " . $alertUserId;
        $result = mysql_query($deleteQ)  or ErrorHandler::handle(mysql_error() . "\n Query with error " . $deleteQ. "\n ");
        $returnArray = array("is_success" => 0, "alert" => NULL);

        if(mysql_affected_rows() > 0){
            $returnArray['alert']  = array("id"=>  $alertId, "user_id" => $alertUserId);
            $returnArray['is_success'] = 1;            
        }        
        
        return $returnArray;
    }    
    
    public function getAlertsByCategoryId($catId){
        $selectQ        = "SELECT id, filters FROM alerts WHERE cat_id = " . $catId; 
        $results        = mysql_query($selectQ)  or ErrorHandler::handle(mysql_error() . "\n Query with error " . $selectQ. "\n ");
        $returnArray    = array("is_success" => 0, "alerts" => NULL);
        
        if(mysql_num_rows($results) >= 0){
            $index = 0;
            while($row = mysql_fetch_assoc($results)){								
                $row['filters'] = json_decode(stripslashes($row['filters']), true);
                $returnArray['alerts'][$index] = $row;
                $index++;
				$this->log->lwrite("alert: " . print_r($row, true));
            }
            $returnArray['is_success'] = 1;            
        } else{
            $returnArray['alerts'] = array();
        }        
        
        return $returnArray;        
    }
    
    public function addToAlert($adId, $alertId){
        $this->log->lwrite("adToAlert: adID=" . $adId . " alertID=" . $alertId);
        $insertQ = "INSERT INTO alert_ads(alert_id, ad_id, added_date) VALUES(" . $alertId . ", " . $adId . ", " . time() . ")";
        $updateAdsCounter = "UPDATE alerts SET total_ads_since_last_check=total_ads_since_last_check+1 WHERE id=" . $alertId;
        
        mysql_query($insertQ);
        mysql_query($updateAdsCounter);
    }
}
?>
