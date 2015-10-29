<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 10/08/15
 * Time: 15:19
 */
require_once(Mage::getBaseDir().DS.'includes'.DS.'config.php');

class Geocom_UruguayanCustomer_Block_Page_Html_Header extends Mage_Page_Block_Html_Header
{
    private $isLocalNeeded = null;
    private $isGuestSession = null;

    public function _construct()
    {
        $this->setTemplate('uruguayancustomer/page/html/header.phtml');
    }

    public function _getSession(){
        return Mage::getSingleton('customer/session');
    }

    public function getIsLocalNeeded()
    {
        if ($this->isLocalNeeded == null) {
            if ($this->isCCAdminLoggedIn()) {
                $session = $this->_getSession();
                $isUserLogged = $session->isLoggedIn();

                if (!isset($isUserLogged) || !$isUserLogged) {
                    $isGuestLogged = $session->getIsGuestLogged();
                    $isGuestSession = isset($isGuestLogged) && $isGuestLogged;
                }

                if ($isGuestSession || $isUserLogged) {
                    $isLocalDefined = $session->getIsLocalDefined();
                    $this->isLocalNeeded = !isset($isLocalDefined) || $isLocalDefined === false;
                }
            } else {
                $this->isLocalNeeded = false;
            }
        }else {
            $this->isLocalNeeded = true;
        }
        return $this->isLocalNeeded;
    }

    public function getAddressesHtmlSelect()
    {
            $options = array(array('value' => -1, 'label' => Mage::helper('checkout')->__('New Address')));
            $session = $this->_getSession();
            $customer = $session->getCustomer();
            $defaultShipping = $customer->getDefaultShippingAddress();
            $defaultBilling = $customer->getDefaultBillingAddress();
            $defaultAddressId = false;
            if ($defaultShipping) {
                $defaultAddressId =  $customer->getDefaultShippingAddress()->getId();
            }
            if($session->isLoggedIn()){
            foreach ($session->getCustomer()->getAddresses() as $address) {
                $options[] = array(
                    'value' => $address->getData('entity_id'),
                    'label' => $address->format('oneline')
                );
            }


            $select = $this->getLayout()->createBlock('core/html_select')

                ->setName('address_select')
                ->setId('address-select')
                ->setClass('address-select')
                //->setExtraParams('onchange="'.$type.'.newAddress(!this.value)"')
                ->setValue(!$defaultAddressId?'':$defaultAddressId) //default address
                ->setOptions($options);


                //->addOption('', Mage::helper('checkout')->__('New Address'));

            return $select->getHtml();
            }
        return '';
    }

    public function getLocalsHtmlSelect(){

        $options = array();

            $locals = unserialize(FARMASHOP_LOCALES);
            $options[] = array('value' => '', 'label' => '' );
            foreach ($locals as $id => $local) {
                $options[] = array(
                    'value' => $id,
                    'label' => $local
                );
            }

            $select = $this->getLayout()->createBlock('core/html_select')
                ->setName('local_select')
                ->setId('local-select')
                ->setClass('local-select validate-select')
                //->setExtraParams('onchange="'.$type.'.newAddress(!this.value)"')
                //->setValue() //default address?
                ->setOptions($options);

            return $select->getHtml();

        return '';

    }


    public function getIsGuestSession() {
        if($this->isGuestSession == null){
            $isGuestLogged = $this->_getSession()->getIsGuestLogged();
            $this->isGuestSession = isset($isGuestLogged) && $isGuestLogged;
        }
        return $this->isGuestSession;
    }

    public function isCCAdminLoggedIn(){
        $adminhtml  = Mage::getModel('pulsestorm_crossareasession/manager')->getSessionData('adminhtml');
        $adminUser = $adminhtml['admin']['user'];
        $adminLoggedIn = isset($adminUser) && $adminUser->getId() && $adminUser->getIsActive();
        return ($adminLoggedIn);
        // Ensure we're in the admin session namespace for checking the admin user..
    }

    public function getLogoutUrl(){
        $key=Mage::getSingleton('adminhtml/url')->getSecretKey();
        $url=$this->getUrl("adminhtml/index/logout/key/".$key);
        return $url;
    }

    public function getCCAdminName(){
        $adminhtml  = Mage::getModel('pulsestorm_crossareasession/manager')->getSessionData('adminhtml');
        $adminUser = $adminhtml['admin']['user'];
        return $adminUser->getData("username");
    }

    public function getGuestName(){
        if($this->getIsGuestSession()){
            if($this->_getSession()->getIsLocalDefined() === true )
                return $this->_getSession()->getGuestData()['name'];
        }
        return false;
    }




}