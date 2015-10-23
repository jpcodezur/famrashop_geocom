<?php
class Geocom_PointsExchangePayment_Block_Info_info  extends Mage_Payment_Block_Info
{
    protected function _prepareSpecificInformation($transport = null)
    {
        if (null !== $this->_paymentSpecificInformation) {
            return $this->_paymentSpecificInformation;
        }
        $info = $this->getInfo();
        $transport = new Varien_Object();
        $transport = parent::_prepareSpecificInformation($transport);
        $transport->addData(array(
            Mage::helper('payment')->__('Por favor Escriba su número de telefono para verificar:') => $info->getTelVerification()
        ));
        return $transport;
    }
}