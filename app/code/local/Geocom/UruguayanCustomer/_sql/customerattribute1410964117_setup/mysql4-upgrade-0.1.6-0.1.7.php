<?php
/* @var $installer Mage_Customer_Model_Entity_Setup */
//postcode not required.
$installer = Mage::getModel('customer/entity_setup', 'core_setup');
$installer->startSetup();

$installer->updateAttribute(
    'customer_address',
    'postcode',
    'is_required',
    0
);
$installer->endSetup();


//geo address id to sync addresses.
$installer = $this;
$installer->startSetup();
$installer->addAttribute("customer_address", "geo_address_id",  array(
    "type"     => "int",
    "backend"  => "",
    "label"    => "Geo Address id",
    "input"    => "text",
    "source"   => "",
    "visible"  => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique"     => false,
    "note"       => ""
));
$attribute   = Mage::getSingleton("eav/config")->getAttribute("customer_address", "geo_address_id");
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
//for saving data in json (latitude,longitude,nearest_local)
$installer->addAttribute("customer_address", "position_json",  array(
    "type"     => "text",
    "backend"  => "",
    "label"    => "Position Json",
    "input"    => "text",
    "source"   => "",
    "visible"  => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique"     => false,
    "note"       => ""

));
$attribute   = Mage::getSingleton("eav/config")->getAttribute("customer_address", "position_json");
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
//

$installer->addAttribute("customer_address", "complement",  array(
    "type"     => "text",
    "backend"  => "",
    "label"    => "Complement",
    "input"    => "text",
    "source"   => "",
    "visible"  => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique"     => false,
    "note"       => ""

));
$attribute   = Mage::getSingleton("eav/config")->getAttribute("customer_address", "complement");
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

//
$tableorder = $this->getTable('sales/order');
$tablequote = $this->getTable('sales/quote');

$installer->run("
ALTER TABLE  $tableorder ADD  `position_json` VARCHAR (65536) NOT NULL
");
$installer->run("
ALTER TABLE  $tablequote ADD  `position_json` VARCHAR (65536) NOT NULL
");
$installer->run("
ALTER TABLE  $tableorder ADD  `puntos_canjeados` VARCHAR (50) NOT NULL
");
$installer->run("
ALTER TABLE  $tablequote ADD  `puntos_canjeados` VARCHAR (50) NOT NULL
");
$installer->run("
ALTER TABLE  $tableorder ADD  `complement` VARCHAR (65536) NOT NULL
");
$installer->run("
ALTER TABLE  $tablequote ADD  `complement` VARCHAR (65536) NOT NULL
");

$installer->run("
            ALTER TABLE {$this->getTable('sales_flat_quote_address')} ADD COLUMN `position_json` VARCHAR(65536) DEFAULT NULL;
            ALTER TABLE {$this->getTable('sales_flat_order_address')} ADD COLUMN `position_json` VARCHAR(65536) DEFAULT NULL;
");
$installer->endSetup();
