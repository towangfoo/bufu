<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Orderarchive
 */


class Amasty_Orderarchive_Model_Backend_Source_Frequency
{
    protected static $_options;

    const CRON_HOURLY          = 'H';
    const CRON_TO_TIME_PER_DAY = '2TD';
    const CRON_DAILY           = 'D';
    const CRON_WEEKLY          = 'W';
    const CRON_MONTHLY         = 'M';

    public function toOptionArray()
    {
        if (!self::$_options) {
            self::$_options = array(
                array(
                    'label' => Mage::helper('cron')->__('Hourly'),
                    'value' => self::CRON_HOURLY,
                ),
                array(
                    'label' => Mage::helper('cron')->__('Two Times Per Day'),
                    'value' => self::CRON_TO_TIME_PER_DAY,
                ),
                array(
                    'label' => Mage::helper('cron')->__('Daily'),
                    'value' => self::CRON_DAILY,
                ),
                array(
                    'label' => Mage::helper('cron')->__('Weekly'),
                    'value' => self::CRON_WEEKLY,
                ),
                array(
                    'label' => Mage::helper('cron')->__('Monthly'),
                    'value' => self::CRON_MONTHLY,
                ),
            );
        }
        return self::$_options;
    }

    /**
     * get shedule
     * @param string $frequency
     * @return string
     */
    public static function generateCronShedule($frequency)
    {
        switch($frequency)
        {
            case self::CRON_HOURLY:
                // Every hour in 0 minutes
                $cronExpString = "0 * * * * *";
                break;
            case self::CRON_TO_TIME_PER_DAY:
                // Every 12 hours
                $cronExpString = '0 */12 * * *';
                break;
            case self::CRON_DAILY:
                //Every day in 00:00
                $cronExpString = "0 0 * * * *";
                break;
            case self::CRON_WEEKLY:
                $cronExpString = "0 0 * * 0";
                break;
            case self::CRON_MONTHLY:
                $cronExpString = "0 0 1 * *";
                break;
            default:
                $cronExpString = '*/10 * * * * *';
                break;
        }
        return $cronExpString;

    }
}
