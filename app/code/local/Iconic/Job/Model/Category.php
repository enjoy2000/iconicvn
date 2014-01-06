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
            $this->setUrlKey(Mage::helper('job')->formatUrlKey($this->getName()));
        }
        parent::_beforeSave();
    }

    public function getParentCategory(){
    	return Mage::getModel('job/parentcategory')->load($this->getParentcategoryId());
    }
}