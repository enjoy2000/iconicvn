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
		
		$level = $this->getRequest()->get("level");
		if($level){
			$searchBlock->setJobLevel((int)$level);
		}
		
		$functionCategory = $this->getRequest()->get("function_category");
		if($functionCategory){
			$searchBlock->setFunctionCategory((int)$functionCategory);
		}
		
		$industry = $this->getRequest()->get("industry");
		if($industry){
			$searchBlock->setIndustry((int)$industry);
		}
		
		$function = $this->getRequest()->get("function");
		if($function){
			$searchBlock->setIndustry((int)$function);
		}
		
		$this->renderLayout();
	}
}