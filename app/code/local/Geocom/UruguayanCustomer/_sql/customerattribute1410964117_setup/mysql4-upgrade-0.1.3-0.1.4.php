<?php
$installer = $this;
$installer->startSetup();

$tablequoteaddress = $this->getTable('sales/quote_address');
$tableorderaddress = $this->getTable('sales/order_address');

$installer->run("
ALTER TABLE  $tablequoteaddress ADD  `city_id` INTEGER NOT NULL
");
$installer->run("
ALTER TABLE  $tableorderaddress ADD  `city_id` INTEGER NOT NULL
");

$installer->run("
ALTER TABLE  $tablequoteaddress ADD  `geo_region_id` INTEGER NOT NULL
");
$installer->run("
ALTER TABLE  $tableorderaddress ADD  `geo_region_id` INTEGER NOT NULL
");

$installer->addAttribute("customer_address", "geo_region_id",  array(
    "type"     => "int",
    "backend"  => "",
    "label"    => "Geo Region id",
    "input"    => "text",
    "source"   => "",
    "visible"  => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique"     => false,
    "note"       => ""

));
$attribute   = Mage::getSingleton("eav/config")->getAttribute("customer_address", "geo_region_id");
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

$installer->addAttribute("customer_address", "city_id",  array(
    "type"     => "int",
    "backend"  => "",
    "label"    => "City id",
    "input"    => "text",
    "source"   => "",
    "visible"  => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique"     => false,
    "note"       => ""

));
$attribute   = Mage::getSingleton("eav/config")->getAttribute("customer_address", "city_id");
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


$installer->endSetup();
