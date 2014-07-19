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
 * @package   Symmetrics_InvoicePdf
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Torsten Walluhn <tw@symmetrics.de>
 * @copyright 2010 Symmetrics Gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * Abstract Class to render Items such als Products, Totals and others
 *
 * @category  Symmetrics
 * @package   Symmetrics_InvoicePdf
 * @author    Symmetrics GmbH <info@symmetrics.de>
 * @author    Torsten Walluhn <tw@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
abstract class Symmetrics_InvoicePdf_Model_Pdf_Items_Abstract extends Mage_Core_Model_Abstract
{
    /**
     * Order model
     *
     * @var Mage_Sales_Model_Order
     */
    protected $_order;

    /**
     * Source model (invoice, shipment, creditmemo)
     *
     * @var Mage_Core_Model_Abstract
     */
    protected $_source;

    /**
     * Item object
     *
     * @var Varien_Object
     */
    protected $_item;

    /**
     * container of given items
     *
     * @var array
     */
    protected $_items = array();
    
    /**
     * Pdf object
     *
     * @var Mage_Sales_Model_Order_Pdf_Abstract
     */
    protected $_pdf;

    /**
     * Pdf current page
     *
     * @var Zend_Pdf_Page
     */
    protected $_pdfPage;

    /**
     * Set order model
     *
     * @param Mage_Sales_Model_Order $order order to set
     * 
     * @return Mage_Sales_Model_Order_Pdf_Items_Abstract
     */
    public function setOrder(Mage_Sales_Model_Order $order)
    {
        $this->_order = $order;
        return $this;
    }

    /**
     * Set Source model
     *
     * @param Mage_Core_Model_Abstract $source source to set
     * 
     * @return Symmetrics_InvoicePdf_Model_Pdf_Items_Abstract
     */
    public function setSource(Mage_Core_Model_Abstract $source)
    {
        $this->_source = $source;
        return $this;
    }

    /**
     * Set item object
     *
     * @param Varien_Object $item item to set
     *
     * @return Symmetrics_InvoicePdf_Model_Pdf_Items_Abstract
     */
    public function setItem(Varien_Object $item)
    {
        $this->_item = $item;
        return $this;
    }

    /**
     * Set Pdf model
     *
     * @param Symmetrics_InvoicePdf_Model_Pdf_Abstract $pdf model to set
     *
     * @return Symmetrics_InvoicePdf_Model_Pdf_Items_Abstract
     */
    public function setPdf(Symmetrics_InvoicePdf_Model_Pdf_Abstract $pdf)
    {
        $this->_pdf = $pdf;
        return $this;
    }

    /**
     * Set current page
     *
     * @param Zend_Pdf_Page $page Pdf page to set
     *
     * @return Symmetrics_InvoicePdf_Model_Pdf_Items_Abstract
     */
    public function setPage(Zend_Pdf_Page $page)
    {
        $this->_pdfPage = $page;
        return $this;
    }

    /**
     * Retrieve order object
     *
     * @throws Mage_Core_Exception
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (is_null($this->_order)) {
            Mage::throwException(Mage::helper('sales')->__('Order object is not specified.'));
        }
        return $this->_order;
    }

    /**
     * Retrieve source object
     *
     * @throws Mage_Core_Exception
     * @return Mage_Core_Model_Abstract
     */
    public function getSource()
    {
        if (is_null($this->_source)) {
            Mage::throwException(Mage::helper('sales')->__('Source object is not specified.'));
        }
        return $this->_source;
    }

    /**
     * Retrieve item object
     *
     * @throws Mage_Core_Exception
     * @return Varien_Object
     */
    public function getItem()
    {
        if (is_null($this->_item)) {
            Mage::throwException(Mage::helper('sales')->__('Item object is not specified.'));
        }
        return $this->_item;
    }

    /**
     * Retrieve Pdf model
     *
     * @throws Mage_Core_Exception
     *
     * @return Symmetrics_InvoicePdf_Model_Pdf_Abstract
     */
    public function getPdf()
    {
        if (is_null($this->_pdf)) {
            Mage::throwException(Mage::helper('sales')->__('PDF object is not specified.'));
        }
        return $this->_pdf;
    }

    /**
     * Retrieve Pdf page object
     *
     * @throws Mage_Core_Exception
     * @return Zend_Pdf_Page
     */
    public function getPage()
    {
        if (is_null($this->_pdfPage)) {
            Mage::throwException(Mage::helper('sales')->__('PDF page object is not specified.'));
        }
        return $this->_pdfPage;
    }

    /**
     * add a Row to items
     *
     * @param Symmetrics_InvoicePdf_Model_Pdf_Items_Item $item item to add
     *
     * @return void
     */
    public function addRow(Symmetrics_InvoicePdf_Model_Pdf_Items_Item $item)
    {
        $this->_items[] = $item;
    }

    /**
     * get row of given line number
     *
     * @param int $lineNumber line number
     * 
     * @return mixed
     */
    public function getRow($lineNumber = 0)
    {
        return $this->_items[$lineNumber];
    }

    /**
     * get all rows
     *
     * @return array
     */
    public function getAllRows()
    {
        return $this->_items;
    }

    /**
     * get count of all rows
     *
     * @return int
     */
    public function getRowCount()
    {
        return count($this->_items);
    }

    /**
     * clear all rows
     *
     * @return void
     */
    public function clearRows()
    {
        $this->_items = array();
    }

    /**
     * Draw item line
     *
     * @return void
     */
    abstract public function draw();

    /**
     * calculate height off all current items
     *
     * @return float
     */
    public function calculateHeight()
    {
        $maxHeight = 0;

        foreach ($this->_items as $item) {
            $maxHeight += $item->calculateHeight();
        }

        return $maxHeight;
    }

    /**
     * get Options of current product
     *
     * @return array
     */
    public function getItemOptions()
    {
        $result = array();
        if ($options = $this->getItem()->getOrderItem()->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }
        return $result;
    }

    /**
     * get Sku for given prodcut
     *
     * @param Mage_Core_Model_Abstract $item item to get sky
     * 
     * @return string
     */
    public function getSku($item)
    {
        if ($item->getOrderItem()->getProductOptionByCode('simple_sku')) {
            return $item->getOrderItem()->getProductOptionByCode('simple_sku');
        } else {
            return $item->getSku();
        }
    }
}