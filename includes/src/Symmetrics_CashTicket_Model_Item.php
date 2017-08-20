<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category  Symmetrics
 * @package   Symmetrics_CashTicket
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Eugen Gitin <eg@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * Symmetrics_CashTicket_Model_Item
 *
 * @category  Symmetrics
 * @package   Symmetrics_CashTicket
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Eugen Gitin <eg@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
*/
class Symmetrics_CashTicket_Model_Item extends Mage_Core_Model_Abstract
{
    /**
     * Construct
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('cashticket/item');
    }

    /**
     * Get config item object
     * and load by currency
     *
     * @param string $currency Currency
     *
     * @return array
     */
    public function getConfigItem($currency)
    {
        $ids = $this->getItemsCollection($currency)->getAllIds();
        return $this->load($ids[0])->toArray();
    }
    
    /**
     * Get all enabled config items
     * for current currency
     *
     * @param string $currency Currency
     *
     * @return object
     */
    public function getItemsCollection($currency)
    {
        $collection = $this->getCollection()
            ->addFieldToFilter(
                'currency_code', 
                $currency
            )
            ->addFieldToFilter(
                'enable', 
                1
            );

        return $collection;
    }
}