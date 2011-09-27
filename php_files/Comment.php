<?php
class Comment {
    /*
    mysql> CREATE TABLE comments(
        -> id INT NOT NULL PRIMARY KEY auto_increment,
        -> title VARCHAR(50) NOT NULL,
        -> content VARCHAR(200) NOT NULL,
        -> ad_id INT NOT NULL,
        -> owner_user_id INT NOT NULL,
        -> date INT NOT NULL,
        -> rating INT NOT NULL DEFAULT 0);    
    */
    // Data
    private $id;
    private $title;
    private $content;
    private $adId;
    private $ownerUserId;
    private $date;
    private $rating;
    private $userName;
    
    
    function __construct() {
        
    }

    public function setUserName($pUserName){
            $this->userName=$pUserName;
            return $this;
    }

    public function getUserName(){
            return $this->userName;
    }
    
    public function setId($pId){
            $this->id=$pId;
            return $this;
    }

    public function getId(){
            return $this->id;
    }


    public function setTitle($pTitle){
            $this->title=$pTitle;
            return $this;
    }

    public function getTitle(){
            return $this->title;
    }


    public function setContent($pContent){
            $this->content=$pContent;
            return $this;
    }

    public function getContent(){
            return $this->content;
    }


    public function setAdId($pAdId){
            $this->adId=$pAdId;
            return $this;
    }

    public function getAdId(){
            return $this->adId;
    }


    public function setOwnerUserId($pOwnerUserId){
            $this->ownerUserId=$pOwnerUserId;
            return $this;
    }

    public function getOwnerUserId(){
            return $this->ownerUserId;
    }


    public function setDate($pDate){
            $this->date=$pDate;
            return $this;
    }

    public function getDate(){
            return $this->date;
    }


    public function setRating($pRating){
            $this->rating=$pRating;
            return $this;
    }

    public function getRating(){
            return $this->rating;
    }


    public function toString(){
            return  "Field Id:". $this->id
            ."\n".  "Field Title:". $this->title
            ."\n".  "Field Content:". $this->content
            ."\n".  "Field AdId:". $this->adId
            ."\n".  "Field OwnerUserId:". $this->ownerUserId
            ."\n".  "Field Date:". $this->date
            ."\n".  "Field Rating:". $this->rating
            ."\n";
    }      
    
    public function toArray(){
            return array(
            "id"=>$this->id,
            "title"=>$this->title,
            "content"=>$this->content,
            "ad_id"=>$this->adId,
            "owner_user_id"=>$this->ownerUserId,
            "date"=>$this->date,
            "rating"=>$this->rating,
             "user_name" => $this->userName);
    }
    
}
?>