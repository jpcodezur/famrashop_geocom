<?php
$installer = $this;
$installer->startSetup();

$tableorder = $this->getTable('sales/order');
$tablequote = $this->getTable('sales/quote');

$installer->run("
ALTER TABLE  $tableorder ADD  `customer_document_type` VARCHAR (255) NOT NULL
");
$installer->run("
ALTER TABLE  $tablequote ADD  `customer_document_type` VARCHAR (255) NOT NULL
");
$installer->run("
ALTER TABLE  $tableorder ADD  `customer_middle_name` VARCHAR (255) NOT NULL
");
$installer->run("
ALTER TABLE  $tablequote ADD  `customer_middle_name` VARCHAR (255) NOT NULL
");
$installer->run("
ALTER TABLE  $tableorder ADD  `rut_buyer` VARCHAR (255) NOT NULL
");
$installer->run("
ALTER TABLE  $tablequote ADD  `rut_buyer` VARCHAR (255) NOT NULL
");
$installer->run("
ALTER TABLE  $tableorder ADD  `rut_number` VARCHAR (255) NOT NULL
");
$installer->run("
ALTER TABLE  $tablequote ADD  `rut_number` VARCHAR (255) NOT NULL
");


$tablequoteaddress = $this->getTable('sales/quote_address');
$tableorderaddress = $this->getTable('sales/order_address');

$installer->run("
ALTER TABLE  $tablequoteaddress ADD  `address_number` varchar(255) NOT NULL
");
$installer->run("
ALTER TABLE  $tableorderaddress ADD  `address_number` varchar(255) NOT NULL
");
$installer->run("
ALTER TABLE  $tablequoteaddress ADD  `address_corner` varchar(255) NOT NULL
");
$installer->run("
ALTER TABLE  $tableorderaddress ADD  `address_corner` varchar(255) NOT NULL
");

	
$installer->endSetup();

