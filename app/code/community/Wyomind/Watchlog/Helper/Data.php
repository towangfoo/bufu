<?php
/**
 * Copyright © 2017 Wyomind. All rights reserved.
 */
class Wyomind_Watchlog_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function checkNotification() 
    {
        $lastNotification = Mage::getStoreConfig('watchlogpro/settings/last_notification');
        $failedLimit = Mage::getStoreConfig('watchlogpro/settings/failed_limit');
        $percent = Mage::getResourceModel('watchlog/watchlog')->getFailedPercentFromDate($lastNotification)->getPercent();
        
        if ($percent > $failedLimit) {   
            // add notif in inbox
            $notificationTitle = Mage::getStoreConfig('watchlogpro/settings/notification_title');
            $notificationDescription = Mage::getStoreConfig('watchlogpro/settings/notification_description');
            $notificationLink = Mage::helper('adminhtml')->getUrl('adminhtml/notification/index');
            $watchlogLink = Mage::helper('adminhtml')->getUrl('adminhtml/basic/index');
            $date = Mage::getSingleton('core/date')->gmtDate('Y-m-d H:i:s');

            $notify = Mage::getModel('adminnotification/inbox');
            $item = $notify->getCollection()->addFieldToFilter('title', array('eq' => 'Watchlog security warning'))
                                            ->addFieldToFilter('is_remove', array('eq' => 0));
            $data = $item->getLastItem()->getData();

            if (isset($data['notification_id'])) {
                $notify->load($data['notification_id']);
                $notify->setUrl($notificationLink);
                $notify->setDescription($this->__($notificationDescription, round($percent * 100), $watchlogLink));
                $notify->setData('is_read', 0);
                $notify->save();
            } else {
                $notify->setUrl($notificationLink);
                $notify->setDescription($this->__($notificationDescription, round($percent * 100), $watchlogLink));
                $notify->setTitle($this->__($notificationTitle));
                $notify->setSeverity(1);
                $notify->setDateAdded($date);
                $notify->save();
            }
            
            // update watchlogpro/settings/last_notification config
            Mage::getConfig()->saveConfig('watchlogpro/settings/last_notification', $date, 'default', '0');
        }
    }

    public function checkWarning() 
    {
        $failedLimit = Mage::getStoreConfig('watchlogpro/settings/failed_limit');
        $percent = Mage::getResourceModel('watchlog/watchlog')->getFailedPercentFromDate()->getPercent();
        $notificationDetails = Mage::getStoreConfig('watchlogpro/settings/notification_details');
        
        if ($percent > $failedLimit) {
            Mage::getSingleton('core/session')->addError($this->__($notificationDetails, round($percent * 100)));
        }
    }
}