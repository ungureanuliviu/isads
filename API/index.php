<?php    
    
    session_start();
    
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 2097 05:00:00 GMT');
    header('Content-type: application/json');        
    header('Accept: application/json');
    
    include_once "../php_files/Log.php";
    include_once '../php_files/User.php';
    include_once '../php_files/Ad.php';
    include_once '../php_files/DBManager.php';    
    date_default_timezone_set("Europe/Bucharest");
    $dbMan  = new DBManager();
    $log    = new Log();    
    $publicMethods = array("createUser", "ads_get_all", "comments_get_all");
    
    if(empty ($_POST)){        
        $_POST = json_decode (file_get_contents("php://input"), true);     
    }
                
    if(!in_array($_GET['type']."_".$_GET['method'], $publicMethods)){            
        // login 
        if (!isset($_SERVER['PHP_AUTH_USER']) && !isset ($_POST['client'])) {
            header('WWW-Authenticate: Basic realm="Login"');
            header('HTTP/1.0 401 Unauthorized');                    
            return;
        } else {                    
            $client = $_POST['client'];                
            if(strcmp($client, "android") == 0  && $_SESSION['is_logged_in'] == 0){                
                $result = $dbMan->login($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);                
                if($result['is_success'] == 1){                             
                        $_SESSION['is_logged_in'] = 1;
                } else{
                    print(json_encode(array("is_success"=>0, "message"=>"Auth failed")));
                    $_SESSION['is_logged_in'] = 0;
                    return;
                }
            }    
        }                                                           
    }    
    
    $log->lwrite( file_get_contents("php://input") . " Server: " . print_r($_SERVER, true) . "\n\n" . "method: " . $_SERVER['REQUEST_URI'] . "\n GET " . print_r($_GET, true) . " \nPOST: ". print_r($_POST, true) . "=====================================" );        
    
    //print_r($_GET);
    if(!empty($_GET)){
		switch ($_GET['type']){
                        case "alerts":
                                switch ($_GET['method']){
                                    case "get_all":
                                        /**
                                         * Get all alerts for a user.
                                         * params:
                                         *      user_id : user's id
                                         * return:
                                         *      a json with all alerts
                                         */
                                        $userId = $_POST['user_id'];
                                        $result = $dbMan->getAllAlerts($userId);
                                        print(json_encode($result));
                                    break;
                                    case "add":
                                        /** public function addAlert($alertTitle, $alertFilters, $alertUserId, $alertCategoryId){
                                         * Create a new alert for a given user
                                         * params:
                                         *      title:      alert's title
                                         *      filters:    an comma separated string with all filter data
                                         *      user_id:    user's id
                                         *      cat_id      a category id.
                                         */
                                         $title     = $_POST['title'];
                                         $filters   = $_POST['filters'];
                                         $userId    = $_POST['user_id'];
                                         $catId     = $_POST['cat_id'];
                                         $result    = $dbMan->addAlert($title, $filters, $userId, $catId);
                                         print($result);
                                    break;
                                    case 'remove':
                                        /**
                                         * public function removeAlert($alertId, $alertUserId){  
                                         * Remove a alert
                                         * params:
                                         *      user_id: owner's id
                                         *      id: alert's id
                                         */
                                        $userId     = $_POST['user_id'];
                                        $alertId    = $_POST['id'];
                                        $result     = $dbMan->removeAlert($alertId, $userId);
                                        print(json_encode($result));
                                    break;
                                }
                                break;
			case "session":
				switch ($_GET['method']){
					case "login":
						// we have to login the user
						$userName = $_POST['user_name'];
						$userPass = $_POST['user_password'];
						$result = $dbMan->login($userName, $userPass);
						if($result['is_success'] == 1){
							// set the current user details in session
							$_SESSION['user'] = $result['user'];
						}
						print(json_encode($result));
						break;
					case "logout":
						// we have to login the user
						$userId = $_POST['user_id'];
						$result = $dbMan->logout($userId);
						if($result['is_success'] == 1){
							// clear the current user details from session
							unset($_SESSION['user']);
                                                        $_SESSION['is_logged_in'] = 0;
							session_destroy();
						}
						print(json_encode($result));
						break;
				}
			break;
			case "categories":
                            switch ($_GET['method']){
                                case  "get_all":                        
                                     // get categories
                                    $result = $dbMan->getCategories();
                                    print(json_encode($result));       
                                    
                                    break;                                    
                            }				
			break;
			case "ads":
				switch ($_GET['method']){
					case "get_all":
                                            $page = $_POST['page'];
                                            $categoryId = $_POST['category_id'];
                                            $adsPerPage = $_POST['ads_per_page'];

                                            $log->lwrite("Page: " . $page . " catID: " . $categoryId . " adsPerPage: " . $adsPerPage);

                                            // get all ads
                                            $result = $dbMan->getPageAds($page, $categoryId, $adsPerPage);
                                            print(json_encode($result));
                                            break;			
                                        case "add":
                                            $result = $dbMan->addAdWithParams($_POST['title'], $_POST['content'], $_POST['price'], $_POST['address'], $_POST['category_id'],
                                                                              $_POST['phone'], $_POST['email'], $_POST['user_id'], $_POST['source'], $_POST['images']);
                                            print(json_encode($result));
                                            break;
				}
			break;
			case "comments":
				switch ($_GET['method']){
					case "add":
						// get parameters
						$commentTitle	= $_POST['title'];
						$commentContent = $_POST['content'];
						$commetOwnerId  = $_POST['owner_user_id'];
						$commentAdId	= $_POST['ad_id'];

						$comment = new Comment();
						$comment->setTitle($_POST['title']);
						$comment->setContent($_POST['content']);
						$comment->setAdId($_POST['ad_id']);
						$comment->setOwnerUserId($_POST['owner_user_id']);
						$comment->setDate(time());
						$comment->setRating(0);
						$result = $dbMan->addComment($comment);
                                                $result['comment']['user_name'] = $_SESSION['user']['name'];

						print(json_encode($result));
						break;
                                          case "get_all":
                                              $adId = $_POST['ad_id'];
                                              $result = $dbMan->getAllCommentsForAd($adId);
                                              
                                              print(json_encode($result));
                                              break;
                                          case "remove":
                                              $commentId = $_POST['id'];
                                              $commentOwnerId = $_POST['owner_user_id'];
                                              $result = $dbMan->removeComment($commentId, $commentOwnerId);
                                              
                                              print(json_encode($result));
                                              break;
                                          default: print(json_encode(array("is_success" => 0, "message"=> "The method [" . $_GET['method'] . "] is not registered in system"))); break;
				}
				break;
			default: print("invalid request"); break;
		}                               
    }
?>
