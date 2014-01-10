<?php
 
class Iconic_Job_Model_Category extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('job/category');
    }


    protected function _beforeSave()
    {
        if(!$this->getUrlKey()){
            $urlKey = Mage::helper('job')->formatUrlKey($this->getName());
            if(!Mage::getModel('job/category')->load($urlKey, 'url_key')->getId() || Mage::getModel('job/category')->load($urlKey, 'url_key')->getId() == $this->getId()){
                $this->setUrlKey($urlKey);
            } else {
                $urlKey .= '-' . $this->getId();
            	$this->setUrlKey($urlKey);
            }
        }
        parent::_beforeSave();
    }

    public function getParentCategory(){
    	return Mage::getModel('job/parentcategory')->load($this->getParentcategoryId());
    }
}