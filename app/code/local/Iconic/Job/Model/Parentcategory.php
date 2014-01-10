<?php
 
class Iconic_Job_Model_Parentcategory extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('job/parentcategory');
    }

    protected function _beforeSave()
    {
        if(!$this->getUrlKey()){
            $urlKey = Mage::helper('job')->formatUrlKey($this->getName());
            if(!Mage::getModel('job/parentcategory')->load($urlKey, 'url_key')->getId() || Mage::getModel('job/parentcategory')->load($urlKey, 'url_key')->getId() == $this->getId()){
                $this->setUrlKey($urlKey);
            } else {
                $urlKey .= '-' . $this->getId();
            	$this->setUrlKey($urlKey);
            }
        }
        parent::_beforeSave();
    }
}