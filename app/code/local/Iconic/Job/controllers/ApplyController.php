<?php
class Iconic_Job_ApplyController extends Mage_Core_Controller_Front_Action{
	
	public function indexAction(){		
        $this->loadLayout();  
		
		
		// redirect if user not login 
		if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $session = Mage::getSingleton('customer/session');
            $session->setAfterAuthUrl( Mage::helper('core/url')->getCurrentUrl() );
            $session->setBeforeAuthUrl( Mage::helper('core/url')->getCurrentUrl() );
            $this->_redirect('customer/account/login/');
            return $this;
        }
		
		if($id = $this->getRequest()->getParam('id')){
			$item = Mage::getModel('job/job')->load($id);
			if(!$item->getId()){
				$this->_redirect('/');
			}
			//set title by job title
			$this->getLayout()->getBlock('head')->setTitle($item->getTitle()); 
			//set item to block
			$this->getLayout()->getBlock('job_apply')->setItem($item);
			//set other varibles from other models			
			$this->getLayout()->getBlock('job_apply')->setCategory(Mage::getModel('job/category')->load($item->getCategoryId()));
			$this->getLayout()->getBlock('job_apply')->setFunctionCategory(Mage::getModel('job/category')->load($item->getFunctionCategoryId()));
			$this->getLayout()->getBlock('job_apply')->setLocation(Mage::getModel('job/location')->load($item->getLocationId()));
			$this->getLayout()->getBlock('job_apply')->setLevel(Mage::getModel('job/level')->load($item->getJobLevel()));
			$this->getLayout()->getBlock('job_apply')->setType(Mage::getModel('job/type')->load($item->getJobType()));
			
			//get jobs form same category
			$jobsInCategory = Mage::getModel('job/job')->getCollection()->addFieldToFilter('category_id',array('eq'=>$item->getCategoryId()))
					->setPageSize(20)
					->setCurPage(1)
					->load();
			$this->getLayout()->getBlock('job_apply')->setJobsInCategory($jobsInCategory);
		}
		       
		$this->renderLayout();
		   
	}

	public function uploadAction(){
		error_reporting(E_ALL | E_STRICT);
		require('UploadHandler.php');
		$upload_handler = new UploadHandler();
		$upload_handler->post();
	}
}
