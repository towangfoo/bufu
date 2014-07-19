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
 * Symmetrics_CashTicket_Block_Error
 *
 * @category  Symmetrics
 * @package   Symmetrics_CashTicket
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Eugen Gitin <eg@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
*/
class Symmetrics_CashTicket_Block_Error extends Mage_Core_Block_Abstract
{
    /**
     * This block is shown when the customer
     * cancels the payment process from
     * CashTicket page
     *
     * @return string
     */
    protected function _toHtml()
    {
        $url = Mage::getUrl('checkout/onepage');
        
        $html = '';
        $message = 'Cash-Ticket payment was canceled. Redirecting to checkout...';
        $html .= '<p>' . Mage::helper('cashticket')->__($message) . '</p>';
        $html .= '<p><a href="' . $url . '">';
        $html .= Mage::helper('cashticket')->__('Click here to return to checkout') . '</a></p>';

        $html .= '<script language="javascript">';
        $html .= 'document.location.href="' . $url . '";';
        $html .= '</script>';

        return $html;
    }
}