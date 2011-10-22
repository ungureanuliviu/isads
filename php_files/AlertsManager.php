<?php
    include_once "Ad.php";    
    include_once "ErrorHandler.php";
    include_once 'interfaces.php';
    include_once "Log.php";
    include_once 'DBManager.php';
    
   class AlertsManager implements IAdNotifier{
       // Data
       private $log;
       private $dbMan;
       function __construct() {
           $this->log = new Log();
           $this->dbMan = new DBManager();
    }
    
    public function onAdAdded($newAdResponse){
        $newAd = $newAdResponse['ad'];
        $this->log->lwrite("new ad added: " . print_r($newAd, true));
        if(is_array($newAd)){
            $alertsResponse = $this->dbMan->getAlertsByCategoryId($newAd['cat_id']);
            if($alertsResponse['is_success'] == 1){
                if(!empty ($alertsResponse['alerts'])){
                    for($i = 0; $i < count($alertsResponse['alerts']); $i++){
                        $shouldStop = false;
                        for($j = 0; $j < count($alertsResponse['alerts'][$i]['filters']); $j++){
                            $filter = $alertsResponse['alerts'][$i]['filters'][$j];
                            $this->log->lwrite("current filter: " . print_r($filter, true));                            
                            switch ($filter['type']){
                                case 'content':
                                    for($fIndex = 0; $fIndex < count($filter['constraints']); $fIndex++){
                                        if(strpos($newAd['content'], $filter['constraints'][$fIndex])){
                                            $this->dbMan->addToAlert($newAd['id'], $alertsResponse['alerts'][$i]['id']);                                
                                            $shouldStop = true;
                                        } else if(strpos($newAd['title'], $filter['constraints'][$fIndex])){
                                            $this->dbMan->addToAlert($newAd['id'], $alertsResponse['alerts'][$i]['id']);                                
                                            $shouldStop = true;
                                        } else if(strpos($newAd['address'], $filter['constraints'][$fIndex])){
                                            $this->dbMan->addToAlert($newAd['id'], $alertsResponse['alerts'][$i]['id']);                                
                                            $shouldStop = true;
                                        }
                                    }
                                break;
                                case 'price':                                    
                                    if($newAd['currency'] == $filter['currency']){
                                        if($newAd['price'] <= $filter['max'] && $newAd['price'] >= $filter['min']){
                                            $this->dbMan->addToAlert($newAd['id'], $alertsResponse['alerts'][$i]['id']);    
                                            $shouldStop = true;
                                        }
                                    }
                                break;                                    
                            }
                            
                            /*
                            if(strlen($alertsResponse['alerts'][$i]['filters'][$j]) > 0){
                                if(strpos($newAd['content'], $alertsResponse['alerts'][$i]['filters'][$j]) != FALSE){
                                    $this->log->lwrite("content: " . $newAd['content'] . "\n\n " . $alertsResponse['alerts'][$i]['filters'][$j]);
                                    $this->dbMan->addToAlert($newAd['id'], $alertsResponse['alerts'][$i]['id']);                                
                                    break;
                                }
                            }
                            
                            */                            
                            if($shouldStop)
                                break;
                        }
                    }
                }
            }
        }
        $this->log->lwrite("method end==============================");
    }
    
    public function getAdNotifier(){
        return $this;
    }

}

?>