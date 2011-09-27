<?php
include_once "libraries/simple_html_dom.php";
include_once "Ad.php";
include_once "Console.php";
  
class EVNT_API {		
        public static $CATEGORIES = array("CAT_ID_IMOBILIARE" => 100, "CAT_ID_VANZARI" => 40,
                                          "CAT_ID_CUMPARARI" => 41, "CAT_ID_DIVERSE" => 46,
                                          "CAT_ID_SCHIMBURI" => 53, "CAT_ID_INCHIRIERI" => 50,
                                          "CAT_ID_SERVICII" => 56, "CAT_ID_LOCURI_DE_MUNCA" => 64, "CAT_ID_MEDITATII" => 82);		                                                
        public static $SOURCE     = "evenimentul.ro";
        private	$apiUrl	= "http://anunturi.evenimentul.ro/index.php";
        private $html;
        

        function __construct() {
        
        }

        private function request($url){
                return file_get_contents($url);
        }

        public function  getCategoryNameById($pId){                     
            switch ($pId) {
                case 100: return "Imobiliare";
                case 40 : return "Vanzari";
                case 41 : return "Cumparari";
                case 46 : return "Diverse";
                case 53 : return "Schimburi";
                case 50 : return "Inchirieri";
                case 56 : return "Servicii";
                case 64 : return "Locuri de munca";
                case 82 : return "Meditatii";                    
                default :
                    return "Default";
                    break;
            }                  
        }
        private function http_post ($url, $data){
            $data_url = http_build_query ($data);
            $data_len = strlen ($data_url);

            return array ('content'=>file_get_contents ($url, false, stream_context_create (array ('http'=>array ('method'=>'POST'
                    , 'header'=>"Connection: close\r\nContent-Length: $data_len\r\n"
                    , 'content'=>$data_url
                    ))))
                , 'headers'=>$http_response_header
                );
        }
        
        private function diac($s) { 
            $p = array("ã","º","þ","î","â","Ã","ª","Þ","??","??","?","?","?","î","â","?","?","?","Î","Â"); 
            $r = array("a","s","t","i","a","A","S","T","I","A","a","s","t","i","a","A","S","T","I","A"); 
            $ds = str_replace($p, $r, $s); 
            return $ds; 
        }        
        
        public function collectAds($cat, $lastUpdateDate, $page, $callback){
                Console::debug("\nURL: " . $this->apiUrl . "?categ_id=".$cat. "\npage = " . $page ."\nlastUpdateDate " . $lastUpdateDate . " \n\n\n\n\n");                                    
                $postResponse = "";
                $tempPage = $page;                
                if($tempPage > 5){                                       
                    return 0;     
                }
                
                if($tempPage > 0){                    
                    $postResponse = $this->http_post($this->apiUrl, array("search" => 1, "index"=>$tempPage, "categ_id" => $cat, "field"=> "start_date", "stype"=>0));                                        
                    $this->html = str_get_html($postResponse['content']);                                                                                                       
                }
                else{                                                            
                    $this->html = file_get_html($this->apiUrl . "?categ_id=".$cat);                    
                }
               
                
                $ads = $this->html->find("table[cellspacing=8]",0)->find("table[width=100%]");                                                
                $newAds = array();
                for($i = 0; $i < count($ads); $i++) {                        
                        $content    = $ads[$i]->find("td");                                                   
                        $newAd      = new Ad();
                        $newAd->setCategoryName($this->getCategoryNameById($cat));
                        $newAd->setTitle(trim($content[0]->plaintext));                                                                
                        $newAd->setDate(strtotime($content[1]->plaintext));    
                        $newAd->setSource(EVNT_API::$SOURCE);                                                                        
                        if($newAd->getDate() <= $lastUpdateDate && $lastUpdateDate > 0){                                
                            return 0;
                        }
                        /*
                        $images = $content[2]->find("img");
                        if(!empty($images)){                                    
                            foreach( $images as $img){
                                $newAd->addImage($img->src);                                        
                            }
                        }
                        */

                        $newAd->setContent($this->diac($content[2]->plaintext));          
                        // get phone and email
                        $tempContact = $content[3];
                        $tempContact = str_replace(array("[", "]"), "", $content[3]->plaintext);
                        
                        $infoArray = explode(",", $tempContact);                        
                        if(!empty ($infoArray)){
                            for($j = 0; $j < count($infoArray); $j++){
                                if(strpos($infoArray[$j], "tel") !== false){                 
                                    
                                    // we have a phone number
                                    $tel = trim($infoArray[$j]);                                     
                                    $newAd->setPhone(ereg_replace("tel:|tel[[:space:]]*", "", $tel));
                                                      
                                    //$newAd->setContent(ereg_replace("tel:". $newAd->getPhone(), "", $newAd->getContent()));                                                                                
                                    //$newAd->setContent(ereg_replace("tel[[:space:]]*". $newAd->getPhone(), "", $newAd->getContent()));                                    
                                }
                                else if(strpos($infoArray[$j], "email") !== false){                                            
                                    // we have a email addres
                                    $email = trim($infoArray[$j]);                                    
                                    $newAd->setEmail(ereg_replace("email:|email[[:space:]]*", "", $email));                                            
                                    //$newAd->setContent(str_replace(array("email:". $newAd->getEmail(), "email". $newAd->getEmail()), "", $newAd->getContent()));
                                }
                            }                                                                        
                        }
                        $this->totalAds++;
                        $callback->onAdCollected($newAd);
                        Console::debug("\n " . $newAd->toString());                                				
                }
                
                $this->html->clear(); 
                unset($this->html);
                unset($postResponse);                                         
                return $this->collectAds($cat, $lastUpdateDate, $tempPage + 1, $callback);
        }
}
?>
