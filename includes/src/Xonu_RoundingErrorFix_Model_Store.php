<?php

/**
 * @copyright (c) 2013, Pawel Kazakow <support@xonu.de>
 * @license http://xonu.de/license/ xonu.de EULA
 */

class Xonu_RoundingErrorFix_Model_Store extends Mage_Core_Model_Store {

    private $classList = array();

    /**
     * Initialize object
     */
    protected function _construct()
    {
        $this->classList[] = get_class(Mage::getSingleton('salesrule/quote_discount'));
        parent::_construct();
    }

    /**
     * Round price
     *
     * @param mixed $price
     * @return double
     */
    public function roundPrice($price)
    {
        $trace = debug_backtrace(); $depth = 2;

        // if(in_array($trace[$depth]['class'], $this->classList))
        if(!empty($trace[$depth]['class']) && in_array($trace[$depth]['class'], $this->classList))
            $precision = 2;
        else
            $precision = 4;

        return round($price, $precision);
    }
}