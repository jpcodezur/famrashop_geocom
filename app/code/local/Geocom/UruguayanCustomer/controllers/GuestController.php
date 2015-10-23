<?php
require_once(Mage::getBaseDir('lib') . '/Httpful/Request.php');
require_once(Mage::getBaseDir('lib') . '/Httpful/Http.php');
require_once(Mage::getBaseDir('lib') . '/Httpful/Bootstrap.php');
use \Httpful\Request;
use \Httpful\Http;

/**
 * Created by PhpStorm.
 * User: Andres
 * Date: 9/8/15
 * Time: 5:13 PM
 */
class Geocom_UruguayanCustomer_GuestController extends Mage_Core_Controller_Front_Action
{
    private $_isCCLogged = null;
    private $_isGuestLogged = null;

    public function preDispatch()
    {
        parent::preDispatch();

        //if callcenter isnt logged or a customer is logged, no-dispatch
        if (!$this->getIsCCLogged() || $this->_getSession()->isLoggedIn()) {
            $session = $this->_getSession();

            if ($this->getIsGuestLogged()) {
                $session->unsIsGuestLogged();
                $session->unsGuestData();
                $session->unsIsLocalDefined();
                $session->unsDeliveryLocalId();
            }

            $this->setFlag('', 'no-dispatch', true);
            $this->_redirect('/*'); //to index
        }
    }

    public function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    public function getIsCCLogged()
    {
        if ($this->_isCCLogged == null) {
            $adminhtml = Mage::getModel('pulsestorm_crossareasession/manager')
                ->getSessionData('adminhtml');
            $adminUser = $adminhtml['admin']['user'];
            $this->_isCCLogged = isset($adminUser) && $adminUser->getId() && $adminUser->getIsActive();
        }
        return $this->_isCCLogged;
    }

    public function getIsGuestLogged()
    {
        if ($this->_isGuestLogged == null) {
            $isGuestLogged = $this->_getSession()->getIsGuestLogged();
            $this->_isGuestLogged = isset($isGuestLogged) && $isGuestLogged;
        }
        return $this->_isGuestLogged;
    }

    public function loginAction()
    {

        $session = $this->_getSession();

        if ($session->isLoggedIn() || $this->_isGuestLogged) {
            //redirect to index
            $this->_redirect("/*");
            return;
        }

        $session->setIsGuestLogged(true);
        $this->_redirect("/*");

        return;
    }

    public function logoutAction()
    {
        if ($this->getIsGuestLogged()) {
            $session = $this->_getSession();

            $session->unsIsGuestLogged();
            $session->unsGuestData();
            $session->unsIsLocalDefined();
            $session->unsDeliveryLocalId();

            $cart = Mage::getSingleton('checkout/cart');
            $cart->truncate();
            $cart->save();

        }
        $this->_redirect("/*");
        return;
    }

    public function submitLocalPopupAction()
    {

        $request = $this->getRequest();

        if (!$request->isPost()) {
            $this->_redirect("/*");
            return;
        }

        if (!$this->getIsGuestLogged()) {
            $this->_redirect("/*");
            return;
        }

        //validate guest form basic data (name, phone, etc) and local

        $guestDataRequest = $request->get('guest');

        if (!isset($guestDataRequest)) {
            $this->_redirect("/*");
            return;
        }

        $guestName = $request->get('guest')['name'];
        $guestLastName = $request->get('guest')['lastName'];
        $idLocal = $request->getParam('local_select');

        if (!isset($guestName) || $guestName === '' || !isset($guestLastName) || $guestLastName === '' || !isset($idLocal) || $idLocal == '') {
            $this->_redirect("/*");
            return;
        }

        //save guest basic data in session
        $guestData['name'] = $guestName;
        $guestData['lastname'] = $guestLastName;

        //requeridos
        $addressData['complete_address'] = $request->getParam('complete_address');
        $addressData['country_id'] = $request->getParam('country_id');
        $addressData['geo_region_id'] = $request->getParam('geo_region_id');
        $addressData['region'] = $request->getParam('region');
        $addressData['city_id'] = $request->getParam('city_id');
        $addressData['city'] = $request->getParam('city');
        $addressData['street'] = $request->getParam('street');
        $addressData['address_corner'] = $request->getParam('address_corner');
        $addressData['address_number'] = $request->getParam('address_street_number');
        $addressData['latitude'] = $request->getParam('latitude');
        $addressData['longitude'] = $request->getParam('longitude');
        $addressData['nearest_local'] = $request->getParam('local_select');

        if (!$this->validateAddressData($addressData)) {
            $this->_redirect('/*');
            return;
        }

        //no requeridos
        $addressData['neighborhood'] = $request->getParam('neighborhood');
        $addressData['neighborhood_id'] = $request->getParam('neighborhood_id');
        $addressData['complement'] = $request->getParam('complement');
        $addressData['telephone'] = $request->getParam('telephone');


        $session = $this->_getSession();

        $guestData['address_data'] = $addressData;

        $session->setGuestData($guestData);


        //save address and local ids in session
        $session->setDeliveryLocalId($idLocal);
        $session->setIsLocalDefined(true);


        //redirect to index
        $this->_redirect('/*');
        return;

    }

    private function validateAddressData($addressData)
    {
        foreach ($addressData as $data) {
            if (!isset($data) || $data == '')
                return false;
        }
        return true;
    }
}
