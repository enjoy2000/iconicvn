<?php
 
$installer = $this;
 
$installer->startSetup();
 
$trans = Mage::helper('job'); 
 
$installer->run("
 

ALTER TABLE {$this->getTable('job')} ADD COLUMN url_key varchar(20) NOT NULL;

ALTER TABLE {$this->getTable('job_category')} ADD COLUMN url_key varchar(20) NOT NULL;

ALTER TABLE {$this->getTable('job_parent_category')} ADD COLUMN url_key varchar(20) NOT NULL;

 
    ");
	

 
$installer->endSetup();