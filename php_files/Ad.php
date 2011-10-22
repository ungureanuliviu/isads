<?php
    class Ad {

        private $title;
        private $content;
        private $phone;
        private $address;
        private $email;       
        private $date;
        private $arrayImages;
        private $categoryName;
        private $categoryId;
        private $source;
        private $userId;
        private $views;
        private $price;
    
        function __construct() {
            $this->title = "";
            $this->content = "";
            $this->phone = "";
            $this->address = "";
            $this->email = "";
            $this->date = 0;
            $this->arrayImages = array();
            $this->categoryName = "default";
            $this->categoryId = 1;
            $this->source  = "EVNT";
            $this->userId = -1;
            $this->views = 0;
            $this->price = 0;
        }       

        public function Ad($pTitle, $pContent, $pPhone, $pAddress, $pEmail, $pTime){
            $this->title = $pTitle;
            $this->content = $pContent;
            $this->phone = $pPhone;
            $this->address = $pAddress;
            $this->email = $pEmail;            
            $this->date = $pTime;
            $this->arrayImages = array();
            $this->categoryName = "default";
            $this->categoryId = 1;
            $this->source  = "EVNT";
            $this->userId = -1;
            $this->views = 0;
            $this->price = 0;
        }
        
        public function getPrice(){
            return $this->price;
        }
        
        public function setPrice($pPrice){
            $this->price = $pPrice;
            return $this;
        }
        
        public function getUserId(){
            return $this->userId;
        }
        


        public function getTitle()   { return $this->title; } 
        public function getContent() { return $this->content; } 
        public function getPhone()   { return $this->phone; } 
        public function getAddress() { return $this->address; } 
        public function getEmail()   { return $this->email; } 
        public function setTitle($x) { $this->title = $x; } 
        public function setContent($x) { $this->content = $x; } 
        public function setPhone($x) { $this->phone = $x; } 
        public function setAddress($x) { $this->address = $x; } 
        public function setEmail($x) { $this->email = $x; }     
        public function getViews() {
            return $this->views;
        }
        public function setViews($pViews){
            $this->views = $pViews;
            return $this;
        }
        
        public function setUserId($pUserId){
            $this->userId = $pUserId;
            return $this;
        }
        public function getDate(){
            if($this->date > 0)
                return $this->date;
            else
                return time();
        }
        
        public function getImages(){
            return $this->arrayImages;
        }
        
        public function setDate($x){$this->date = $x; }
        public function addImage($x){$this->arrayImages[count($this->arrayImages)] = $x; }
        public function getCategoryName() { return $this->categoryName; }
        public function setCategoryName($x){ $this->categoryName = $x; }
        public function getCategoryId() { return $this->categoryId; }
        public function setCategoryId($x){ $this->categoryId = $x; }        
        public function setSource($x) { $this->source = $x; }    
        public function getSource() { return $this->source; }
        
        public function toString(){
            $str =  "================ AD ==============\n" .
                        "Title: ". $this->title . "\n".
                        "Date: ". $this->date . "\n".
                        "Content: ". $this->content . "\n".
                        "Phone: ". $this->phone . "\n".
                        "Email: ". $this->email. "\n".
                        "Address: ". $this->address . "\n" .
                        "CategoryName: " . $this->categoryName . "\n". 
                        "CategoryId: " . $this->categoryId . "\n" .
                        "Source: " . $this->source . "\n" .
                        "Images: " . print_r($this->arrayImages, true) . "\n" . 
                        "UserId: " . $this->userId . "\n";
            return $str;                    
        }                    
}
?>