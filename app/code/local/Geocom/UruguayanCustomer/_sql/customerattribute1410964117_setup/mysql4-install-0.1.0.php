<?php
$installer = $this;
$installer->startSetup();


$installer->addAttribute("customer", "document_number",  array(
    "type"     => "varchar",
    "backend"  => "",
    "label"    => "Document number",
    "input"    => "text",
    "source"   => "",
    "visible"  => true,
    "required" => true,
    "default" => "",
    "frontend" => "",
    "unique"     => false,
    "note"       => ""

	));

        $attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", "document_number");

        
$used_in_forms=array();

$used_in_forms[]="adminhtml_customer";
$used_in_forms[]="customer_account_create";
$used_in_forms[]="customer_account_edit";
$used_in_forms[]="checkout_register";
        $attribute->setData("used_in_forms", $used_in_forms)
		->setData("is_used_for_customer_segment", true)
		->setData("is_system", 0)
		->setData("is_user_defined", 1)
		->setData("is_visible", 1)
		->setData("sort_order", 100)
		;
        $attribute->save();
	
	
	

$installer->addAttribute("customer_address", "neighborhood",  array(
    "type"     => "varchar",
    "backend"  => "",
    "label"    => "Neighborhood",
    "input"    => "text",
    "source"   => "",
    "visible"  => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique"     => false,
    "note"       => ""

	));

        $attribute   = Mage::getSingleton("eav/config")->getAttribute("customer_address", "neighborhood");

        
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
	
$installer->addAttribute("customer_address", "complete_address",  array(
    "type"     => "text",
    "backend"  => "",
    "label"    => "Complete address",
    "input"    => "text",
    "source"   => "",
    "visible"  => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique"     => false,
    "note"       => ""

	));

        $attribute   = Mage::getSingleton("eav/config")->getAttribute("customer_address", "complete_address");

        
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
$installer->run("
ALTER TABLE  $tableorder ADD  `customer_document_number` VARCHAR (255) NOT NULL
");

$tablequote = $this->getTable('sales/quote');
$installer->run("
ALTER TABLE  $tablequote ADD  `customer_document_number` VARCHAR (255) NOT NULL
");

$tablequoteaddress = $this->getTable('sales/quote_address');
$installer->run("
ALTER TABLE  $tablequoteaddress ADD  `neighborhood` varchar(255) NOT NULL
");

$tableorderaddress = $this->getTable('sales/order_address');
$installer->run("
ALTER TABLE  $tableorderaddress ADD  `neighborhood` varchar(255) NOT NULL
");
	
$installer->endSetup();
	 