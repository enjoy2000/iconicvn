<?php

class Iconic_Job_Block_Createcv extends Mage_Core_Block_Template
{
    protected function _prepareLayout(){
		parent::_prepareLayout();
	}
	
	public function getFormData()
    {
        $data = new Varien_Object();
		$formdata = $this->getRequest()->getPost();
		$data->addData($formData);
        return $data;
    }
	
	public function getCustomer(){
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		return $customer;
	}
}