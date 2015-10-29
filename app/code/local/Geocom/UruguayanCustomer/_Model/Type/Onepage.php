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
 * OnePage Checkout model
 *
 * @category    Cualit
 * @package     UruguayanCustomer
 * @author      Cualit Core Team <fperez@cualit.com>
 */
class Geocom_UruguayanCustomer_Model_Type_Onepage extends Mage_Checkout_Model_Type_Onepage
{

    /*public function saveBilling($data, $customerAddressId)
    {
        parent::saveBilling($data, $customerAddressId);
    }*/

    public function saveFarmalocal($data){
        if (empty($data)) {
            return array('error' => -1, 'message' => $this->_helper->__('Invalid data.'));
        }

        $adminhtml  = Mage::getModel('pulsestorm_crossareasession/manager')
            ->getSessionData('adminhtml');
        $adminUser = $adminhtml['admin']['user'];
        $adminLoggedIn = isset($adminUser) && $adminUser->getId() && $adminUser->getIsActive();

        if ($adminLoggedIn) {
            Mage::getSingleton('admin/session')->setFarmashopLocal($data['local']);
        }

        $this->getQuote()->setFarmashopLocal($data['local']);
        $this->getQuote()->collectTotals();
        $this->getQuote()->save();

        $this->getCheckout()
            ->setStepData('farmalocal', 'allow', true)
            ->setStepData('farmalocal', 'complete', true)
            ->setStepData('billing', 'allow', true);

        return array();
    }
    /**
     * Create order based on checkout type. Create customer if necessary.
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    public function saveOrder()
    {
        $this->validate();
        $isNewCustomer = false;
        switch ($this->getCheckoutMethod()) {
            case self::METHOD_GUEST:
                $this->_prepareGuestQuote();
                break;
            case self::METHOD_REGISTER:
                $this->_prepareNewCustomerQuote();
                $isNewCustomer = true;
                break;
            default:
                $this->_prepareCustomerQuote();
                break;
        }
        $service = Mage::getModel('sales/service_quote', $this->getQuote());
        $result=true;
        if($isNewCustomer){
            $result=$this->createGeoloyaltyCustomer($this->getQuote()->getCustomer()->getData(),$this->getQuote()->getBillingAddress()->getData());
        }else{
            //update if new address -billing only or check if shipping also needs to be updated
            $billing_address=$this->getQuote()->getBillingAddress()->getData();
            $shipping_address=$this->getQuote()->getShippingAddress()->getData();
            //get long and lat from addresses
            $new_address_data=array();
            if(empty($billing_address['customer_address_id'])){
                $address=$this->extractBillingAddressData($billing_address);
                $new_address_data[]=$address;//psuh data

            }
            if(empty($shipping_address['customer_address_id'])){
                $address=$this->extractShippingAddressData($shipping_address);
                $new_address_data[]=$address;//psuh data

            }
            if(count($new_address_data)>0){
                //if new addresses are set , update geo client address data
                $adminhtml  = Mage::getModel('pulsestorm_crossareasession/manager')->getSessionData('adminhtml');
                $adminUser = $adminhtml['admin']['user'];
                $isAdminLoggedIn = isset($adminUser) && $adminUser->getId() && $adminUser->getIsActive();
                $token=Mage::getSingleton('customer/session')->getData('session_token');
                $customerData=Mage::getModel('uruguayancustomer/customer')->getLoyaltyCustomerData($token,$isAdminLoggedIn);
                $geoAdressUpdated=Mage::getModel('uruguayancustomer/customer')->updateCustomerAddresses($token,$customerData,$new_address_data,$isAdminLoggedIn);
                if($geoAdressUpdated){
                    //sync data
                    Mage::getModel('uruguayancustomer/customer')->syncGeoLoyalty($this->getQuote()->getCustomer()->getId(),$isAdminLoggedIn);
                }
            }


        }
        if(is_array($result)){
            return $result;
        }else if($result){
            $service->submitAll();
        }else{
            return false;
        }

        if ($isNewCustomer) {
            try {
                $this->_involveNewCustomer();
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        $this->_checkoutSession->setLastQuoteId($this->getQuote()->getId())
            ->setLastSuccessQuoteId($this->getQuote()->getId())
            ->clearHelperData();

        $order = $service->getOrder();
        if ($order) {
            Mage::dispatchEvent('checkout_type_onepage_save_order_after',
                array('order'=>$order, 'quote'=>$this->getQuote()));

            /**
             * a flag to set that there will be redirect to third party after confirmation
             * eg: paypal standard ipn
             */
            $redirectUrl = $this->getQuote()->getPayment()->getOrderPlaceRedirectUrl();
            /**
             * we only want to send to customer about new order when there is no redirect to third party
             */
            if (!$redirectUrl && $order->getCanSendNewEmailFlag()) {
                try {
                    $order->sendNewOrderEmail();
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }

            // add order information to the session
            $this->_checkoutSession->setLastOrderId($order->getId())
                ->setRedirectUrl($redirectUrl)
                ->setLastRealOrderId($order->getIncrementId());

            // as well a billing agreement can be created
            $agreement = $order->getPayment()->getBillingAgreement();
            if ($agreement) {
                $this->_checkoutSession->setLastBillingAgreementId($agreement->getId());
            }
        }

        // add recurring profiles information to the session
        $profiles = $service->getRecurringPaymentProfiles();
        if ($profiles) {
            $ids = array();
            foreach ($profiles as $profile) {
                $ids[] = $profile->getId();
            }
            $this->_checkoutSession->setLastRecurringProfileIds($ids);
            // TODO: send recurring profile emails
        }

        Mage::dispatchEvent(
            'checkout_submit_all_after',
            array('order' => $order, 'quote' => $this->getQuote(), 'recurring_profiles' => $profiles)
        );

        return $this;
    }

    private function extractBillingAddressData($billing_address)
    {
        $new_data=array();
        $new_data['phone_id']='0';
        if($billing_address['position_json']){
            $json_pos=json_decode($billing_address['position_json']);
            if($json_pos->latitude && $json_pos->longitude){
                $new_data['lat']=$json_pos->latitude;
                $new_data['lng']=$json_pos->longitude;
            }

        }
        $new_data['complement']=($billing_address['complement'])?$billing_address['complement']:"";
        $new_data['streetNumber']=$billing_address['address_number'];
        $new_data['streetName']=$billing_address['street'];
        $new_data['phone']=$billing_address['telephone'];
        $new_data['department']['id']=$billing_address['geo_region_id'];
        $new_data['department']['name']=$billing_address['region'];
        $new_data['neighborhood']['id']=$billing_address['neighborhood_id'];
        $new_data['neighborhood']['name']=$billing_address['neighborhood'];
        $new_data['city']['id']=$billing_address['city_id'];
        $new_data['city']['name']=$billing_address['city'];
        return $new_data;
    }

    private function extractShippingAddressData($shipping_address)
    {
        $new_data=array();
        $new_data['phone_id']='0';
        if($shipping_address['position_json']){
            $json_pos=json_decode($shipping_address['position_json']);
            if($json_pos->latitude && $json_pos->longitude){
                $new_data['lat']=$json_pos->latitude;
                $new_data['lng']=$json_pos->longitude;
            }
        }
        $new_data['complement']=($shipping_address['complement'])?$shipping_address['complement']:"";
        $new_data['streetNumber']=$shipping_address['address_number'];
        $new_data['streetName']=$shipping_address['street'];
        $new_data['phone']=$shipping_address['telephone'];
        $new_data['department']['id']=$shipping_address['geo_region_id'];
        $new_data['department']['name']=$shipping_address['region'];
        $new_data['neighborhood']['name']=$shipping_address['neighborhood'];
        $new_data['neighborhood']['id']=$shipping_address['neighborhood_id'];
        $new_data['city']['id']=$shipping_address['city_id'];
        $new_data['city']['name']=$shipping_address['city'];
        return $new_data;
    }


    private function createGeoloyaltyCustomer($data,$addressData) {

        $uri = WS_BASE_URL.WS_SIGN_UP;

        $dobNoFormat=$data['dob'];
        $dobNoFormat = new DateTime($dobNoFormat);
        $dob=date_format($dobNoFormat, 'Y-m-d');
        $docNumber= $data['document_number'];
        $docType=$data['document_type'];
        $mname= isset($data['customer_middle_name']) ? $data['customer_middle_name'] : "";
        $email= $data['email'];
        $fname= $data['firstname'];
        $lname= $data['lastname'];
        $password= $data['password'];
        $gender=$data['gender'];
        $gender_char=($gender==1)?"M":"F";
        $country=$addressData['country_id'];
        $street= $addressData['street'];
        $addressNumber= $addressData['address_number'];
        //$addressCorner= $addressData['address_corner']; //no la están guardando ellos
        $telephone= isset($addressData['telephone']) ? $addressData['telephone']:'' ;
        $neighborhoodId=isset($addressData['neighborhood_id'])? $addressData['neighborhood_id'] : '' ;
        $cityId=$addressData['city_id'];
        $departmentId=isset($addressData['geo_region_id'])? $addressData['geo_region_id']:$addressData['region_id'] ;

        $data = array(
            'docType' => $docType,
            'docNumber' => $docNumber,
            'firstName' => $fname,
            'firstSurname' => $lname,
            'gender'=>$gender_char,
            'country'=>$country,
            'secondName'=>$mname,
            'email'=>$email,
            'birthdate'=>$dob,
            'telephones'=>array(array("type"=>"CELL","number"=>$telephone)),
            'addresses'=>array(array("streetName"=>$street,"streetNumber"=>$addressNumber,"city"=>array("id"=>$cityId),"department"=>array("id"=>$departmentId))),
            'businessUnitId'=>"1",
            "requiredOutlineProcess"=>"false",
            "password"=>$password,
            "sourceType"=>"ECOMMERCE"
        );
        if(isset($neighborhoodId)&&!empty($neighborhoodId)){
            $data['addresses'][0]["neighborhood"]=array("id"=>$neighborhoodId);
        }

        try{
            $response = Request::post($uri)->sendsJson()->body(json_encode($data))->expectsJson()->send();
            if($response->body && $response->body->response->responseCode == 0){
                return true;//User Afilliated OK.
            }else if($response->body && ($response->body->response->responseCode == WS_GEO_ALREADY_SAME_EMAIL || $response->body->response->responseCode == WS_GEO_ALREADY_AFFILIATED_RESPONSE_CODE)){
                return array('result'=>false,'msg'=>'Cliente ya registrado.');
            }
            else if($response->body && ($response->body->response->responseCode == WS_GEO_INVALID_DOC_NUMBER_RESPONSE_CODE)){
                return array('result'=>false,'msg'=>'Documento inválido.');
            }
        }catch(Exception $e){
            return false;
        }
        return false;
    }



}
