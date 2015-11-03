<?php
require_once(Mage::getBaseDir('lib') . '/Httpful/Request.php');
require_once(Mage::getBaseDir('lib') . '/Httpful/Http.php');
require_once(Mage::getBaseDir('lib') . '/Httpful/Bootstrap.php');
use \Httpful\Request;
use \Httpful\Http;

require_once "Mage/Customer/controllers/AddressController.php";
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
 * Customer address controller
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
//require_once Mage::getModuleDir('controllers','Cualit_UruguayanCustomer').'/Customer/AddressController.php';
if(is_file(Mage::getModuleDir('controllers','Cualit_UruguayanCustomer').'/Customer/AddressController.php')){
    require_once Mage::getModuleDir('controllers','Cualit_UruguayanCustomer').'/Customer/AddressController.php';
}else{
    require_once 'Cualit/UruguayanCustomer/controllers/AddressController.php';
}
class Geocom_UruguayanCustomer_AddressController extends Cualit_UruguayanCustomer_AddressController
{
//    private $_isCClogged = null;
//
//    /**
//     * Retrieve customer session object
//     *
//     * @return Mage_Customer_Model_Session
//     */
//    protected function _getSession()
//    {
//        return Mage::getSingleton('customer/session');
//    }
//
//    public function preDispatch()
//    {
//        parent::preDispatch();
//
//        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
//            $this->setFlag('', 'no-dispatch', true);
//        }
//    }
//
//
//    public function _getIsCClogged()
//    {
//        if ($this->_isCClogged == null) {
//            $adminhtml = Mage::getModel('pulsestorm_crossareasession/manager')
//                ->getSessionData('adminhtml');
//            $adminUser = $adminhtml['admin']['user'];
//            $this->_isCClogged = isset($adminUser) && $adminUser->getId() && $adminUser->getIsActive();
//        }
//        return $this->_isCClogged;
//    }
//
//    /**
//     * Customer addresses list
//     */
//    public function indexAction()
//    {
//        if (count($this->_getSession()->getCustomer()->getAddresses())) {
//            $this->loadLayout();
//            $this->_initLayoutMessages('customer/session');
//            $this->_initLayoutMessages('catalog/session');
//
//            $block = $this->getLayout()->getBlock('address_book');
//            if ($block) {
//                $block->setRefererUrl($this->_getRefererUrl());
//            }
//            $this->renderLayout();
//        } else {
//            $this->getResponse()->setRedirect(Mage::getUrl('*/*/new'));
//        }
//    }
//
//    public function editAction()
//    {
//        $this->_forward('form');
//    }
//
//    public function newAction()
//    {
//        $this->_forward('form');
//    }
//
//    /**
//     * Address book form
//     */
//    public function formAction()
//    {
//
//        $this->loadLayout();
//        $this->_initLayoutMessages('customer/session');
//        $navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
//        if ($navigationBlock) {
//            $navigationBlock->setActive('customer/address');
//        }
//        //preload data
//        $addressId = $this->getRequest()->getParam('id');
//        /*if ($addressId) {
//            $address=Mage::getModel('customer/address')->load($addressId);
//            //sync address
//            $token=Mage::getSingleton('customer/session')->getData('session_token');
//            $customerData=Mage::getModel('uruguayancustomer/customer')->getLoyaltyCustomerData($token);
//            if(!$customerData){
//                $this->_getSession()->addError($this->__('Cannot save address.'));
//            }
//            //buscar el id del array para la direccion dependiendo si es billing o shipping
//            //save addresses
//            $address->setTelephone($customerData->phones[0]->number)
//                ->setStreet($customerData->addresses[0]->streetName)
//                ->setData('country_id','UY')->setData('city','')
//                ->setData('neighborhood','')->setData('region','')
//                ->setData('region_id','')->setData('address_number','')->setData('city_id','');
//                try{
//                    $address->save();
//                }
//                catch (Exception $e) {Zend_Debug::dump($e->getMessage());}
//        }*/
//        $this->renderLayout();
//    }
//
//    public function formPostAction()
//    {
//        if (!$this->_validateFormKey()) {
//            return $this->_redirect('*/*/');
//        }
//        $existsAddress = null;
//        // Save data
//        if ($this->getRequest()->isPost()) {
//            $customer = $this->_getSession()->getCustomer();
//            /* @var $address Mage_Customer_Model_Address */
//            $address = Mage::getModel('customer/address');
//            $addressId = $this->getRequest()->getParam('id');
//            if ($addressId) {
//                $existsAddress = $customer->getAddressById($addressId);
//                if ($existsAddress->getId() && $existsAddress->getCustomerId() == $customer->getId()) {
//                    $address->setId($existsAddress->getId());
//                }
//
//            }
//            $address->setFirstname($customer->getFirstname())->setLastname($customer->getLastname());
//            $errors = array();
//
//            /* @var $addressForm Mage_Customer_Model_Form */
//            $addressForm = Mage::getModel('customer/form');
//            $addressForm->setFormCode('customer_address_edit')
//                ->setEntity($address);
//            $addressData = $addressForm->extractData($this->getRequest());
//            $addressErrors = $addressForm->validateData($addressData);
//            if ($addressErrors !== true) {
//                $errors = $addressErrors;
//            }
//            $json_position = array();
//            if ($this->getRequest()->getParam('nearest_local')) {
//                $json_position['nearest_local'] = $this->getRequest()->getParam('nearest_local');
//            }
//            //set data
//            $new_data = array();
//
//            if ($this->getRequest()->getParam('latitude') && $this->getRequest()->getParam('longitude')) {
//                $json_position['latitude'] = $this->getRequest()->getParam('latitude');
//                $json_position['longitude'] = $this->getRequest()->getParam('longitude');
//                $new_data['lat'] = $this->getRequest()->getParam('latitude');
//                $new_data['lng'] = $this->getRequest()->getParam('longitude');
//            }
//            $address->setData('position_json', json_encode($json_position));
//            if ($this->getRequest()->getParam('complement')) {
//                $new_data['complement'] = $this->getRequest()->getParam('complement');
//            }
//            $new_data['phone_id'] = '0';
//            $new_data['streetNumber'] = $this->getRequest()->getParam('address_street_number');
//            $new_data['streetName'] = $this->getRequest()->getParam('street')[0];
//            $new_data['phone'] = $this->getRequest()->getParam('telephone');
//            $new_data['department']['id'] = $this->getRequest()->getParam('geo_region_id');
//            $new_data['department']['name'] = $this->getRequest()->getParam('region');
//            $new_data['neighborhood']['id'] = $this->getRequest()->getParam('neighborhood_id');
//            $new_data['neighborhood']['name'] = $this->getRequest()->getParam('neighborhood');
//            $new_data['city']['id'] = $this->getRequest()->getParam('city_id');
//            $new_data['city']['name'] = $this->getRequest()->getParam('city');
//            if ($existsAddress) {
//                $new_data['address_id'] = $existsAddress->getData('geo_address_id');
//            }
//            //check if delivery is loged in
//            $adminhtml = Mage::getModel('pulsestorm_crossareasession/manager')->getSessionData('adminhtml');
//            $adminUser = $adminhtml['admin']['user'];
//            $isAdminLoggedIn = isset($adminUser) && $adminUser->getId() && $adminUser->getIsActive();
//
//            $token = Mage::getSingleton('customer/session')->getData('session_token');
//            $customerData = Mage::getModel('uruguayancustomer/customer')->getLoyaltyCustomerData($token, $isAdminLoggedIn);
//            $geoAdressUpdated = Mage::getModel('uruguayancustomer/customer')->updateCustomerAddresses($token, $customerData, array($new_data), $isAdminLoggedIn);
//            if (!$geoAdressUpdated) {
//                $errors[] = $this->__('Cannot save address.');
//            } else {
//                //sync data
//                Mage::getModel('uruguayancustomer/customer')->syncGeoLoyalty($customer->getId(), $isAdminLoggedIn);
//            }
//            try {
//                $addressForm->compactData($addressData);
//                $address->setCustomerId($customer->getId())
//                    ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', null))
//                    ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));
//
//                $addressErrors = $address->validate();
//                if ($addressErrors !== true) {
//                    $errors = array_merge($errors, $addressErrors);
//                }
//                if (count($errors) === 0 && $geoAdressUpdated) {
//                    $address->save();
//                    $this->_getSession()->addSuccess($this->__('The address has been saved.'));
//                    $this->_redirectSuccess(Mage::getUrl('*/*/index', array('_secure' => true)));
//                    return;
//                } else {
//                    $this->_getSession()->setAddressFormData($this->getRequest()->getPost());
//                    foreach ($errors as $errorMessage) {
//                        $this->_getSession()->addError($errorMessage);
//                    }
//                }
//            } catch (Mage_Core_Exception $e) {
//                $this->_getSession()->setAddressFormData($this->getRequest()->getPost())
//                    ->addException($e, $e->getMessage());
//            } catch (Exception $e) {
//                $this->_getSession()->setAddressFormData($this->getRequest()->getPost())
//                    ->addException($e, $this->__('Cannot save address.'));
//            }
//        }
//
//        return $this->_redirectError(Mage::getUrl('*/*/edit', array('id' => $address->getId())));
//    }
//
//    public function deleteAction()
//    {
//        $addressId = $this->getRequest()->getParam('id', false);
//
//        if ($addressId) {
//            $address = Mage::getModel('customer/address')->load($addressId);
//            $geoId = $address->getData('geo_address_id');
//            // Validate id geo sync
//            if (!$geoId) {
//                $this->_getSession()->addError($this->__('An error occurred while deleting the address.'));
//                $this->getResponse()->setRedirect(Mage::getUrl('*/*/index'));
//                return;
//            }
//            // Validate address_id <=> customer_id
//            if ($address->getCustomerId() != $this->_getSession()->getCustomerId()) {
//                $this->_getSession()->addError($this->__('The address does not belong to this customer.'));
//                $this->getResponse()->setRedirect(Mage::getUrl('*/*/index'));
//                return;
//            }
//
//            try {
//                $adminhtml = Mage::getModel('pulsestorm_crossareasession/manager')->getSessionData('adminhtml');
//                $adminUser = $adminhtml['admin']['user'];
//                $isAdminLoggedIn = isset($adminUser) && $adminUser->getId() && $adminUser->getIsActive();
//
//                $token = Mage::getSingleton('customer/session')->getData('session_token');
//                $customerData = Mage::getModel('uruguayancustomer/customer')->getLoyaltyCustomerData($token, $isAdminLoggedIn);
//                $geoAdressUpdated = Mage::getModel('uruguayancustomer/customer')->deleteCustomerAddress($token, $customerData, $geoId, $isAdminLoggedIn);
//                if (!$geoAdressUpdated) {
//                    $this->_getSession()->addError($this->__('An error occurred while deleting the address.'));
//                    $this->getResponse()->setRedirect(Mage::getUrl('*/*/index'));
//                    return;
//                } else {
//                    //sync data
//                    $address->delete();
//                    Mage::getModel('uruguayancustomer/customer')->syncGeoLoyalty($this->_getSession()->getCustomerId(), $isAdminLoggedIn);
//                }
//
//                $this->_getSession()->addSuccess($this->__('The address has been deleted.'));
//            } catch (Exception $e) {
//                $this->_getSession()->addException($e, $this->__('An error occurred while deleting the address.'));
//            }
//        }
//        $this->getResponse()->setRedirect(Mage::getUrl('*/*/index'));
//    }
//
//    /*
//     * Get data address by ID (Ajax)
//     */
//    public function getAddressDataAction()
//    {
//        $request = $this->getRequest();
//        if ($request->isAjax()) {
//            $addressId = $request->getParam('id', false);
//            if ($addressId) {
//                $address = Mage::getModel('customer/address')->load($addressId);
//                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($address->getData()));
//            }
//        }
//        return;
//    }
//
//    /*
//     * Submit Local popup for Delivery
//     */
//    public function submitLocalPopupAction()
//    {
//
//        if (!$this->_getIsCClogged()) {
//            $this->_redirect("/*");
//            return;
//        }
//
//        $request = $this->getRequest();
//
//        if (!$request->isPost()) {
//            $this->_redirect("/*");
//            return;
//        }
//
//        $session = $this->_getSession();
//
//        if ($session->getIsLocalDefined() === true) {
//            $this->_redirect("/*");
//            return;
//        }
//
//
//        $isUserLogged = $session->isLoggedIn();
//
//
//        if (isset($isUserLogged) && $isUserLogged === true) {
//
//            //update or save address (sync)
//            $customer = $this->_getSession()->getCustomer();
//            /* @var $address Mage_Customer_Model_Address */
//            $address = Mage::getModel('customer/address');
//            $addressId = $this->getRequest()->getParam('address_select');
//            if ($addressId) {
//                if ($addressId != -1) {
//                    $existsAddress = $customer->getAddressById($addressId);
//                    if ($existsAddress->getId() && $existsAddress->getCustomerId() == $customer->getId()) {
//                        $address->setId($existsAddress->getId());
//                    }
//                }
//            }
//            $address->setFirstname($customer->getFirstname())->setLastname($customer->getLastname());
//            $errors = array();
//
//            /* @var $addressForm Mage_Customer_Model_Form */
//            $addressForm = Mage::getModel('customer/form');
//            $addressForm->setFormCode('customer_address_edit')
//                ->setEntity($address);
//            $addressData = $addressForm->extractData($this->getRequest());
//            $addressErrors = $addressForm->validateData($addressData);
//            if ($addressErrors !== true) {
//                $errors = $addressErrors;
//            }
//            $json_position = array();
//            if ($this->getRequest()->getParam('local_select')) {
//                $json_position['local_select'] = $this->getRequest()->getParam('local_select');
//            }
//
//            //set data
//            $new_data = array();
//
//            if ($this->getRequest()->getParam('latitude') && $this->getRequest()->getParam('longitude')) {
//                $json_position['latitude'] = $this->getRequest()->getParam('latitude');
//                $json_position['longitude'] = $this->getRequest()->getParam('longitude');
//                $new_data['lat'] = $this->getRequest()->getParam('latitude');
//                $new_data['lng'] = $this->getRequest()->getParam('longitude');
//            }
//            $address->setData('position_json', json_encode($json_position));
//            if ($this->getRequest()->getParam('complement')) {
//                $new_data['complement'] = $this->getRequest()->getParam('complement');
//            }
//            $new_data['phone_id'] = '0';
//            $new_data['streetNumber'] = $this->getRequest()->getParam('address_street_number');
//            $new_data['streetName'] = $this->getRequest()->getParam('street')[0];
//            $new_data['phone'] = $this->getRequest()->getParam('telephone');
//            $new_data['department']['id'] = $this->getRequest()->getParam('geo_region_id');
//            $new_data['department']['name'] = $this->getRequest()->getParam('region');
//            $new_data['neighborhood']['id'] = $this->getRequest()->getParam('neighborhood_id');
//            $new_data['neighborhood']['name'] = $this->getRequest()->getParam('neighborhood');
//            $new_data['city']['id'] = $this->getRequest()->getParam('city_id');
//            $new_data['city']['name'] = $this->getRequest()->getParam('city');
//            if ($existsAddress) {
//                $new_data['address_id'] = $existsAddress->getData('geo_address_id');
//            } else {
//                $customerAddresses = Mage::getResourceModel('customer/address_collection')->addAttributeToFilter('parent_id',$customer->getId())->getItems();
//            }
//
//            $token = $session->getData('session_token');
//            $customerData = Mage::getModel('uruguayancustomer/customer')->getLoyaltyCustomerData($token, $this->_getIsCClogged());
//            $geoAdressUpdated = Mage::getModel('uruguayancustomer/customer')->updateCustomerAddresses($token, $customerData, array($new_data), $this->_getIsCClogged());
//            if (!$geoAdressUpdated) {
//                $errors[] = $this->__('Cannot save address.');
//            } else {
//                //sync data
//                Mage::getModel('uruguayancustomer/customer')->syncGeoLoyalty($customer->getId(), $this->_getIsCClogged());
//                if(!$existsAddress){
//
//                    $newCustomerAddresses = Mage::getResourceModel('customer/address_collection')->addAttributeToFilter('parent_id',$customer->getId())->getItems();
//                    $newAddress = reset(array_diff_key($newCustomerAddresses,$customerAddresses));
//                }
//
//                $idLocal = $request->getParam('local_select');
//                if (!isset($idLocal) || $idLocal == '') {
//                    $errors[] = $this->__('Falta ingresar local.');
//                } else {
//                    //save address and local ids in session
//                    $session->setDeliveryAddressId($addressId == -1 && !$existsAddress? $newAddress->getId(): $addressId);
//                    $session->setDeliveryLocalId($idLocal);
//                    $session->setIsLocalDefined(true);
//                }
//
//            }
//            try {
//                $addressForm->compactData($addressData);
//                $address->setCustomerId($customer->getId())
//                    ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', null))
//                    ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));
//
//                $addressErrors = $address->validate();
//                if ($addressErrors !== true) {
//                    $errors = array_merge($errors, $addressErrors);
//                }
//                if (count($errors) === 0 && $geoAdressUpdated) {
//                    $address->save();
//                    $this->_getSession()->addSuccess($this->__('The address has been saved.'));
//                    $this->_redirectSuccess(Mage::getUrl('/*', array('_secure' => true)));
//                    return;
//                } else {
//                    $this->_getSession()->setAddressFormData($this->getRequest()->getPost());
//                    foreach ($errors as $errorMessage) {
//                        $this->_getSession()->addError($errorMessage);
//                    }
//                }
//            } catch (Mage_Core_Exception $e) {
//                $this->_getSession()->setAddressFormData($this->getRequest()->getPost())
//                    ->addException($e, $e->getMessage());
//            } catch (Exception $e) {
//                $this->_getSession()->setAddressFormData($this->getRequest()->getPost())
//                    ->addException($e, $this->__('Cannot save address.'));
//            }
//
//
//        }
//        return $this->_redirectError(Mage::getUrl('/*'));
//    }

}
