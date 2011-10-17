<?php
   include "php_files/libraries/wideimage_lib/WideImage.php";
   include_once "php_files/Log.php";
   $destination_path = getcwd()."/library/img/ads_img/";
   $resultArray      = array();      
   $log              = new Log();   
      
   foreach ($_FILES as $key => $value) { 
       $log->lwrite("key: " . $key  . " value: " . print_r($value, true) .  " data " . $value['data']);
       $time = time();
       $value['name'] = str_replace(" ", "-", $value['name']);
       $target_path = $destination_path . $time . "_" . basename($value['name']);           
       if(move_uploaded_file($value['tmp_name'], $target_path)) {
          $image = WideImage::load($target_path);          
          $log->lwrite("target: " . $target_path);
          $resized = $image->resize(80);
          $resized->saveToFile(getcwd()."/library/img/ads_img/th/".$time . "_" . $value['name']);           
          if(count($_FILES) != 1)
            $resultArray[count($resultArray)] = array("name" => $time . "_". $value['name'], "url" => "/library/img/ads_img/".$time . "_" .$value['name']);                    
          else
              $resultArray = array("name" => $time . "_". $value['name'], "url" => "/library/img/ads_img/".$time . "_" .$value['name']);                    
       }       
       usleep(2);
   }   
      
   print(json_encode($resultArray));     
?>