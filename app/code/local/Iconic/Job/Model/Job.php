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
        if(!$this->getUrlKey()){
            $urlKey = Mage::helper('job')->formatUrlKey($this->getTitle());
            if(!Mage::getModel('job/job')->load($urlKey, 'url_key')->getId()){
                $this->setUrlKey($urlKey);
            } else {
                $this->setUrlKeyNotSetted(true);
            }
        }
        parent::_beforeSave();
    }
    
    protected function _afterSave()
    {
        if($this->getUrlKeyNotSetted()){
            $urlKey = Mage::helper('job')->formatUrlKey($this->getTitle());
            $urlKey .= '-' . $this->getId();
            $this->setUrlKey($urlKey);
            $this->save();
            $this->setUrlKeyNotSetted(false);
        }
        parent::_afterSave();
    } 

    public function getCategory(){
        return Mage::getModel('job/category')->load($this->getCategoryId());
    }
	
	
	public function getJobsInCategory($limit=20){
		$category = $this->getCategory();
		$jobsInCategory = Mage::getModel('job/job')->getCollection()->addFieldToFilter('category_id',array('eq'=>$this->getCategoryId()))
					->addFieldToFilter('job_id',array('neq'=>$this->getId()))
					->setOrder('created_time','DESC')
					->setPageSize($limit)
					->setCurPage(1)
					->load();
		return $jobsInCategory;
	}
}