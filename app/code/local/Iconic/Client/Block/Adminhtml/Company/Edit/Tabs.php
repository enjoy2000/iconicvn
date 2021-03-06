<?php
 
class Iconic_Client_Block_Adminhtml_Company_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
 
    public function __construct()
    {
        parent::__construct();
        $this->setId('client_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('client')->__('Countries'));
    }
 
    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('client')->__('Company'),
            'title'     => Mage::helper('client')->__('Company Details'),
            'content'   => $this->getLayout()->createBlock('client/adminhtml_company_edit_tab_form')->toHtml(),
        ));
       
        return parent::_beforeToHtml();
    }
}