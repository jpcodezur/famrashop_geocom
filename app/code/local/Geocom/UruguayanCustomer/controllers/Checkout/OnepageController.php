<?php
require_once 'Mage/Checkout/controllers/OnepageController.php';
require_once Mage::getModuleDir('controllers','Cualit_UruguayanCustomer').'/Checkout/OnepageController.php';

class Geocom_UruguayanCustomer_Checkout_OnepageController extends Cualit_UruguayanCustomer_Checkout_OnepageController
{
    
    public function savePaymentAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        try {
            if (!$this->getRequest()->isPost()) {
                $this->_ajaxRedirectResponse();
                return;
            }

            $data = $this->getRequest()->getPost('payment', array());
            $card_digits = $this->getRequest()->getPost('payment[credit_card_iin]', null);
            $card_type = $this->getRequest()->getPost('payment[credit_card_type]', null);
            
            if($card_digits && $card_type){
                $this->callGeoEvaluationService($address, $evaluation_params);
            }
            
            if ($data["method"] === 'pointsexchangepayment') {

                $customer = Mage::getSingleton('customer/session')->getCustomer();
                $encryptedPass = Mage::getSingleton('customer/session')->getData('encrypted_password');
                $decrytedPass = Mage::helper('uruguayancustomer')->c_decrypt($encryptedPass, PASS_ENCRYPT_SALT);
                
                //$customerData = Mage::getModel('uruguayancustomer/customer')->getGeoLoyaltyCustomerData($customer->getData("email"), $decrytedPass);
                /*if($customerData){
                    $geoLoyaltyTelephones = $customerData->phones;
                    $telVerified=false;
                    $tel_number = trim($data["tel_verification"]);
                    for($p=0;$p<count($geoLoyaltyTelephones);$p++){
                        if(!$telVerified && $tel_number != null && $tel_number == $geoLoyaltyTelephones[$p]->number){$telVerified=true;}
                    }
                }*/
                if($decrytedPass != $data["pass_verification"]){
                    $result['error'] = $this->__('La password no corresponde.');
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                    return;
                }else{
                    //chequear total de puntos del customer via geoloyalty
                    $customer = Mage::getSingleton('customer/session')->getCustomer();
                    $token = Mage::getSingleton('customer/session')->getData('session_token');
                    $balanceData = Mage::getModel('uruguayancustomer/customer')->getLoyaltyBalanceQuery($token);
                    $customerTotalFarmaPoints = 0;
                    if ($balanceData) {
                        //chequear que la currency sea puntos
                        foreach ($balanceData as $b) {
                            if ($b->currencyName === "Puntos") {
                                $customerTotalFarmaPoints = $b->totalBalance;
                                break;
                            }
                        }
                    }
                    $ratio = Mage::getStoreConfig('farmaratio/farmaratio_group/farmaratio_input', Mage::app()->getStore());
                    $total = $this->getOnepage()->getQuote()->getData("subtotal");
                    if ($ratio * $customerTotalFarmaPoints < $total) {
                        $result['error'] = $this->__('No tienes Farmapuntos suficientes.');
                        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                        return;
                    }
                }
                /*if (!$telVerified) {
                    $result['error'] = $this->__('El telefono no corresponde????.');
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                    return;
                } else {
                    //chequear total de puntos del customer via geoloyalty
                    $customer = Mage::getSingleton('customer/session')->getCustomer();
                    $token = Mage::getSingleton('customer/session')->getData('session_token');
                    $balanceData = Mage::getModel('uruguayancustomer/customer')->getLoyaltyBalanceQuery($token);
                    $customerTotalFarmaPoints = 0;
                    if ($balanceData) {
                        //chequear que la currency sea puntos
                        foreach ($balanceData as $b) {
                            if ($b->currencyName === "Puntos") {
                                $customerTotalFarmaPoints = $b->totalBalance;
                                break;
                            }
                        }
                    }
                    $ratio = Mage::getStoreConfig('farmaratio/farmaratio_group/farmaratio_input', Mage::app()->getStore());
                    $total = $this->getOnepage()->getQuote()->getData("subtotal");
                    if ($ratio * $customerTotalFarmaPoints < $total) {
                        $result['error'] = $this->__('No tienes Farmapuntos suficientes.');
                        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                        return;
                    }

                }*/
            }
            $result = $this->getOnepage()->savePayment($data);
            // get section and redirect data
            $redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
            if (empty($result['error']) && !$redirectUrl) {
                $this->loadLayout('checkout_onepage_review');
                $result['goto_section'] = 'review';
                $result['update_section'] = array(
                    'name' => 'review',
                    'html' => $this->_getReviewHtml()
                );
            }
            if ($redirectUrl) {
                $result['redirect'] = $redirectUrl;
            }
        } catch (Mage_Payment_Exception $e) {
            if ($e->getFields()) {
                $result['fields'] = $e->getFields();
            }
            $result['error'] = $e->getMessage();
        } catch (Mage_Core_Exception $e) {
            $result['error'] = $e->getMessage();
        } catch (Exception $e) {
            Mage::logException($e);
            $result['error'] = $this->__('Unable to set Payment Method.');
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
}