<?php
/**
 * Copyright © 2017 Wyomind. All rights reserved.
 */
class Wyomind_Watchlog_Block_Adminhtml_Chart extends Mage_Adminhtml_Block_Widget_Container
{
    const FAIL = 0;
    const SUCCESS = 1;
    const BLOCKED = 2;

    public function __construct() 
    {
        $this->_controller = 'adminhtml_chart';
        $this->_blockGroup = 'watchlog';

        parent::__construct();

        $this->setTemplate('watchlog/chart.phtml');
    }

    public function getChartDataSummaryMonth() 
    {
        $coreHelper = Mage::helper('core');
        $watchlogHelper = Mage::helper('watchlog');
        $pro = $coreHelper->isModuleEnabled('Wyomind_Watchlogpro') 
                && $coreHelper->isModuleOutputEnabled('Wyomind_Watchlogpro') 
                && Mage::getConfig()->getNode('modules/Wyomind_Watchlogpro/active');

        $data = array();
        $headers = array(
            $watchlogHelper->__('Date'),
            $watchlogHelper->__('Success'),
            $watchlogHelper->__('Failed')
        );
        
        if ($pro) {
            $headers[] = $watchlogHelper->__('Blocked');
        }

        $data[] = $headers;
        $tmpData = array();
        $currentTimestamp = Mage::getModel('core/date')->gmtTimestamp();
        $yestermonthTimestamp = $currentTimestamp - 29 * 24 * 60 * 60;
        
        while ($yestermonthTimestamp <= $currentTimestamp) {
            $key = Mage::getModel('core/date')->date('Y-m-d', $yestermonthTimestamp);
            $tmpData[$key] = array(self::FAIL => 0, self::SUCCESS => 0, self::BLOCKED => 0);
            $yestermonthTimestamp += 24 * 60 * 60;
        }

        $collection = Mage::getResourceModel('watchlog/watchlog')->getSummaryMonth();
        foreach ($collection as $entry) {
            $key = Mage::getModel('core/date')->date('Y-m-d', strtotime($entry->getDate()));
            if (!isset($tmpData[$key])) {
                $tmpData[$key] = array(self::FAIL => 0, self::SUCCESS => 0, self::BLOCKED => 0);
            }
            $tmpData[$key][$entry->getType()] = $entry->getNb();
        }
        ksort($tmpData);
        
        foreach ($tmpData as $date => $entry) {
            if ($pro) {
                $data[] = array(
                    "#new Date('" . $date . "')#", 
                    (int) $entry[self::SUCCESS], 
                    (int) $entry[self::FAIL], 
                    (int) $entry[self::BLOCKED]
                );
            } else {
                $data[] = array(
                    "#new Date('" . $date . "')#", 
                    (int) $entry[self::SUCCESS], 
                    (int) $entry[self::FAIL]
                );
            }
        }

        return $data;
    }

    public function getChartDataSummaryDay() 
    {
        $coreHelper = Mage::helper('core');
        $watchlogHelper = Mage::helper('watchlog');
        $pro = $coreHelper->isModuleEnabled('Wyomind_Watchlogpro') 
                && $coreHelper->isModuleOutputEnabled('Wyomind_Watchlogpro') 
                && Mage::getConfig()->getNode('modules/Wyomind_Watchlogpro/active');

        $data = array();
        $headers = array(
            $watchlogHelper->__('Date'), 
            $watchlogHelper->__('Success'), 
            $watchlogHelper->__('Failed')
        );
        
        if ($pro) {
            $headers[] = $watchlogHelper->__('Blocked');
        }

        $data[] = $headers;
        $tmpData = array();
        $currentTimestamp = Mage::getModel('core/date')->gmtTimestamp();
        $yesterdayTimestamp = $currentTimestamp - 23 * 60 * 60;
        
        while ($yesterdayTimestamp <= $currentTimestamp) {
            $key = Mage::getModel('core/date')->date('M d, Y H:00:00', $yesterdayTimestamp);
            $tmpData[$key] = array(self::FAIL => 0, self::SUCCESS => 0, self::BLOCKED => 0);
            $yesterdayTimestamp += 60 * 60;
        }

        $collection = Mage::getResourceModel('watchlog/watchlog')->getSummaryDay();
        foreach ($collection as $entry) {
            $key = Mage::getModel('core/date')->date('M d, Y H:00:00', strtotime($entry->getDate()));
            if (!isset($tmpData[$key])) {
                $tmpData[$key] = array(self::FAIL => 0, self::SUCCESS => 0, self::BLOCKED => 0);
            }
            $tmpData[$key][$entry->getType()] = $entry->getNb();
        }

        foreach ($tmpData as $date => $entry) {
            if ($pro) {
                $data[] = array(
                    "#new Date('" . $date . "')#", 
                    (int) $entry[self::SUCCESS], 
                    (int) $entry[self::FAIL], 
                    (int) $entry[self::BLOCKED]
                );
            } else {
                $data[] = array(
                    "#new Date('" . $date . "')#", 
                    (int) $entry[self::SUCCESS], 
                    (int) $entry[self::FAIL]
                );
            }
        }
        
        return $data;
    }
}