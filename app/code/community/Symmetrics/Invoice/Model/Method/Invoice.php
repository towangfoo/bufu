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
 * @package   Symmetrics_Invoice
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Eugen Gitin <eg@symmetrics.de>
 * @author    Yauhen Yakimovich <yy@symmetrics.de>
 * @copyright 2009-2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * Symmetrics_Invoice_Model_Method_Invoice
 *
 * @category  Symmetrics
 * @package   Symmetrics_Invoice
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Eugen Gitin <eg@symmetrics.de>
 * @author    Yauhen Yakimovich <yy@symmetrics.de>
 * @copyright 2009-2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_Invoice_Model_Method_Invoice extends Mage_Payment_Model_Method_Abstract
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code  = 'invoice';

    /**
     * Block type for payment form
     *
     * @var string
     */
    protected $_formBlockType = 'invoice/form_invoice';

    /**
     * Block type for payment info
     *
     * @var string
     */
    protected $_infoBlockType = 'invoice/info_invoice';

    /**
     * Get payment method code var value
     *
     * @return string code
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * Validate payment method information object
     *
     * @return Symmetrics_Invoice_Model_Method_Invoice
     */
    public function validate()
    {
         return $this;
    }

    /**
     * To check billing country is allowed for the payment method
     *
     * @param string $country country code
     *
     * @return bool
     */
    public function canUseForCountry($country)
    {
        //unused var, but cannot remove from params
        $country = null;
        $groupId = Mage::getSingleton('customer/session')->getCustomer()->getGroupId();
        $allowedGroups = explode(',', $this->getConfigData('specificgroup'));

        return in_array($groupId, $allowedGroups);
    }
}
