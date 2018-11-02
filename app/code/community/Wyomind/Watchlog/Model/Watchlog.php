<?php
/**
 * Copyright © 2017 Wyomind. All rights reserved.
 */
class Wyomind_Watchlog_Model_Watchlog extends Mage_Core_Model_Abstract
{
    protected function _construct() 
    {
        $this->_init('watchlog/watchlog');
    }
}