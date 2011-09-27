<?php
   include "php_files/libraries/wideimage_lib/WideImage.php";
   
   $destination_path = getcwd()."/library/img/ads_img/";
   $resultArray = array();      

   foreach ($_FILES as $key => $value) {  
       $time = time();
       $value['name'] = str_replace(" ", "-", $value['name']);
       $target_path = $destination_path . $time . "_" . basename($value['name']);           
       if(move_uploaded_file($value['tmp_name'], $target_path)) {
          $image = WideImage::load($target_path);          
          $resized = $image->resize(100);
          $resized->saveToFile(getcwd()."/library/img/ads_img/th/".$time . "_" . $value['name']);           
          $resultArray[count($resultArray)] = array("name" => $time . "_". $value['name'], "url" => "/library/img/ads_img/".$time . "_" .$value['name']);                    
       }       
       usleep(2);
   }
      
   print(json_encode($resultArray));     
?>