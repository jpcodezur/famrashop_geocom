<?php
class Geocom_UruguayanCustomer_Block_Onepage_Farmalocal extends Mage_Checkout_Block_Onepage_Abstract {
    protected function _construct()
    {
        $adminhtml  = Mage::getModel('pulsestorm_crossareasession/manager')
            ->getSessionData('adminhtml');
        $adminUser = $adminhtml['admin']['user'];
        $adminLoggedIn = isset($adminUser) && $adminUser->getId() && $adminUser->getIsActive();

        if ($adminLoggedIn) {
            $this->getCheckout()->setStepData('farmalocal', array(
                'label'     => Mage::helper('checkout')->__('Local Farmashop'),
                'is_show'   => $this->isShow()
            ));
            if ($this->isCustomerLoggedIn()) {
                $this->getCheckout()->setStepData('farmalocal', 'allow', true);
                $this->getCheckout()->setStepData('billing', 'allow', false);
            }
        }

        parent::_construct();
    }

    public function isSelectedLocal($local) {
        if (!isset($local)) return false;
        $adminhtml  = Mage::getModel('pulsestorm_crossareasession/manager')
            ->getSessionData('adminhtml');
        $adminUser = $adminhtml['admin']['user'];
        $adminLoggedIn = isset($adminUser) && $adminUser->getId() && $adminUser->getIsActive();
        $flocal=$this->getQuote()->getFarmashopLocal();
        if (!empty($flocal)) {
            return $flocal == $local;
        }

        if ($adminLoggedIn) {
            $savedLocal = Mage::getSingleton('admin/session')->getFarmashopLocal();
            return $savedLocal == $local;
        }

        return false;
    }
}