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
 * @author    Yauhen Yakimovich <yy@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * Symmetrics totals tax renderer. Overrides standard rendering mechanism to
 * allow incl./excl. tax logic (specific for germany)
 *
 * @category  Symmetrics
 * @package   Symmetrics_TweaksGerman
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Yauhen Yakimovich <yy@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_TweaksGerman_Block_Tax_Render extends Mage_Checkout_Block_Total_Tax
{
    /**
     * Setter for totals
     *
     * @param Varien_Object $total totals data
     *
     * @return void
     */
     public function setTotal($total)
     {
        parent::setTotal($total);
        $store = $this->getStore();
        if ($total->getCode() == 'tax') {
            $taxHelper = Mage::helper('tax');
            if ($taxHelper->displaySalesPriceInclTax($store)) {
                $title = Mage::helper('tweaksgerman')->__('Incl. VAT');
                $total->setTitle($title);
            } else if ($taxHelper->displaySalesPriceExclTax($store)) {
                $title = Mage::helper('tweaksgerman')->__('Excl. VAT');
                $total->setTitle($title);
            }
        }
        
        return $this;
     }
}

