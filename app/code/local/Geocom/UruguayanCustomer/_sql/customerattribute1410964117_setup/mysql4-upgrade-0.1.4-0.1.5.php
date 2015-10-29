<?php
$installer = $this;
$installer->startSetup();

$tableorder = $this->getTable('sales/order');
$tablequote = $this->getTable('sales/quote');

$installer->run("
ALTER TABLE  $tableorder ADD  `cajero` VARCHAR (255) NOT NULL
");
$installer->run("
ALTER TABLE  $tablequote ADD  `cajero` VARCHAR (255) NOT NULL
");
$installer->run("
ALTER TABLE  $tableorder ADD  `terminal_web` VARCHAR (255) NOT NULL
");
$installer->run("
ALTER TABLE  $tablequote ADD  `terminal_web` VARCHAR (255) NOT NULL
");
$installer->run("
ALTER TABLE  $tableorder ADD  `caja_geopos` VARCHAR (255) NOT NULL
");
$installer->run("
ALTER TABLE  $tablequote ADD  `caja_geopos` VARCHAR (255) NOT NULL
");

$installer->endSetup();

