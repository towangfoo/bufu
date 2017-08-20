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
 * @package   Symmetrics_Buyerprotect
 * @author    symmetrics - a CGI Group brand <info@symmetrics.de>
 * @author    Torsten Walluhn <tw@symmetrics.de>
 * @author    Toni Stache <ts@symmetrics.de>
 * @copyright 2010-2013 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * Block class to render the beadge on the right sidebar
 *
 * @category  Symmetrics
 * @package   Symmetrics_Buyerprotect
 * @author    symmetrics - a CGI Group brand <info@symmetrics.de>
 * @author    Torsten Walluhn <tw@symmetrics.de>
 * @author    Toni Stache <ts@symmetrics.de>
 * @copyright 2010-2013 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_Buyerprotect_Block_Badge extends Mage_Core_Block_Template
{

    /**
     * Check if Buyerprotection is enabled, has a valid user id and TS logo/image
     * exists.
     *
     * @return boolean
     */
    protected function isValid()
    {
        $helper = Mage::helper('buyerprotect');
        /* @var $helper Symmetrics_Buyerprotect_Helper_Data */
        $configData = $helper->getConfigData();

        if (!isset($configData['ts_logo_img'], $configData['ts_background_img'])) {
            return false;
        }

        $pattern = '!^X[A-Za-z0-9]{32}$!imsU';

        if (preg_match($pattern, $configData['trustedshops_id'])
            && !empty($configData['ts_logo_img'])
            && !empty($configData['ts_background_img'])
            && $configData['trustedshops_certificate_logo_active'] == 1) {
            return true;
        }

        return false;
    }

    /**
     * Returns the seal code with adapted design and paths for magento
     *
     * @return string
     */
    public function getSeal()
    {
        $helper = Mage::helper('buyerprotect');
        /* @var $helper Symmetrics_Buyerprotect_Helper_Data */

        $configData = $helper->getConfigData();

        $seal = $configData['trustedshops_certificate_logo_code'];

        $new = 'class="box" ';
        $pos = 'id="tsBox"';
        $seal = str_replace($pos, $new . $pos, $seal);

        // You can not simply merge code to dev branch! But when you do,
        // make sure that you validate the code before in all MRG using
        // shops the logo and background are missing.
        $logo = Mage::getBaseUrl('media') . '/trustedshops/' . $configData['ts_logo_img'];
        $background = Mage::getBaseUrl('media') . '/trustedshops/' . $configData['ts_background_img'];

        $search = array();
        $search[] = '!(\<img\s{1,}style\=\"(?:.*)\"\s{1,}src\=\")(?:.*\/trustedshop.*\..{3,4})\"!imsU';
        $search[] = '/images\/bg_(yellow|grey|blue).jpg/i';
        $search[] = '/width:[0-9]+(?:\.[0-9]*)?px/i';
        $search[] = '/border:1px solid #C0C0C0/i';
        $search[] = '/padding:2px/i';
        $search[] = '!(\<div.*)(width:[0-9]*\%\;)(.*)(padding:[0-9]*px)(.*id=\"tsInnerBox\".*\>)!i';

        $replace = array();
        $replace[] = '$1' . $logo . '"';
        $replace[] = $background;
        $replace[] = 'width:100%';
        $replace[] = 'border:0px';
        $replace[] = 'padding:0px';
        $replace[] = '$1$3padding:5px$5';
        $seal = preg_replace($search, $replace, $seal);

        return $seal;
    }

}
