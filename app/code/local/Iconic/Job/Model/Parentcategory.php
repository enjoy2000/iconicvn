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
            $this->setUrlKey(Mage::helper('job')->formatUrlKey($this->getName()));
        }
        parent::_beforeSave();
    }
}