<?php
    interface ICollector{
        public function onAdCollected($ad);
        //public function onCollectProcessDone($source, $totalAds);
    }
    
    interface IAdNotifier{
        public function onAdAdded($newAd);
    }
?>