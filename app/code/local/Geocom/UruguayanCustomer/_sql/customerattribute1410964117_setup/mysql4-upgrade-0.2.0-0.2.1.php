<?php

$installer = Mage::getModel('customer/entity_setup', 'core_setup');
$installer->startSetup();

$installer->addAttribute("customer_address", "neighborhood_id",  array(
    "type"     => "int",
    "backend"  => "",
    "label"    => "neighborhood_id",
    "input"    => "text",
    "source"   => "",
    "visible"  => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique"     => false,
    "note"       => ""
));
$attribute   = Mage::getSingleton("eav/config")->getAttribute("customer_address", "neighborhood_id");
$used_in_forms=array();
$used_in_forms[]="adminhtml_customer_address";
$used_in_forms[]="customer_register_address";
$used_in_forms[]="customer_address_edit";
$attribute->setData("used_in_forms", $used_in_forms)
    ->setData("is_used_for_customer_segment", true)
    ->setData("is_system", 0)
    ->setData("is_user_defined", 1)
    ->setData("is_visible", 1)
    ->setData("sort_order", 100)
;
$attribute->save();

$tableorder = $this->getTable('sales/order');
$tablequote = $this->getTable('sales/quote');

$installer->run("
ALTER TABLE  $tableorder ADD  `neighborhood_id` INT(11)
");
$installer->run("
ALTER TABLE  $tablequote ADD  `neighborhood_id` INT(11)
");

$attribute->save();

$installer->endSetup();