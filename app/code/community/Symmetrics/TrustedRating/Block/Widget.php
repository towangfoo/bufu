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
 * @copyright 2009-2014 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://github.com/symmetrics/trustedshops_trustedrating/
 * @link      http://www.symmetrics.de/
 * @link      http://www.de.cgi.com/
 */

/**
 * Generate the rating widget
 *
 * @category  Symmetrics
 * @package   Symmetrics_TrustedRating
 * @author    symmetrics - a CGI Group brand <info@symmetrics.de>
 * @author    Siegfried Schmitz <ss@symmetrics.de>
 * @copyright 2009-2014 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://github.com/symmetrics/trustedshops_trustedrating/
 * @link      http://www.symmetrics.de/
 * @link      http://www.de.cgi.com/
 */
class Symmetrics_TrustedRating_Block_Widget extends Symmetrics_TrustedRating_Block_Widget_Abstract
{
    /**
     * Generate rating link
     *
     * @return string
     */
    public function getRatingLink()
    {
        $link = '';
        if ($data = $this->getDataForWidget('RATING')) {
            $link = $data['ratingLink'] . '_' . $data['tsId'] . '.html';
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
        if ($data = $this->getDataForWidget('RATING')) {
            $baseUrl = Mage::getBaseUrl('web');
            $widgetSrc = $baseUrl . $data['imageLocalPath'] . $data['tsId'] . '.gif';
        }
        return $widgetSrc;
    }
}
