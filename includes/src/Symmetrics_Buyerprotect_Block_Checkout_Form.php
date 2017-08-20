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
 * @author    Benjamin Klein <bk@symmetrics.de>
 * @copyright 2010-2013 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * Block class to manage the functionality of the Buyerprotection form in payment section
 * of the checkout
 *
 * @category  Symmetrics
 * @package   Symmetrics_Buyerprotect
 * @author    symmetrics - a CGI Group brand <info@symmetrics.de>
 * @author    Torsten Walluhn <tw@symmetrics.de>
 * @author    Benjamin Klein <bk@symmetrics.de>
 * @copyright 2010-2013 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_Buyerprotect_Block_Checkout_Form extends Mage_Core_Block_Template
{
    /**
     * @const CLASSIC_CERTIFICATE_URL URL of the form to check the seal of approval.
     */
    const CLASSIC_CERTIFICATE_URL = 'https://www.trustedshops.com/shop/certificate.php';
    
    /**
     * @const CLASSIC_SUBSCRIBE_URL URL of the form to subscribe to classic buyer protection.
     */
    const CLASSIC_SUBSCRIBE_URL = 'https://www.trustedshops.com/shop/protection.php';
    
    /**
     * @const FORM_ENCODING Encoding of subscription form.
     */
    const FORM_ENCODING = 'UTF-8';
    
    /**
     * Check if Trusted Shops - Excellence Buyerprotection form can be shown in checkout.
     * 
     * @return boolean
     */
    public function showForm()
    {
        $helper = Mage::helper('buyerprotect');
        /* @var $helper Symmetrics_Buyerprotect_Helper_Data */

        /*
         * Get sure service is activated and store has products of type
         * Symmetrics_Buyerprotect_Model_Type_Buyerprotect::TYPE_BUYERPROTECT
         */
        if ($helper->isBuyerprotectActive() && $helper->getAllTsProductTypes()) {
            return true;
        }

        return false;
    }

    /**
     * Get Product collection of all products with type buyerprotect.
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    public function getAllTsProducts()
    {
        $helper = Mage::helper('buyerprotect');     
        $productCollection = $helper->getAllTsProducts();
        return $productCollection;
    }
    
    /**
     * Compute tax info.
     *
     * @return string tax info
     */
    public function getTaxInfo()
    {
        $tax = Mage::helper('tax');
        // bundle product type has not tax percent
        if ($tax->displayPriceIncludingTax()) {
            $taxInfo = Mage::helper('buyerprotect')->__('Incl. tax');
        } else {
            $taxInfo = Mage::helper('buyerprotect')->__('Excl. tax');
        }

        return $taxInfo;
    }
    
    /**
     * Get certificate validation form action URL.
     *
     * @return string
     */
    public function getCertificateAction()
    {
        return self::CLASSIC_CERTIFICATE_URL;
    }
    
    /**
     * Get classic buyer protection form action URL.
     *
     * @return string
     */
    public function getClassicFormAction()
    {
        return self::CLASSIC_SUBSCRIBE_URL;
    }
    
    /**
     * Get Trusted Shops ID.
     *
     * @return string
     */
    public function getTsId()
    {
        return Mage::helper('buyerprotect')->getTsUserId();
    }
    
    /**
     * Get current parameter encoding.
     *
     * @return string
     */
    public function getEncoding()
    {
        return self::FORM_ENCODING;
    }
    
    /**
     * Get payment type.
     *
     * @param Mage_Sales_Model_Order $order Order instance.
     *
     * @return string
     */
    public function getPaymentType($order)
    {               
        // phpmd hack.
        unset($order);
        // $payments = Mage::helper('buyerprotect')->getPaymentMapping();
        // $paymentMethod = $order->getPayment()->getMethod();
        // if (is_array($payments)) {
        //     $payments = array_flip($payments);
        //     if (array_key_exists($paymentMethod, $payments)) {
        //         return $payments[$paymentMethod];
        //     }
        // }
        return '';
    }
}
