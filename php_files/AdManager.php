<?php
    include_once "Ad.php";
    include_once "EVNT_API.php";
    include_once "DBManager.php";
    include_once "ErrorHandler.php";
    include_once 'interfaces.php';
    include_once 'AlertsManager.php';
    

class AdManager implements ICollector{

        // apis
        private $evnt_api;
        
        // data
        private $dbMan;
        private $totalAds;
        private $myEmail = "smartliviu@gmail.com";
        private $alertsMan;
        
        function __construct() {
            $this->dbMan        = new DBManager();
            $this->evnt_api     = new EVNT_API();         
            $this->totalAds     = 0;
            $this->alertsMan    = new AlertsManager();
        }
        
        public function collect(){
            
            foreach (EVNT_API::$CATEGORIES  as $catId) {                                
                
                // get last update time
                $lastUpdateTime = $this->dbMan->getLastUpdateDate($this->evnt_api->getCategoryNameById($catId), EVNT_API::$SOURCE);                
                
                // collect from evenimentul.ro
               $this->evnt_api->collectAds($catId, $lastUpdateTime, 0, $this);               
            } 
            
            $message = "iasianunta.info AdCollector stats on " . date("D, d M Y H:i:s");
            $message .= "<br /><br />Total ads added from " . EVNT_API::$SOURCE . ": " . $this->totalAds . " ads. <br /><br /><br />";
            $message .= "<a href='www.iasianunta.info' target='_blank'>www.iasianunta.info</a>";
            echo("sending status mail....\n");
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

            // Additional headers
            $headers .= 'To: Mary <'. $this->myEmail .'>' . "\r\n";
            $headers .= 'From: collectBot' . "\r\n";            
            mail($this->myEmail, '[iasianunta.info AdCollector stats] total: ' . $this->totalAds . " - " . EVNT_API::$SOURCE, $message, $headers);
            echo("\nemail sent. \n");
            echo("Total ads added from " . EVNT_API::$SOURCE . ": " . $this->totalAds . " ads\n");
            
        }
        
        public function onAdCollected($ad){            
            if(strlen($ad->getContent()) > 10){
                $newAd = $this->dbMan->addAdWithParams($ad->getTitle(), $ad->getContent(), $ad->getPrice(), $ad->getAddress(), $ad->getCategoryId(), $ad->getPhone(), $ad->getEmail(), $ad->getUserId(), $ad->getImages(), "LEI");
                $this->totalAds++;
                $this->alertsMan->onAdAdded($newAd);
            }
        }     
}
    
?>