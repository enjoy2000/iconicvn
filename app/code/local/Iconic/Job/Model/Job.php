<?php
 
class Iconic_Job_Model_Job extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('job/job');
    }
    
    protected function _beforeSave()
    {        
        parent::_beforeSave();
    }
    
    protected function _afterSave()
    {    
        
        parent::_afterSave();
    } 
}