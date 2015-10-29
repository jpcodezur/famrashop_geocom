<?php
class Geocom_CardPosPayment_Model_CardPosPayment extends Mage_Payment_Model_Method_Abstract
{
    protected $_code = 'cardpospayment';

    /*protected $_formBlockType = 'uruguayancustomer/form_cashondelivery';
    protected $_infoBlockType = 'uruguayancustomer/info';*/

    public function isAvailable($quote = null) {
        $adminhtml  = Mage::getModel('pulsestorm_crossareasession/manager')
            ->getSessionData('adminhtml');
        $adminUser = $adminhtml['admin']['user'];
        $adminLoggedIn = isset($adminUser) && $adminUser->getId() && $adminUser->getIsActive();
        return parent::isAvailable($quote) && $adminLoggedIn;
    }
    
    public function validate()
	{
		parent::validate();

		$info = $this->getInfoInstance();

		$credit_card_type = $info->getCreditCardType();
		$credit_card_iin = $info->getCreditCardIin();
		if(empty($credit_card_type) || empty($credit_card_iin)){
			$errorCode = 'invalid_data';
			$errorMsg = $this->_getHelper()->__('Ingrese tarjeta de crédito y primeros seis dígitos');
		}

		if($errorMsg){
			Mage::throwException($errorMsg);
		}


		return $this;
	}
}
?>