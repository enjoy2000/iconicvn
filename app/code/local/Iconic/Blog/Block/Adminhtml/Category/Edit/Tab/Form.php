<?php
 
class Iconic_Blog_Block_Adminhtml_Category_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('category_form', array('legend'=>Mage::helper('blog')->__('Category information')));
       
        $fieldset->addField('name', 'text', array(
            'label'     => Mage::helper('blog')->__('Name'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'name',
        ));
		
		
		$parents = Mage::getModel('blog/parentcategory')->getCollection();
		foreach($parents as $cat){
			$arrayCat[] = array(
					'label'		=> $cat->getName(),
					'value' 	=> $cat->getParentcategoryId(),
					);	
		}
		$fieldset->addField('parentcategory_id', 'select', array(
            'label'     => Mage::helper('blog')->__('Parent Category'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'parentcategory_id',
            'values'	=> $arrayCat,
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