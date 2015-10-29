<?php
$installer = $this;
$installer->startSetup();

$tableorder = $this->getTable('sales/order');
$tablequote = $this->getTable('sales/quote');

$installer->run("
ALTER TABLE  $tableorder ADD  `comment` VARCHAR (65536) NOT NULL
");
$installer->run("
ALTER TABLE  $tablequote ADD  `comment` VARCHAR (65536) NOT NULL
");

$installer->endSetup();

