<?php
/* @var $installer Mage_Customer_Model_Entity_Setup */
//postcode not required.
$installer = Mage::getModel('customer/entity_setup', 'core_setup');
$installer->startSetup();
$tableorder = $this->getTable('sales/order');
$tablequote = $this->getTable('sales/quote');




$installer->run("ALTER TABLE $tableorder DROP COLUMN delivery_time;");
$installer->run("ALTER TABLE $tablequote DROP COLUMN delivery_time;");
$installer->run("ALTER TABLE $tableorder DROP COLUMN delivery_date;");
$installer->run("ALTER TABLE $tablequote DROP COLUMN delivery_date;");
$installer->run("
ALTER TABLE  $tableorder ADD  `delivery_to` DATETIME  NOT NULL
");
$installer->run("
ALTER TABLE  $tablequote ADD  `delivery_to` DATETIME NOT NULL
");
$installer->run("
ALTER TABLE  $tableorder ADD  `delivery_from` DATETIME NOT NULL
");
$installer->run("
ALTER TABLE  $tablequote ADD  `delivery_from` DATETIME NOT NULL
");


$installer->endSetup();
