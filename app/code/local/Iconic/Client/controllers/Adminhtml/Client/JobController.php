<?php
 
class Iconic_Client_Adminhtml_Client_JobController extends Mage_Adminhtml_Controller_Action
{
 
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('client/items')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
        return $this;
    }   
   
    public function indexAction() {
        $this->_initAction();    
        $this->_addContent($this->getLayout()->createBlock('client/adminhtml_job'));
        $this->renderLayout();
    }
    public function editAction()
    {
        $jobId     = $this->getRequest()->getParam('id');
        $jobModel  = Mage::getModel('job/job')->load($jobId);
 
        if ($jobModel->getId() || $jobId == 0) {
 
            Mage::register('job_data', $jobModel);
 
            $this->loadLayout();
            $this->_setActiveMenu('client/items');
           
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));
           
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
           
            $this->_addContent($this->getLayout()->createBlock('client/adminhtml_job_edit'))
                 ->_addLeft($this->getLayout()->createBlock('client/adminhtml_job_edit_tabs'));
               
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('job')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }
   
    public function newAction()
    {
        $this->_forward('edit');
    }
   
    public function saveAction()
    {
        if ( $this->getRequest()->getPost() ) {
            try {              
                
                $postData = $this->getRequest()->getPost();
                $jobModel = Mage::getModel('job/job');
                $currentDate = Date('Y-m-d H:i:s');
                $jobModel->setData($postData)
	                     ->setId($this->getRequest()->getParam('id'))
						 ->setUpdateTime($currentDate)
						 ->setLocationId(','.$this->getRequest()->getParam('location_id').',')
						 ->setLanguageId(','.implode(',', $this->getRequest()->getParam('language_id')).',')
					     ->setFeatureId(','.implode(',', $this->getRequest()->getParam('feature_id')).',');
						 
                if(!$this->getRequest()->getParam('id')){
                	$jobModel->setCreatedTime($currentDate);
                }
				$jobModel->save();
				
				if(!$this->getRequest()->getParam('id')){
                	$jobModel->setCreatedTime($currentDate);
					$newjob = Mage::getModel('job/job')->getCollection()->getLastItem();
                }else{
                	$newjob = Mage::getModel('job/job')->load($this->getRequest()->getParam('id'));
                }
				//$newjob->setLanguageId(','.implode(',', $this->getRequest()->getParam('language_id')).',')
				//	   ->setFeatureId(','.implode(',', $this->getRequest()->getParam('feature_id')).',')
				//	   ->save();
				//set url key
				//if($postData['url_key']){
					//$urlkey = Mage::helper('job')->formatUrlKey($postData['url_key']);
				//}else{
					//$urlkey = Mage::helper('job')->formatUrlKey($postData['title']);
				//}
				//$jobModel->setUrlKey($urlkey)->save();
                                
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setJobData(false);
 
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setJobData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }
   
    public function deleteAction()
    {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $jobModel = Mage::getModel('job/job');
               
                $jobModel->setId($this->getRequest()->getParam('id'))
                    ->delete();
                   
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }
    /**
     * Product grid for AJAX request.
     * Sort and filter result for example.
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
               $this->getLayout()->createBlock('client/adminhtml_job_grid')->toHtml()
        );
    }
    
    public function massDeleteAction()
    {
        if(is_array($this->getRequest()->getParam('job_id'))) {
        	try{
                $jobIds = $this->getRequest()->get('job_id');
            	foreach($jobIds as $k => $v){
            	   Mage::getModel('job/job')->setId($v)->delete();
            	}
               Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item(s) were successfully deleted'));
               $this->_redirect('*/*/');
            }catch(Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/');
            }
        }
        $this->_redirect('*/*/');
    }
}