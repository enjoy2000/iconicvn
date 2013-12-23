<?php

class Iconic_Job_Block_Category extends Mage_Core_Block_Template
{
    public function indexAction()
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $connection->fetchAll('SELECT * FROM `job_category`');
    }
}