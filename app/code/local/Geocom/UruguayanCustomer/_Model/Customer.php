<?php
require_once(Mage::getBaseDir('lib') . '/Httpful/Request.php');
require_once(Mage::getBaseDir('lib') . '/Httpful/Http.php');
require_once(Mage::getBaseDir('lib') . '/Httpful/Bootstrap.php');
use \Httpful\Request;
use \Httpful\Http;
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer model
 *
 * @category    Mage
 * @package     Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Geocom_UruguayanCustomer_Model_Customer extends Mage_Customer_Model_Customer
{

    /**
     * Load customer by email or document number
     *
     * @param   string $customerEmail
     * @param   string $documentNumber
     * @return  Mage_Customer_Model_Customer
     */
    public static function findByEmailOrDocument($emailOrDocument)
    {

        if (!empty($emailOrDocument)) {
            $collection = Mage::getModel('uruguayancustomer/customer')->getCollection()
                ->addFieldToFilter('email', $emailOrDocument);
            $size=$collection->getSize();
            if (empty($size)) {
                $collection = Mage::getModel('uruguayancustomer/customer')->getCollection()
                    ->addFieldToFilter('document_number', $emailOrDocument);
            }

            return $collection->getFirstItem()->load();
        }

        return false;
    }
    /*
     * returns data from customer's farmaPoints
     */
    public static function getBalanceQuery($email,$password){
        $uri= WS_BASE_URL.WS_BALANCE_QUERY;
        $data = array('email' => $email,"password"=>$password,"sessionToken"=>"","sourceType"=>"PORTAL_WEB");
        try{
            $response = Request::post($uri)->sendsJson()->body(json_encode($data))->expectsJson()->send();
            return (($response->body && $response->body->response->responseCode===0)?($response->body->balaceByCurrencyList):false);
        }catch(Exception $e){
            return false;
        }
    }

    /*
     * returns data from customer's farmaPoints via session token
     */
    public static function getLoyaltyBalanceQuery($token){
        $uri= WS_BASE_URL.WS_BALANCE_QUERY;
        $data = array('email' => '',"password"=>'',"sessionToken"=>$token,"sourceType"=>"ECOMMERCE");
        try{
            $response = Request::post($uri)->sendsJson()->body(json_encode($data))->expectsJson()->send();
            return (($response->body && $response->body->response->responseCode===0)?($response->body->balaceByCurrencyList):false);
        }catch(Exception $e){
            return false;
        }
    }

    /*
     * returns data for a  customer
     */
    public static function getGeoLoyaltyCustomerData($email,$password){
        $uri= WS_BASE_URL.WS_CLIENT_DATA;
        $data = array('email' => $email,"password"=>$password,"sessionToken"=>"","sourceType"=>"PORTAL_WEB");
        try{
            $response = Request::post($uri)->sendsJson()->body(json_encode($data))->expectsJson()->send();
            return (($response->body && $response->body->response->responseCode===0)?($response->body->client):false);
        }catch(Exception $e){
            return false;
        }
    }

    /*
     * returns data for a  customer via session token
     */
    public static function getLoyaltyCustomerData($token,$callcenter=false){
        $uri= WS_BASE_URL.WS_CLIENT_DATA;
        //$data = array('email' => '',"password"=>'',"sessionToken"=>$token,"sourceType"=>"MOBILE");
        $data = array('email' => '',"password"=>'',"sessionToken"=>$token,"sourceType"=>"ECOMMERCE");
        if($callcenter){$data['sourceType']="DELIVERY";}
        try{
            $response = Request::post($uri)->sendsJson()->body(json_encode($data))->expectsJson()->send();
            return (($response->body && $response->body->response->responseCode===0)?($response->body->client):false);
        }catch(Exception $e){
            return false;
        }
    }

    /*public static function redeemPoints($email,$password,$amount){
        $uri= WS_BASE_URL.WS_REDEEM_POINTS;
        $data = array('email' => $email,'amount'=>$amount,"password"=>$password,"sessionToken"=>"","sourceType"=>"PORTAL_WEB");
        try{
            $response = Request::post($uri)->sendsJson()->body(json_encode($data))->expectsJson()->send();
            return ($response->body && $response->body->response->responseCode===0);
        }catch(Exception $e){
            return false;
        }
    }*/
    //redeems farma points via session token
    public static function redeemPoints($token,$amount){
        $uri= WS_BASE_URL.WS_REDEEM_POINTS;
        $data = array('email' => '','amount'=>$amount,"password"=>'',"sessionToken"=>$token,"sourceType"=>"ECOMMERCE");
        try{
            $response = Request::post($uri)->sendsJson()->body(json_encode($data))->expectsJson()->send();
            return ($response->body && $response->body->response->responseCode===0);
        }catch(Exception $e){
            return false;
        }
    }

    /*
     * token => session token
     * customer_data => original customer data from ws
     * id_geo_address => address id to be deleted
     * callcenter => if callcenter is loged in
     */
    public static function deleteCustomerAddress($token,$customer_data,$id_geo_address,$callcenter=false){
        if(!$customer_data)return false;
        $uri = WS_BASE_URL.WS_UPDATE_CLIENT;
        $data=array('sessionToken'=>$token,'docNumber'=>'','docType'=>'','email'=>'','password'=>'');
        if(!$callcenter){
            $data['sourceType']='ECOMMERCE';
        }
        else{
            $data['sourceType']='DELIVERY';
        }
        $data['client']=$customer_data;
        $addresses= $data['client']->addresses;
        $addreses_modified=array();;
        for($i=0;$i<count($addresses);$i++){
            if($id_geo_address != $addresses[$i]->id){
                $addreses_modified[]=(object)$addresses[$i];
            }
        }
        $data['client']->addresses=$addreses_modified;
        try{
            $response = Request::post($uri)->sendsJson()->body(json_encode($data))->expectsJson()->send();
            return (($response->body && $response->body->response->responseCode===0));
        }catch(Exception $e){
            return false;
        }
    }
    /*
     * token => session token
     * customer_data => original customer data from ws
     * new_data => new address data to be inserted/updated
     * callcenter => if callcenter is loged in
     */
    //new data => matrix of address data
    public static function updateCustomerAddresses($token,$customer_data,$new_data,$callcenter=false){
        if(!$customer_data)return false;
        if(count($new_data)<1)return false;

        $uri = WS_BASE_URL.WS_UPDATE_CLIENT;

        $data=array('sessionToken'=>$token,'docNumber'=>'','docType'=>'','email'=>'','password'=>'');

        if(!$callcenter){
            $data['sourceType']='ECOMMERCE';
        }
        else{
            $data['sourceType']='DELIVERY';
        }
        $data['client']=$customer_data;
        if(isset($new_data[0]['country']))$data['client']->country=$new_data[0]['country'];//set country
        //set phone
        if(isset($new_data[0]['phone'])){
            //si no hay un telefono crear uno, si existe editar
            if(!$data['client']->phones[0]){
                $phone = (object)array('type'=>'CELL','number'=>$new_data[0]['phone']);
                $data['client']->phones[]=$phone;
            }else{
                $data['client']->phones[0]->number=$new_data[0]['phone'];
            }
        }
        $addresses= $data['client']->addresses;

        for($j=0;$j<count($new_data);$j++){

            $department= (object) array('id'=>$new_data[$j]['department']['id'],'name'=>$new_data[$j]['department']['name']);
            $neighborhood= (object) array('id'=>$new_data[$j]['neighborhood']['id'],'name'=>$new_data[$j]['neighborhood']['name']);
            $city= (object) array('id'=>$new_data[$j]['city']['id'],'name'=>$new_data[$j]['city']['name']);
            $complement=(isset($new_data[$j]['complement']))? $new_data[$j]['complement']:'';
            if(!$new_data[$j]['address_id']){
                //push new address
                $address=array('department'=>$department,'city'=>$city);
                if($new_data[$j]['neighborhood']['id'] && $new_data[$j]['neighborhood']['name']){
                    $address['neighborhood']=$neighborhood;
                }
                if($new_data[$j]["lat"] && $new_data[$j]["lng"]){
                    $address['longitude']=$new_data[$j]["lng"];
                    $address['latitude']=$new_data[$j]["lat"];
                }
                $address['streetName']=$new_data[$j]['streetName'];
                $address['streetNumber']=$new_data[$j]['streetNumber'];
                $address['complement']=$complement;
                $addresses[]=(object)$address;//push new address
            }else{
                //search and edit
                for($i=0;$i<count($addresses);$i++){
                    if($new_data[$j]['address_id']==$addresses[$i]->id){
                        $address=array('id'=>$new_data[$j]['address_id'],'department'=>$department,'city'=>$city);
                        if($new_data[$j]['neighborhood']['id'] && $new_data[$j]['neighborhood']['name']){
                            $address['neighborhood']=$neighborhood;
                        }
                        if($new_data[$j]["lat"] && $new_data[$j]["lng"]){
                            $address['longitude']=$new_data[$j]["lng"];
                            $address['latitude']=$new_data[$j]["lat"];
                        }
                        $address['streetName']=$new_data[$j]['streetName'];
                        $address['streetNumber']=$new_data[$j]['streetNumber'];
                        $address['complement']=$complement;
                        $addresses[$i]=(object)$address;
                    }
                }
            }
        }
        $data['client']->addresses=$addresses;
        try{
            $response = Request::post($uri)->sendsJson()->body(json_encode($data))->expectsJson()->send();
            return (($response->body && $response->body->response->responseCode===0));
        }catch(Exception $e){
            return false;
        }
    }



    public static function updateAccountInfo($token,$customer_data,$name,$middlename,$lastname,$dob,$gender){
        if(!$customer_data)return false;

        $uri = WS_BASE_URL.WS_UPDATE_CLIENT;
        $pgender=($gender==1)?"M":"F";

        $data=array('sessionToken'=>$token,'docNumber'=>'','docType'=>'','email'=>'','password'=>'');
        $data['sourceType']='ECOMMERCE';
        $data['client']=$customer_data;

        $data['client']->name=(object)array("firstName" => $name,"secondName"=> $middlename,"firstSurname" => $lastname, "secondSurname" =>"");
        $data['client']->gender=$pgender;
        $data['client']->birthdate = strtotime($dob)*1000;

        try{
            $response = Request::post($uri)->sendsJson()->body(json_encode($data))->expectsJson()->send();
            $response;
            return (($response->body && $response->body->response->responseCode===0)?true:false);
        }catch(Exception $e){
            return false;
        }
    }

    public static function syncGeoLoyalty($customer_id=null,$callcenter=false){
        //call geoloyalty to get user data
        $token=Mage::getSingleton('customer/session')->getData('session_token');
        $customerData=Mage::getModel('uruguayancustomer/customer')->getLoyaltyCustomerData($token,$callcenter);
        if(!$customerData)return false;

        $geoIds=array();//array for found ids to update and not create

        //check customer existance
        if($customer_id){
            $customer = Mage::getModel('customer/customer')->load($customer_id);
            $customerAddressCollection = Mage::getResourceModel('customer/address_collection')->addAttributeToFilter('parent_id',$customer->getId())->getItems();
            foreach($customerAddressCollection as $customerAddress){
                $corrupted=true;
                $customer_address_id = $customerAddress->getData('entity_id');
                $c=Mage::getModel('customer/address')->load($customer_address_id);
                $gAddressId=$c->getData('geo_address_id');
                //check for corrupted addresses and erase if any
                for($i=0;$i<count($customerData->addresses);$i++){
                    if($gAddressId==$customerData->addresses[$i]->id){
                        $geoIds[]=$gAddressId;
                       $corrupted=false;
                    }
                }
                if($corrupted){
                    $c->delete();
                }
            }

        }else{
            //if not exists crete new one
            $customer = Mage::getModel("customer/customer");
        }
        $gender=($customerData->gender=="M")?'1':'2';
        $websiteId = Mage::app()->getWebsite()->getId();
        $store = Mage::app()->getStore();
        $customer->setWebsiteId($websiteId)->setStore($store)->setGroupId(2)->setPrefix('')->setData('document_number',$customerData->clientId->number)
            ->setData('document_type',$customerData->clientId->type)->setGender($gender)->setFirstname($customerData->name->firstName)->setMiddleName($customerData->name->secondName)
            ->setLastname($customerData->name->firstSurname)->setSuffix('')->setEmail($customerData->email);
        //->setDob(Date("Y-m-d",$customerData->birthdate));
        if($customerData->birthdate){
            $bday = date( 'Y-m-d', $customerData->birthdate/1000);
            $customer->setDob($bday);
        }
        try{$customer->save();}
        catch (Exception $e) {
            Zend_Debug::dump($e->getMessage());
            return false;
        }
        $addresses=$customerData->addresses;
        foreach($addresses as $a){
            $isPrimary = false;
            if (isset($a->addressCategoryType) && $a->addressCategoryType == 'PRIMARY')
                $isPrimary = true;

            if(in_array($a->id,$geoIds)){//if is in array update, else create
                //$address = Mage::getModel('customer/address')->load($a->id, 'geo_address_id');
                $address = Mage::getResourceModel('customer/address_collection')
                    ->addAttributeToFilter('parent_id',$customer->getId())
                    ->addAttributeToFilter('geo_address_id',$a->id)
                    ->getFirstItem();
            }else{
                $address = Mage::getModel('customer/address');
            }
            //latitude and longitude sync
            $json_position=array();
            $json_position['latitude']=($a->longitude)?$a->latitude:DEFAULT_MAP_LATITUDE;
            $json_position['longitude']=($a->latitude)?$a->longitude:DEFAULT_MAP_LONGITUDE;
            $address->setData('position_json',json_encode($json_position));

            $address->setCustomerId($customer->getId())
                ->setFirstname($customer->getFirstname())
                ->setMiddleName($customer->getMiddlename())
                ->setLastname($customer->getLastname())
                ->setCountryId('UY')
                //->setPostcode('2100')
                ->setIsDefaultBilling($isPrimary)
                ->setIsDefaultShipping($isPrimary)
                ->setTelephone($customerData->phones[0]->number)
                ->setCompany('')
                ->setStreet($a->streetName)
                ->setSaveInAddressBook('1')
                ->setData('geo_address_id',$a->id)
                ->setData('city_id',$a->city->id)
                ->setData('city',$a->city->name)
                ->setData('region',$a->department->name)
                ->setData('geo_region_id',$a->department->id)
                ->setData('address_number',$a->streetNumber);
            if($a->neighborhood && $a->neighborhood->id){
                $address->setData('neighborhood',$a->neighborhood->name);
                $address->setData('neighborhood_id',$a->neighborhood->id);
            }
            if($a->complement){$address->setData('complement',$a->complement);}
            if(Mage::getModel('uruguayancustomer/customer')->isValidAddress($address)){
                try{$address->save();}
                catch (Exception $e) {Zend_Debug::dump($e->getMessage());
                }
            }
        }
        return $customer->getId();
    }

    public static function isValidAddress($address){
        $telephone=$address->getData('telephone');
        //$empty_tel=empty($telephone);
        $empty_tel=false;
        $street=$address->getData('street');
        $empty_street=empty($street);
        $country_id=$address->getData('country_id');
        $empty_country_id=!isset($country_id);
        $city_id=$address->getData('city_id');
        $empty_city_id=!isset($city_id);
        $city=$address->getData('city');
        $empty_city=empty($city);
        $region=$address->getData('region');
        $empty_region=empty($region);
        $region_id=$address->getData('geo_region_id');
        $empty_region_id=!isset($region_id);
        $address_number=$address->getData('address_number');
        $empty_address_number=empty($address_number);
        return (!$empty_region_id && !$empty_city_id &&!$empty_address_number && !$empty_city && !$empty_country_id && !$empty_region && !$empty_street && !$empty_tel);
    }

}
