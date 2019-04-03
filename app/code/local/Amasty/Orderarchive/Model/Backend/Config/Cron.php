<?php
 /**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Orderarchive
 */

class Amasty_Orderarchive_Model_Backend_Config_Cron extends Mage_Core_Model_Config_Data
{
    const CRON_STRING_PATH = 'crontab/jobs/amorderarchive_archiving/schedule/cron_expr';

    protected function _afterSave()
    {
        $frequency = $this->getData('groups/general/fields/frequency/value');

        $cronExprString = Amasty_Orderarchive_Model_Backend_Source_Frequency::generateCronShedule($frequency);

        try {
            Mage::getModel('core/config_data')
                ->load(self::CRON_STRING_PATH, 'path')
                ->setValue($cronExprString)
                ->setPath(self::CRON_STRING_PATH)
                ->save();
        }
        catch (Exception $e) {
            throw new Exception(Mage::helper('cron')->__('Unable to save the cron expression.'));

        }
    }
}