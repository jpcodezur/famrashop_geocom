<?php
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
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Onepage checkout block
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Geocom_UruguayanCustomer_Block_Onepage extends Mage_Checkout_Block_Onepage
{

    public function getSteps()
    {
        $steps = array();

        $adminhtml = Mage::getModel('pulsestorm_crossareasession/manager')
            ->getSessionData('adminhtml');
        $adminUser = $adminhtml['admin']['user'];
        $loggedIn = isset($adminUser) && $adminUser->getId() && $adminUser->getIsActive();

        if (!$loggedIn) {
            if (!$this->isCustomerLoggedIn()) {
                $steps['login'] = $this->getCheckout()->getStepData('login');
            }
        } else {
            Mage::getSingleton('checkout/type_onepage')->saveCheckoutMethod('guest');
        }

        $stepCodes = array('billing', 'shipping', 'shipping_method', 'payment', 'review');

        /*if ($loggedIn) {
            $stepCodes = array_merge(['farmalocal'], $stepCodes);
        }*/

        foreach ($stepCodes as $step) {
            $steps[$step] = $this->getCheckout()->getStepData($step);
        }
        return $steps;
    }

    /**
     * Get active step
     *
     * @return string
     */
    public function getActiveStep()
    {
        $adminhtml = Mage::getModel('pulsestorm_crossareasession/manager')
            ->getSessionData('adminhtml');
        $adminUser = $adminhtml['admin']['user'];
        $adminLoggedIn = isset($adminUser) && $adminUser->getId() && $adminUser->getIsActive();


        if ($adminLoggedIn) {
            // return $this->isCustomerLoggedIn() ? 'farmalocal' : 'login';

            $session = Mage::getsingleton('customer/session');

            $isLocalDefined = $session->getIsLocalDefined();

            if (isset($isLocalDefined) && $isLocalDefined == true) {

                if ($this->isCustomerLoggedIn()) {

                    $onePage = Mage::getSingleton('checkout/type_onepage');

                    //$deliveryAddressId = $session->getDeliveryAddressId();
                    $deliveryLocalId = $session->getDeliveryLocalId();

                    if(!$deliveryLocalId) {
                        $session->setIsLocalDefined(false);
                        Mage::app()->getResponse()->setRedirect('/');
                        return;
                    }


                    $onePage->saveFarmalocal($deliveryLocalId);
                    $onePage->getQuote()->setCheckoutMethod('guest');
                    //$onePage->saveBilling(Mage::getModel('customer/address')->load($deliveryAddressId)->toArray(), false);
                    //$onePage->saveShipping(Mage::getModel('customer/address')->load($deliveryAddressId)->toArray(), false);
                    //$onePage->saveShippingMethod(Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->getShippingMethod());
                    return 'billing';

                } else {

                    $isGuestLogged = $session->getIsGuestLogged();
                    $guestData = $session->getGuestData();

                    if (isset($isGuestLogged) && $isGuestLogged == true && isset($guestData) && isset($guestData['address_data'])) {

                        $data = $guestData['address_data'];
                        $data['firstname'] = $session->getGuestData()['name'];
                        $data['lastname'] = $session->getGuestData()['lastname'];
                        $data['farmalocal'] = $session->getDeliveryLocalId();

                        if($data['lastname']==''){$data['lastname']=GUEST_DEFAULT_LASTNAME;}
                        if($data['postcode']==''){$data['postcode']=GUEST_DEFAULT_ZIPCODE;}
                        if($data['email']==''){$data['email']=GUEST_DEFAULT_EMAIL;}
                        if($data['document_number']==''){$data['document_number']=GUEST_DEFAULT_DOCUMENT;}
                        if($data['document_type']==''){$data['document_type']='CI';}
                        if($data['day']==''){$data['day']='1';}
                        if($data['month']==''){$data['month']='1';}
                        if($data['year']==''){$data['year']='2000';}
                        if($data['dob']==''){$data['dob']='2000-01-01';}
                        if($data['gender']==''){$data['gender']="1";}

                        $onePage = Mage::getSingleton('checkout/type_onepage');

                        $onePage->saveFarmalocal($data['farmalocal']);
                        //$onePage->saveBilling($data, false);
                        //$onePage->saveShipping($data, false);

                        return 'billing';
                    }
                }
            } else {
                Mage::app()->getResponse()->setRedirect('/');
            }

        }

        return $this->isCustomerLoggedIn() ? 'billing' : 'login';
    }
}
