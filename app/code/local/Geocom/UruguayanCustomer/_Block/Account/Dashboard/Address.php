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
 * @package     Mage_Customer
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer dashboard addresses section
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Geocom_UruguayanCustomer_Block_Account_Dashboard_Address extends Mage_Customer_Block_Account_Dashboard_Address
{

    public function getPrimaryShippingAddressHtml()
    {
        if($this->getCustomer()->getPrimaryShippingAddress() && $this->getCustomer()->getPrimaryShippingAddress()->getData()["complete_address"])
           return $this->getCustomer()->getPrimaryShippingAddress()->getData()["complete_address"];

            return Mage::helper('customer')->__('You have not set a default shipping address.');

    }
    public function getPrimaryBillingAddressHtml(){
        if($this->getCustomer()->getPrimaryBillingAddress() && $this->getCustomer()->getPrimaryBillingAddress()->getData()["complete_address"])
            return $this->getCustomer()->getPrimaryShippingAddress()->getData()["complete_address"];

        return Mage::helper('customer')->__('You have not set a default shipping address.');
    }

}
