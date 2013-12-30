<?php

class Iconic_Job_Block_Search extends Mage_Core_Block_Template
{
	protected function _prepareLayout(){
			
		$this->setPost($this->getRequest()->getPost());
		
		//get list location and category
		$location = Mage::getModel('job/location')->getCollection();
		$listLocation = '';
		foreach($location as $loc){
			$listLocation .= '<option value="' . $loc->getLocationId() . '">' . $loc->getName() . '</option>';
		}
		$this->setLocationList($listLocation);
		
		$parentCategory = Mage::getModel('job/parentcategory')->getCollection();
		$listCategory = '';
		foreach($parentCategory as $parent){
			$categories = Mage::getModel('job/category')->getCollection()->addFieldToFilter('parentcategory_id', array('eq'=>$parent->getParentcategoryId()));
			$catOptions = '';
			foreach($categories as $cat){
				$catOptions .= '<option value="' . $cat->getCategoryId() . '">' . $cat->getName() . '</option>';
			}
			$listCategory .= '<optgroup label="'.$parent->getName().'">'.$catOptions.'</optgroup>';
		}
		$this->setCategoryList($listCategory);
		
		parent::_prepareLayout();
	}
}
        