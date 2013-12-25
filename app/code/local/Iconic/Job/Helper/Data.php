<?php

class Iconic_Job_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function formatUrlKey($str)
    {
        $urlKey = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('catalog/product_url')->format($str));
        $urlKey = strtolower($urlKey);
        $urlKey = trim($urlKey, '-');
    
        return $urlKey;
    }
	public function formatDate($date)
    {
        $date = date('d M, Y', strtotime($date));
        return $date;
    }
}