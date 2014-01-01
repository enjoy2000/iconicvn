<?php

class Iconic_Job_Block_Search extends Mage_Core_Block_Template
{
	protected function _prepareLayout(){
			
		$this->setPost($this->getRequest()->getPost());
		
		parent::_prepareLayout();
	}
	
	protected function _beforeToHtml(){
		if($this->needFetchJobs()){
			$this->_fetchJobs();
		}
		return parent::_beforeToHtml();
	}
	
	protected function _fetchJobs(){
		$collection = Mage::getModel('job/job')->getCollection();
		/* @var $collection Iconic_Job_Model_Mysql4_Job_Collection */
		
		if ($this->getKeyword()){
			$likeStm = array('like'=> "%{$this->getKeyword()}%");
			$collection->addFieldToFilter(
							array('title', 'info', 'requirement'),
							array($likeStm, $likeStm, $likeStm));
		}
		
		if ($this->getCategory()){
			$collection->addFieldToFilter('category_id', array('eq' => $this->getCategory()));
		}
		
		if ($this->getLocation()){
			$collection->addFieldToFilter('location_id', array('eq' => $this->getLocation()));
		}
		if ($this->getJobType()){
			$collection->addFieldToFilter('job_type', array('eq' => $this->getJobType()));
		}
		
		if ($this->getFunctionCategory()){
			$collection->addFieldToFilter('function_category_id', array('eq' => $this->getFunctionCategory()));
		}
		
		var_dump($collection->getSelect()->__toString());
		
		
		$this->setResults($collection);
	}
	
	public function needFetchJobs(){
		return !$this->getResults() and $this->isQueryParamAvailable();
	}
	
	public function isQueryParamAvailable(){
		return $this->getKeyword() || $this->getCategory() || $this->getLocation();
	}
	
	public function getLocationList(){
		//get list location and category
		if (!$this->hasData('locationList')){
			$location = Mage::getModel('job/location')->getCollection();
			$listLocation = '';
			if ($this->getLocation()){
				foreach ($location as $loc){
					$selected = "";
					if($loc->getLocationId() == $this->getLocation()){
						$selected = " selected=\"selected\"";
					}
					$listLocation .= "<option value=\"{$loc->getLocationId()}\"{$selected}>{$loc->getName()}</option>";
				}
			} else {
				foreach ($location as $loc){
					$listLocation .= '<option value="' . $loc->getLocationId() . '">' . $loc->getName() . '</option>';
				}				
			}
			$this->setData('locationList', $listLocation);
		}
		return $this->getData('locationList');
	}
	
	public function getCategoryList(){
		if (!$this->hasData('categoryList')){
		
			$parentCategory = Mage::getModel('job/parentcategory')->getCollection();
			$listCategory = '';
			if ($this->getCategory()){
				foreach ($parentCategory as $parent){
					$categories = Mage::getModel('job/category')->getCollection()->addFieldToFilter('parentcategory_id', array('eq'=>$parent->getParentcategoryId()));
					$catOptions = '';
					foreach ($categories as $cat){
						$selected = "";
						if($cat->getCategoryId() == $this->getCategory()){
							$selected = " selected=\"selected\"";
						}
						$catOptions .= "<option value=\"{$cat->getCategoryId()}\"{$selected}>{$cat->getName()}</option>";
					}
					$listCategory .= '<optgroup label="'.$parent->getName().'">'.$catOptions.'</optgroup>';
				}
			} else {
				foreach ($parentCategory as $parent){
					$categories = Mage::getModel('job/category')->getCollection()->addFieldToFilter('parentcategory_id', array('eq'=>$parent->getParentcategoryId()));
					$catOptions = '';
					foreach ($categories as $cat){
						$catOptions .= '<option value="' . $cat->getCategoryId() . '">' . $cat->getName() . '</option>';
					}
					$listCategory .= '<optgroup label="'.$parent->getName().'">'.$catOptions.'</optgroup>';
				}
			}
			$this->setData('categoryList', $listCategory);
		}
		return $this->getData('categoryList');
	}

	public function getFilters(){
		if($this->needFetchJobs()){
			$this->_fetchJobs();
		}
		$filters = array();
		if (!$this->getCategory()){
			$filter = $this->getLayout()->createBlock('job/search_filter');
			$filter->setCollection($this->getResults());
			$filter->setField('category_id');
			$filter->setDesticationField('category_id');
			$filter->setModel('job/category');
			$filter->setParamName('category');
			$filter->setTitle("Category");
			$filters['category'] = $filter;
		}
		
		if (!$this->getFunctionCategory()){
			$filter = $this->getLayout()->createBlock('job/search_filter');
			$filter->setCollection($this->getResults());
			$filter->setField('function_category_id');
			$filter->setDesticationField('parentcategory_id');
			$filter->setModel('job/parentcategory');
			$filter->setParamName('function_category');
			$filters['function_category'] = $filter;
			$filter->setTitle("Function Category");
		}

		if (!$this->getJobType()){
			$filter = $this->getLayout()->createBlock('job/search_filter');
			$filter->setCollection($this->getResults());
			$filter->setField('job_type');
			$filter->setDesticationField('type_id');
			$filter->setModel('job/type');
			$filter->setParamName('type');
			$filter->setTitle("Job Type");
			$filters['type'] = $filter;
		}
		return $filters;
	}
}
        