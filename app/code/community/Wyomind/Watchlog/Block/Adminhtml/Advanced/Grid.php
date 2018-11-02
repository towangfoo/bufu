<?php
/**
 * Copyright © 2017 Wyomind. All rights reserved.
 */
class Wyomind_Watchlog_Block_Adminhtml_Advanced_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct() 
    {
        parent::__construct();
        $this->setId('watchlogGrid');
        $this->setDefaultSort('attempts');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() 
    {
        $collection = Mage::getResourceModel('watchlog/watchlog')->getSummary();
        

        $this->setCollection($collection);
        
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns() 
    {
        $watchlogHelper = Mage::helper('watchlog');
        
        $this->addColumn(
            'ip', array(
            'header'    => $watchlogHelper->__('IP'),
            'width'     => '100px',
            'align'     => 'center',
            'type'      => 'text',
            'renderer'  => 'Wyomind_Watchlog_Block_Adminhtml_Renderer_Ip',
            'index'     => 'ip',
            'sortable'  => false
            )
        );
        
        $this->addColumn(
            'date', array(
            'header'    => $watchlogHelper->__('Last attempt'),
            'width'     => 'auto',
            'type'      => 'datetime',
            'index'     => 'date',
            'sortable'  => false
            )
        );
        
        $this->addColumn(
            'attempts', array(
            'header'    => $watchlogHelper->__('Attempts'),
            'width'     => '100px',
            'type'      => 'number',
            'index'     => 'attempts',
            'filter'    => false,
            'sortable'  => false
            )
        );
        
        $this->addColumn(
            'failed', array(
            'header'    => $watchlogHelper->__('Failed'),
            'width'     => '100px',
            'type'      => 'number',
            'index'     => 'failed',
            'filter'    => false,
            'sortable'  => false
            )
        );
        
        $this->addColumn(
            'succeeded', array(
            'header'    => $watchlogHelper->__('Succeeded'),
            'width'     => '100px',
            'type'      => 'number',
            'index'     => 'succeeded',
            'filter'    => false,
            'sortable'  => false
            )
        );
        
        return parent::_prepareColumns();
    }
    
    public function getRowUrl($row) 
    {
        return '#';
    }
}