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
			$block = $this->getLayout()->getBlock('job_apply');
			//set user
			$user = Mage::getSingleton('customer/session')->getCustomer();
			$block->setUser($user);
			//set title by job title
			$this->getLayout()->getBlock('head')->setTitle(Mage::helper('job')->__('Ứng tuyển').' '.$item->getTitle()); 
			//set item to block
			$block->setItem($item);
			//set other varibles from other models			
			$block->setCategory(Mage::getModel('job/category')->load($item->getCategoryId()));
			$block->setFunctionCategory(Mage::getModel('job/category')->load($item->getFunctionCategoryId()));
			$block->setLocation(Mage::getModel('job/location')->load($item->getLocationId()));
			$block->setLevel(Mage::getModel('job/level')->load($item->getJobLevel()));
			$block->setType(Mage::getModel('job/type')->load($item->getJobType()));
			
			//get jobs form same category
			$jobsInCategory = Mage::getModel('job/job')->getCollection()->addFieldToFilter('category_id',array('eq'=>$item->getCategoryId()))
					->setPageSize(20)
					->setCurPage(1)
					->load();
			$block->setJobsInCategory($jobsInCategory);
		}
		       
		$this->renderLayout();
		   
	}

	public function uploadAction(){
		error_reporting(E_ALL | E_STRICT);
		require('UploadHandler.php');
		$upload_handler = new UploadHandler();
		$user = Mage::getSingleton('customer/session')->getCustomer();
		$upload_handler->options['upload_dir'] = Mage::getBaseDir().'/files/'.$user->getId().'/';
		$upload_handler->options['upload_url'] = Mage::getBaseUrl().'files/'.$user->getId().'/';
		$upload_handler->options['accept_file_types'] = '/\.(gif|jpe?g|png|doc?x|xls?x|ppt?x|pdf)$/i';
		$upload_handler->initialize();
	}
	
	public function sendAction(){
		$this->loadLayout();
		
		// redirect if user not login 
		if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $session = Mage::getSingleton('customer/session');
            $session->setAfterAuthUrl( Mage::helper('core/url')->getCurrentUrl() );
            $session->setBeforeAuthUrl( Mage::helper('core/url')->getCurrentUrl() );
            $this->_redirect('customer/account/login/');
            return $this;
        }
		
		$user = Mage::getSingleton('customer/session')->getCustomer();
		
		$mail = new Zend_Mail('UTF-8');
		$data = $this->getRequest()->getPost();
		foreach($data['files'] as $filename){
			$fileContents = file_get_contents(Mage::getBaseUrl().$user->getId().'/'.$filename);
			$mail->createAttachment($fileContents);
		}
		/*
		$config = array(
                    'auth' => 'login',
				    'ssl' => 'tls',
				    'username' => 'email2@domain.com',
				    'password' => 'mypasshere'
					);
 
		$transport = new Zend_Mail_Transport_Smtp('smtp.gmail.com', $config);
		*/
		$mail->setBodyText('This is the text of the mail.');
		$mail->setFrom('sender@test.com', 'Some Sender');
		$mail->addTo('enjoy3013@gmail.com', 'Some Recipient');
		$mail->setSubject('TestSubject');
		$mail->send($transport);
		
		$this->renderLayout();
	}
}
