<?php
class Geocom_PointsExchangePayment_Block_Form_Form extends Mage_Payment_Block_Form
{
	protected function _construct()
	{
		parent::_construct();
        $this->setTemplate('PointsExchangePayment/form/PointsExchangePaymentFormPassword.phtml');
    }

}