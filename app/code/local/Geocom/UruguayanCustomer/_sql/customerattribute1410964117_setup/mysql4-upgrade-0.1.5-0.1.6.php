<?php
$installer = $this;
$installer->startSetup();

$tableorder = $this->getTable('sales/order');
$tablequote = $this->getTable('sales/quote');

$installer->run("
ALTER TABLE  $tableorder ADD  `contexto_promocion` TEXT NOT NULL
");
$installer->run("
ALTER TABLE  $tablequote ADD  `contexto_promocion` TEXT NOT NULL
");

$installer->endSetup();

