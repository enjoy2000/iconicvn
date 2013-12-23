<?php
 
class Iconic_Job_Block_Adminhtml_Category_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('caetgory_form', array('legend'=>Mage::helper('job')->__('Category information')));
       
        $fieldset->addField('name', 'text', array(
            'label'     => Mage::helper('job')->__('Name'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'name',
        ));
		
		$parentCategoryModel = Mage::getModel('job/parentcategory')->getCollection();
		foreach($parentCategoryModel as $cat){
			$parentCategoryValues[] = array(
										'value' => $cat->getParentCategoryId(),
										'label' => $cat->getName(),
			);
		}
		$fieldset->addField('parent_category_id', 'select', array(
            'label'     => Mage::helper('job')->__('Parent Category'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'parent_category_id',
            'values'	=> $parentCategoryValues,
        ));
 
        if ( Mage::getSingleton('adminhtml/session')->getCategoryData() )
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getCategoryData());
            Mage::getSingleton('adminhtml/session')->setCategoryData(null);
        } elseif ( Mage::registry('category_data') ) {
            $form->setValues(Mage::registry('category_data')->getData());
        }
        return parent::_prepareForm();
    }
}