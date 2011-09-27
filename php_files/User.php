<?php
    class User {
        private $name;
        private $authName;
        private $password;
        private $id;
        private $is_activ;
        private $activate_code;
        private $email;
        private $isLoggedId;
            
        function __construct($pName, $pAuthName, $pPassword, $pEmail) {
            $this->name = $pName;
            $this->authname = $pAuthName;
            $this->password = $pPassword;
            $this->email = $pEmail;
            $this->is_activ = 0;
            $this->activate_code = "";
            $this->id = -1;
            $this->isLoggedIn = 0;
        }   
        
        public function isLoggedIn(){
            return $this->isLoggedIn;
        }
        
        public function setLoginStatus($isLoggedId){
            $this->isLoggedIn = $isLoggedId;
            return $this;
        }
        
        public function getName(){
            return $this->name;
        }
        
        public function setName($pName){
            $this->name = $pName;
            return $this;
        }
        
        public function setAuthName($pAuthName){
            $this->authname = $pAuthName;
            return $this;
        }
        
        public function getAuthName(){
            return $this->authname;
        }
        
        public function setPassword($pPassword){
            $this->password = $pPassword;
            return $this;
        }
        
        public function getPassword(){
            return $this->password;
        }
        
        public function getId(){
            return $this->id;
        }
        
        public function setId($pId){
            $this->id = $pId;
            return $this;
        }
        
        public function setActiveStatus($pIsActiv){
            $this->is_activ = $pIsActiv;
            return $this;
        }
        
        public function isActiv(){
            return $this->is_activ;
        }
        
        public function setActivateCode($pCode){
            $this->activate_code = $pCode;
            return $this;
        }
        
        public function getActivateCode(){
            return $this->activate_code;
        }
        
        public function getEmail(){
            return $this->email;
        }
        
        public function setEmail($pEmail){
            $this->email = $pEmail;
            return $this;
        }
        
        public function toString(){
            return "\n========================= USER =========================\n".
                    "Id: " . $this->id . "\n" .
                    "Name: " . $this->name . "\n".
                    "Authname: " . $this->authname . "\n".
                    "Password: " . $this->password . "\n" .
                    "Email: " . $this->email . "\n" .
                    "is_activ: " . $this->is_activ . "\n" . 
                    "activate_code: " . $this->activate_code . "\n" .
                    "is_logged_in: " . $this->isLoggedIn . "\n" .
                    "===========================================================\n\n";
        }
        
        public function  toArray(){
            $userArray = array("name" => $this->name, 'authname' => $this->authname, "password" => $this->password,
                               "email" => $this->email, "id" => $this->id, "is_activ" => $this->is_activ, "activate_url" => $this->activate_code,
                                "isLoggedIn" => $this->isLoggedIn);
            return $userArray;
        }
}
?>