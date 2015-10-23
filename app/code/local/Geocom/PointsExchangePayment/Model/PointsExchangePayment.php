<?php
class Geocom_PointsExchangePayment_Model_PointsExchangePayment extends Mage_Payment_Model_Method_Abstract
{
    protected $_code = 'pointsexchangepayment';

    protected $_formBlockType = 'pointsexchangepayment/form_form';
    protected $_infoBlockType = 'pointsexchangepayment/info_info';

    public function isAvailable($quote = null) {
        $adminhtml  = Mage::getModel('pulsestorm_crossareasession/manager')
            ->getSessionData('adminhtml');
        $adminUser = $adminhtml['admin']['user'];
        $adminLoggedIn = isset($adminUser) && $adminUser->getId() && $adminUser->getIsActive();
        $isUserLoged=Mage::getSingleton('customer/session')->isLoggedIn();
        return (parent::isAvailable($quote) && $isUserLoged) && !$adminLoggedIn;
    }


    /*public function validate()
    {
        parent::validate();

        $info = $this->getInfoInstance();

        $tel_verification = $info->getTelVerification();

        if(empty($tel_verification)){
            $errorCode = 'invalid_data';
            $errorMsg = $this->_getHelper()->__('Verificación requerida');
        }

        if($errorMsg){
            Mage::throwException($errorMsg);
        }


        return $this;
    }*/
}
?>