<?php
/* @var $installer Mage_Customer_Model_Entity_Setup */
//postcode not required.
$installer = Mage::getModel('customer/entity_setup', 'core_setup');
$installer->startSetup();


$installer->run("
            ALTER TABLE {$this->getTable('sales_flat_quote_address')} ADD COLUMN `complement` VARCHAR(65536) DEFAULT NULL;
            ALTER TABLE {$this->getTable('sales_flat_order_address')} ADD COLUMN `complement` VARCHAR(65536) DEFAULT NULL;
");

$tableorder = $this->getTable('sales/order');
$tablequote = $this->getTable('sales/quote');
$installer->run("
ALTER TABLE  $tableorder ADD  `delivery_time` VARCHAR (100) NOT NULL
");
$installer->run("
ALTER TABLE  $tablequote ADD  `delivery_time` VARCHAR (100) NOT NULL
");
$installer->run("
ALTER TABLE  $tableorder ADD  `delivery_date` DATE NOT NULL
");
$installer->run("
ALTER TABLE  $tablequote ADD  `delivery_date` DATE NOT NULL
");


$installer->endSetup();
