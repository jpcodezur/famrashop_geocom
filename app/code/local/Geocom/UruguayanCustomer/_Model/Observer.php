<?php

class Geocom_UruguayanCustomer_Model_Observer
{

    public function saveOrderBefore($evt)
    {
        $event = $evt->getEvent();

        //get order
        $order = $event->getOrder();

        //$data = $this->getRequest()->getPost('billing', array());
        //$rut_buyer=(isset($data['rut_buyer']))?$data['rut_buyer']:"0";
        //$rut_number=(isset($data['rut_number']))?$data['rut_number']:"0";

        //set the data here
        //$order->setRutBuyer($rut_buyer);
        //$order->setRutNumber($rut_number);

    }

    /*
     * Setea el descuento total en caso de que el metodo de pago sea farampuntos
     */
    public function setPointsDiscount($observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $quoteid = $quote->getId();
        $total = $quote->getBaseSubtotal();
        try {
            $method = $quote->getPayment()->getMethodInstance()->getCode();
        } catch (Exception $e) {
            return;
        }
        $discountAmount = $total;
        if ($quoteid && $method === 'PointsExchangePayment') {
            if ($discountAmount > 0) {
                $quote->setSubtotal(0);
                $quote->setBaseSubtotal(0);
                $quote->setSubtotalWithDiscount(0);
                $quote->setBaseSubtotalWithDiscount(0);
                $quote->setGrandTotal(0);
                $quote->setBaseGrandTotal(0);
                $canAddItems = $quote->isVirtual() ? ('billing') : ('shipping');
                foreach ($quote->getAllAddresses() as $address) {

                    $address->setSubtotal(0);
                    $address->setBaseSubtotal(0);

                    $address->setGrandTotal(0);
                    $address->setBaseGrandTotal(0);

                    $address->collectTotals();

                    $quote->setSubtotal((float)$quote->getSubtotal() + $address->getSubtotal());
                    $quote->setBaseSubtotal((float)$quote->getBaseSubtotal() + $address->getBaseSubtotal());

                    $quote->setSubtotalWithDiscount(
                        (float)$quote->getSubtotalWithDiscount() + $address->getSubtotalWithDiscount()
                    );
                    $quote->setBaseSubtotalWithDiscount(
                        (float)$quote->getBaseSubtotalWithDiscount() + $address->getBaseSubtotalWithDiscount()
                    );

                    $quote->setGrandTotal((float)$quote->getGrandTotal() + $address->getGrandTotal());
                    $quote->setBaseGrandTotal((float)$quote->getBaseGrandTotal() + $address->getBaseGrandTotal());

                    $quote->save();

                    $quote->setGrandTotal($quote->getBaseSubtotal() - $discountAmount)
                        ->setBaseGrandTotal($quote->getBaseSubtotal() - $discountAmount)
                        ->setSubtotalWithDiscount($quote->getBaseSubtotal() - $discountAmount)
                        ->setBaseSubtotalWithDiscount($quote->getBaseSubtotal() - $discountAmount)
                        ->save();


                    if ($address->getAddressType() == $canAddItems) {
                        //echo $address->setDiscountAmount; exit;
                        $address->setSubtotalWithDiscount((float)$address->getSubtotalWithDiscount() - $discountAmount);
                        $address->setGrandTotal((float)$address->getGrandTotal() - $discountAmount);
                        $address->setBaseSubtotalWithDiscount((float)$address->getBaseSubtotalWithDiscount() - $discountAmount);
                        $address->setBaseGrandTotal((float)$address->getBaseGrandTotal() - $discountAmount);
                        if ($address->getDiscountDescription()) {
                            $address->setDiscountAmount(-($address->getDiscountAmount() - $discountAmount));
                            $address->setDiscountDescription($address->getDiscountDescription() . ', FarmaPuntos');
                            $address->setBaseDiscountAmount(-($address->getBaseDiscountAmount() - $discountAmount));
                        } else {
                            $address->setDiscountAmount(-($discountAmount));
                            $address->setDiscountDescription('FarmaPuntos');
                            $address->setBaseDiscountAmount(-($discountAmount));
                        }
                        $address->save();
                    }//end: if
                } //end: foreach


                foreach ($quote->getAllItems() as $item) {
                    //We apply discount amount based on the ratio between the GrandTotal and the RowTotal
                    $rat = $item->getPriceInclTax() / $total;
                    $ratdisc = $discountAmount * $rat;
                    $item->setDiscountAmount(($item->getDiscountAmount() + $ratdisc) * $item->getQty());
                    $item->setBaseDiscountAmount(($item->getBaseDiscountAmount() + $ratdisc) * $item->getQty())->save();

                }


            }

        }


    }

    public function unsetCustomerSessionDataWhenCC($observer){
        if($this->isCCAdmin()) {
            $session = Mage::getSingleton('customer/session');
            $session->unsetAll();
            $session->getCookie()->delete($session->getSessionName());
            //$session->addSuccess(Mage::helper('adminhtml')->__('You have logged out the customer.'));
            Mage::app()->getResponse()->setRedirect(Mage::getUrl("*"));
        }
    }

    private function isCCAdmin(){
        $adminhtml  = Mage::getModel('pulsestorm_crossareasession/manager')->getSessionData('adminhtml');
        $adminUser = $adminhtml['admin']['user'];
        return ($adminUser && $adminUser->getRole()->getRoleName()===CALLCENTER_ROLE_NAME);
        // Ensure we're in the admin session namespace for checking the admin user..

    }

}
