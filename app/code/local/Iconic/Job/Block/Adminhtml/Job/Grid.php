<?php
 
class Iconic_Job_Block_Adminhtml_Job_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('jobGrid');
        // This is the primary key of the database
        $this->setDefaultSort('job_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
 
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('job/job')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
 
    protected function _prepareColumns()
    {
        $this->addColumn('job_id', array(
            'header'    => Mage::helper('job')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'job_id',
        ));
 
        $this->addColumn('title', array(
            'header'    => Mage::helper('job')->__('Title'),
            'align'     =>'left',
            'index'     => 'title',
        ));
        
        $this->addColumn('category_id', array(
            'header'    => Mage::helper('job')->__('Category'),
            'width'     => '50px',
            'index'     => 'category_id',
        ));
                
        $this->addColumn('location_id', array(
            'header'    => Mage::helper('job')->__('Location'),
            'width'     => '50px',
            'index'     => 'location_id',
        ));
 
        $this->addColumn('created_time', array(
            'header'    => Mage::helper('job')->__('Creation Time'),
            'align'     => 'left',
            'width'     => '80px',
            'type'      => 'date',
            'default'   => '--',
            'index'     => 'created_time',
        ));
 
        return parent::_prepareColumns();
    }


    protected function _prepareMassaction()
    {
    	$this->setMassactionIdField('job_id');
    	$this->getMassactionBlock()->setFormFieldName('job_id');
    
    	$this->getMassactionBlock()->addItem('delete', array(
    			'label'    => Mage::helper('job')->__('Delete'),
    			'url'      => $this->getUrl('*/*/massDelete'),
    			'confirm'  => Mage::helper('job')->__('Are you sure?')
    	));
    
    	$this->getMassactionBlock()->addItem('awaiting', array(
    			'label'    => Mage::helper('job')->__('Awaiting'),
    			'url'      => $this->getUrl('*/*/massawaiting')
    	));
    
    	$this->getMassactionBlock()->addItem('available', array(
    			'label'    => Mage::helper('job')->__('Available'),
    			'url'      => $this->getUrl('*/*/massAvailable')
    	));
        
        $this->getMassactionBlock()->addItem('waiting', array(
    			'label'    => Mage::helper('job')->__('Waiting'),
    			'url'      => $this->getUrl('*/*/massWaiting')
    	));
        
        $this->getMassactionBlock()->addItem('closed', array(
    			'label'    => Mage::helper('job')->__('Closed'),
    			'url'      => $this->getUrl('*/*/massClosed')
    	));
        
        $this->getMassactionBlock()->addItem('rejected', array(
    			'label'    => Mage::helper('job')->__('Rejected'),
    			'url'      => $this->getUrl('*/*/massRejected')
    	));
    
    	return $this;
    }
 
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
 
    public function getGridUrl()
    {
      return $this->getUrl('*/*/grid', array('_current'=>true));
    }
 
 
}