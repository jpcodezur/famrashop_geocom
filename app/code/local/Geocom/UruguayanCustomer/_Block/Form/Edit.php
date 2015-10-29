<?php
Class Geocom_UruguayanCustomer_Block_Form_Edit extends Mage_Customer_Block_Form_Edit{

    private $geoLoyaltyCustomerDataLoaded=false;

    public function setGeoLoyaltyCustomerDataLoaded($loaded){
        $this->geoLoyaltyCustomerDataLoaded=$loaded;
    }
    public function isGeoLoyaltyCustomerDataLoaded(){
        return $this->geoLoyaltyCustomerDataLoaded;
    }

}