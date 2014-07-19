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
 * @package   Symmetrics_TweaksGerman
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Siegfried Schmitz <ss@symmetrics.de>
 * @copyright 2009-2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * Symmetrics_TweaksGerman_Helper_Data
 *
 * @category  Symmetrics
 * @package   Symmetrics_TweaksGerman
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Siegfried Schmitz <ss@symmetrics.de>
 * @copyright 2009-2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_TweaksGerman_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Path to the email legal notice
     *
     * @var string store config
     */
    const EMAILNOTICE_PATH = 'customer/create_account/emailnotice';
    
    /**
     * Path to enable switch
     *
     * @var string store config
     */
    const ENABLE_EMAILNOTICE_PATH = 'customer/create_account/enable_emailnotice';

    /**
     * Get email notice
     *
     * @return string emailnotice
     */
    public function getEmailNotice()
    {
        if (Mage::getStoreConfig(self::ENABLE_EMAILNOTICE_PATH)) {
            $emailNotice = Mage::getStoreConfig(self::EMAILNOTICE_PATH);
        } else {
            $emailNotice = '';
        }

        return $emailNotice;
    }
}
