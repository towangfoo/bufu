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
 * @package   Symmetrics_TrustedRating
 * @author    symmetrics - a CGI Group brand <info@symmetrics.de>
 * @author    Siegfried Schmitz <ss@symmetrics.de>
 * @author    Ngoc Anh Doan <ngoc-anh.doan@cgi.com>
 * @copyright 2009-2014 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://github.com/symmetrics/trustedshops_trustedrating/
 * @link      http://www.symmetrics.de/
 * @link      http://www.de.cgi.com/
 */

/**
 * Generate the email widget
 *
 * @category  Symmetrics
 * @package   Symmetrics_TrustedRating
 * @author    symmetrics - a CGI Group brand <info@symmetrics.de>
 * @author    Siegfried Schmitz <ss@symmetrics.de>
 * @author    Ngoc Anh Doan <ngoc-anh.doan@cgi.com>
 * @copyright 2009-2014 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://github.com/symmetrics/trustedshops_trustedrating/
 * @link      http://www.symmetrics.de/
 * @link      http://www.de.cgi.com/
 */
class Symmetrics_TrustedRating_Block_Email_Widget extends Symmetrics_TrustedRating_Block_Widget_Abstract
{
    /**
     * Generate rating link
     *
     * @return string
     */
    public function getRatingLink()
    {
        $link = '';
        if ($data = $this->getDataForWidget('EMAIL')) {
            $buyerEmail = base64_encode($data['buyerEmail']);
            $orderId = base64_encode($data['orderId']);
            // Do not change the query string separator, it's necessary to use the ampersand (&)
            // for the TS systems to handle the query parameters correctly.
            $link = $data['ratingLink'] . '_' . $data['tsId'] . '.html&buyerEmail=';
            $link .= $buyerEmail . '&shopOrderID=' . $orderId;
        }
        return $link;
    }

    /**
     * Generate widget image source
     *
     * @return string
     */
    public function getWidgetSource()
    {
        $widgetSrc = '';
        if ($data = $this->getDataForWidget('EMAIL')) {
            $baseUrl = Mage::getBaseUrl('web');
            $widgetSrc = $baseUrl . $data['imageLocalPath'] . $data['widgetName'];

        }
        return $widgetSrc;
    }
}
