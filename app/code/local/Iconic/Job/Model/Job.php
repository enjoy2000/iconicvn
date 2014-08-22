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
                $urlKey .= '-' . $this->getId();
            	$this->setUrlKey($urlKey);
            }
        }
        parent::_beforeSave();
    }
    
    protected function _afterSave()
    {
        parent::_afterSave();
    } 

    public function getCategory(){
        return Mage::getModel('job/category')->load($this->getCategoryId());
    }
	
	
	public function getJobsInCategory($limit=20){
		$category = $this->getCategory();
		$jobsInCategory = Mage::getModel('job/job')->getCollection()->addFieldToFilter('category_id',array('eq'=>$this->getCategoryId()))
					->addFieldToFilter('job_id',array('neq'=>$this->getId()))
					->addFieldToFilter('status', array('eq'=>'active'))
					->setOrder('created_time','DESC')
					->setPageSize($limit)
					->setCurPage(1)
					->load();
		return $jobsInCategory;
	}
	
	public function getCountry(){
		$location = Mage::getModel('job/location')->load($this->getLocationId());
		return Mage::helper('job')->getTransName($location);
	}
	
	public function getCategoryName(){
		$category = Mage::getModel('job/category')->load($this->getCategoryId());
		$parent = Mage::getModel('job/parentcategory')->load($category->getParentcategoryId());
		return Mage::helper('job')->getTransName($category);
	}
	
	public function getFunctionName(){
		$function = Mage::getModel('job/category')->load($this->getFunctionCategoryId());
		$parent = Mage::getModel('job/parentcategory')->load($function->getParentcategoryId());
		return Mage::helper('job')->getTransName($function);
	}
	
	public function getLevel(){
		$level = Mage::getModel('job/level')->load($this->getJobLevel());
		return Mage::helper('job')->getTransName($level);
	}
	
	public function getType(){
		$type = Mage::getModel('job/type')->load($this->getJobType());
		return Mage::helper('job')->getTransName($type);
	}
	
	public function getFullSalary(){
		if($this->getJobSalary() && $this->getJobSalaryTo()){
			$salary = $this->getJobSalaryCurrency() . $this->getJobSalary() . ' - ' . $this->getJobSalaryTo() . '(' .$this->getJobSalaryType() . ')'; 
		}else if($this->getJobSalary() && !$this->getJobSalaryTo()){
			$salary = $this->getJobSalaryCurrency() . $this->getJobSalary() . '(' .$this->getJobSalaryType() . ')'; 
		}else{
			$salary = Mage::helper('job')->__('Negotiable');
		}
		return $salary;
	}
	
	public function getEditUrl(){
		$url = Mage::getUrl('client/job/post', array('id'=>$this->getId()));
		return $url;
	}
}