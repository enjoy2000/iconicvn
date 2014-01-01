<?php
class Iconic_Job_SearchController extends Mage_Core_Controller_Front_Action{
	
	public function indexAction(){		
        $this->loadLayout();
		
		$searchBlock = $this->getLayout()->getBlock("job_search");

		$q = $this->getRequest()->get("q");
		$searchBlock->setKeyword($q);
		
		$category = $this->getRequest()->get("category");
		if($category){
			$searchBlock->setCategory((int)$category);
		}
		
		$location = $this->getRequest()->get("location");
		if($location){
			$searchBlock->setLocation((int)$location);
		}
		
		$type = $this->getRequest()->get("type");
		if($type){
			$searchBlock->setJobType((int)$type);
		}
		
		$functionCategory = $this->getRequest()->get("function_category");
		if($functionCategory){
			$searchBlock->setFunctionCategory((int)$functionCategory);
		}
		
		$this->renderLayout();
	}
}