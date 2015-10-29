<?php
require_once 'Mage/Checkout/controllers/OnepageController.php';
require_once Mage::getModuleDir('controllers','Cualit_UruguayanCustomer').'/Checkout/OnepageController.php';

class Geocom_UruguayanCustomer_Checkout_OnepageController extends Cualit_UruguayanCustomer_Checkout_OnepageController
{
    
    private function array_to_xml($array, $level = 1)
    {
        $xml = '';
        if ($level == 1) {
            $xml .= '<arg0><![CDATA[<?xml version="1.0" encoding="UTF-8"?>' .
                "\n<operation>\n";
        }
        foreach ($array as $key => $value) {
            $key = strtolower($key);
            if (is_array($value)) {
                $multi_tags = false;
                foreach ($value as $key2 => $value2) {
                    if (is_array($value2)) {
                        $xml .= str_repeat("\t", $level) . "<$key>\n";
                        $xml .= $this->array_to_xml($value2, $level + 1);
                        $xml .= str_repeat("\t", $level) . "</$key>\n";
                        $multi_tags = true;
                    } else {
                        if (trim($value2) != '') {
                            /*if (htmlspecialchars($value2) != $value2) {
                                $xml .= str_repeat("\t", $level) .
                                    "<$key><![CDATA[$value2]]>" .
                                    "</$key>\n";
                            } else {*/
                            $xml .= str_repeat("\t", $level) .
                                "<$key>$value2</$key>\n";
                            //}
                        }
                        $multi_tags = true;
                    }
                }
                if (!$multi_tags and count($value) > 0) {
                    $xml .= str_repeat("\t", $level) . "<$key>\n";
                    $xml .= $this->array_to_xml($value, $level + 1);
                    $xml .= str_repeat("\t", $level) . "</$key>\n";
                }
            } else {
                if (trim($value) != '') {
                    if (htmlspecialchars($value) != $value) {
                        $xml .= str_repeat("\t", $level) . "<$key>" .
                            "<![CDATA[$value]]></$key>\n";
                    } else {
                        $xml .= str_repeat("\t", $level) .
                            "<$key>$value</$key>\n";
                    }
                }
            }
        }
        if ($level == 1) {
            $xml .= "</operation>\n]]></arg0>";
        }
        return $xml;
    }
    
    private function callGeoEvaluationService($address, $evaluation_params)
    {
        define('GEO_URL', "http://192.168.250.44:9001/cxf/EvaluationService?wsdl");
        
        $quote = $address->getQuote();
        $evaluation_params = $this->addTransactionAndPaymentToParams($address,$evaluation_params);
        $evaluation_params = array("document" => array($evaluation_params));
        $xml_params = $this->array_to_xml($evaluation_params, 1);

        try {
            $soapvar = new SoapVar($xml_params, XSD_ANYXML);
            $contextvar = new SoapVar($this->getContext(), XSD_ANYXML);
            $client = new SoapClient(GEO_URL, array('trace' => 1));
            $response = $client->__soapCall("evaluateWithFlags", array("parameters" => array("arg0" => $soapvar, "arg2" => $contextvar)));
            //$response = $client->__soapCall("evaluate", array("parameters" => array("arg0" => $soapvar)));
            $this->processResponse($quote, $response);
        } catch (Exception $e) {
        }
    }

    private function processResponse($quote, $response) {
        $response = $response->return;
        $xml_response = simplexml_load_string($response);
        $discounts = $xml_response->discount;
        $savedContext = (string) $xml_response->savedContext->attributes()->context;
        $added_discounts = array();
        foreach($discounts as $discount) {
            $product_sku = (string) $discount[0]->attributes()->sku;
            $product = $this->getProductBySku($product_sku);
            $productId = $product->getId();
            if(is_null($added_discounts[$productId])) {
                $added_discounts[$productId] = array();
            }
            $added_discounts[$productId][] = $discount;
        }

        try {
            $promos = json_encode($added_discounts);
            $serializedResponse = serialize($savedContext);
            Mage::getSingleton('checkout/session')->setData('promos', $promos);
            Mage::getSingleton('checkout/session')->setData("serialized_promos", $serializedResponse);
        } catch(Exception $f) {

        }

        foreach($added_discounts as $discount_arr) {
            $price = null;
            $last_product = null;
            foreach($discount_arr as $discount) {
                $product_sku = (string) $discount[0]->attributes()->sku;
                if($last_product != $product_sku) {
                    $last_product = $product_sku;
                    $price = null;
                }
                $amount = (float) $discount[0]->attributes()->amount;
                $quantity = (int) $discount[0]->attributes()->quantity;
                $quote_item = $this->getQuoteItemByProductSku($quote, $product_sku);
                if (!is_null($quote_item) || $quote_item !== false) {
                    if(is_null($price)) {
                        $price = $quote_item->getProduct()->getPrice();
                    }
                    $quote_item->getProduct()->setIsSuperMode(true);
                }
            }
        }
        $items = $quote->getAllItems();
        foreach($items as $item){
            if($added_discounts!=null && $added_discounts[$item->getProductId()]==null){
                $quote_item = $this->getQuoteItemByProductId($quote, $item->getProductId());
                $quote_item->setCustomPrice(null);
                $quote_item->setOriginalCustomPrice(null);
            }
        }
    }
    
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