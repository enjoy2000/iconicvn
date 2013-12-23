<?php
 
class Iconic_Job_Block_Adminhtml_Location_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
 
    public function __construct()
    {
        parent::__construct();
        $this->setId('job_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('job')->__('Locations'));
    }
 
    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('job')->__('Location'),
            'title'     => Mage::helper('job')->__('Location Details'),
            'content'   => $this->getLayout()->createBlock('job/adminhtml_location_edit_tab_form')->toHtml(),
        ));
       
        return parent::_beforeToHtml();
    }
}