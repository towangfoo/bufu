<?php
/**
 * Copyright © 2017 Wyomind. All rights reserved.
 */
class Wyomind_Watchlog_Block_Adminhtml_Basic_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct() 
    {
        parent::__construct();
        $this->setId('watchlogGrid');
        $this->setDefaultSort('date');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() 
    {
        $collection = Mage::getModel('watchlog/watchlog')->getCollection();
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
            'renderer'  => 'Wyomind_Watchlog_Block_Adminhtml_Renderer_Ip',
            'index'     => 'ip'
            )
        );
        
        $this->addColumn(
            'date', array(
            'header'    => $watchlogHelper->__('Date'),
            'index'     => 'date',
            'width'     => '200px',
            'type'      => 'datetime'
            )
        );

        $this->addColumn(
            'login', array(
            'header'    => $watchlogHelper->__('Login'),
            'index'     => 'login',
            'width'     => '200px'
            )
        );
        
        $this->addColumn(
            'message', array(
            'header'    => $watchlogHelper->__('Message'),
            'index'     => 'message',
            'width'     => '200px'
            )
        );
        
        $this->addColumn(
            'url', array(
            'header'    => $watchlogHelper->__('Url'),
            'index'     => 'url'
            )
        );
        
        $this->addColumn(
            'type', array(
            'header'    => $watchlogHelper->__('Status'),
            'index'     => 'type',
            'renderer'  => 'Wyomind_Watchlog_Block_Adminhtml_Renderer_Status',
            'width'     => '100px',
            'filter'    => false
            )
        );

        return parent::_prepareColumns();
    }

    public function getRowUrl($row) 
    {
        return '#';
    }
}