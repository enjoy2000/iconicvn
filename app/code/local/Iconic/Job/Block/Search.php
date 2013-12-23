<?php

class Iconic_Job_Block_Search extends Mage_Core_Block_Template
{
	protected function _prepareLayout(){
		
		if($this->getRequest()->getQuery('s')){
		    $posts = Mage::getModel('job/job')->getCollection()->addFieldToFilter('status', $this->getRequest()->getQuery('s'));
		}
        else{
            $posts = Mage::getModel('job/job')->getCollection()->addFieldToFilter('status', 'available');
        }
        $this->setPosts($posts);
		parent::_prepareLayout();
	}
}
        