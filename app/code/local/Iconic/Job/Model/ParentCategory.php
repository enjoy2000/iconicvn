<?php
 
class Iconic_Job_Model_ParentCategory extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('job/parentcategory', 'id');
    }
}