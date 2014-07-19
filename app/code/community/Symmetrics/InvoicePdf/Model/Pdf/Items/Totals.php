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
 * class to render the total block
 *
 * @category  Symmetrics
 * @package   Symmetrics_InvoicePdf
 * @author    Symmetrics GmbH <info@symmetrics.de>
 * @author    Torsten Walluhn <tw@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_InvoicePdf_Model_Pdf_Items_Totals
    extends Symmetrics_InvoicePdf_Model_Pdf_Items_Abstract
{

    /**
     * default total model
     *
     * @var string
     */
    protected $_defaultTotalModel = 'sales/order_pdf_total_default';

    /**
     * draw the total block
     *
     * @return void
     */
    public function draw()
    {
        $source = $this->getSource();
        $page = $this->getPage();
        $pdf = $this->getPdf();

        $order = $source->getOrder();
        $totals = $this->_getTotalsList();
        //$fullTaxInfo = $order->getFullTaxInfo();
        $font = Mage::helper('invoicepdf')->getFont();

        $index = 1;
        
        foreach ($totals as $total) {
            $total->setOrder($order)
                ->setSource($source);

            if ($total->canDisplay()) {
                foreach ($total->getTotalsForDisplay() as $totalData) {
                    $tableRowItem = Mage::getModel('invoicepdf/pdf_items_item');
                    /* @var $tableRowItem Symmetrics_InvoicePdf_Model_Pdf_Items_Item */
                    
                    // cut last :
                    $totalData['label'] = substr($totalData['label'], 0, -1);
                    $taxTitle = $totalData['label'];
                    
                    $tableRowItem->addColumn(
                        'label_' . $index,
                        $taxTitle,
                        120, 
                        'right',
                        0,
                        $font,
                        $totalData['font_size']
                    );

                    $tableRowItem->addColumn(
                        'amount_' . $index,
                        $totalData['amount'],
                        10,
                        'right',
                        0,
                        $font,
                        $totalData['font_size']
                    );

                    $this->addRow($tableRowItem);
                    
                    $index++;
                }
            }
        }

        $page = $pdf->insertTableRow($page, $this);
        $this->setPage($page);
    }

    /**
     * sort the total list by 'sort_order' key
     *
     * @param array $left  array to sort
     * @param array $right array to sort
     * 
     * @return array
     */
    protected function _sortTotalsList($left, $right)
    {
        if (!isset($left['sort_order']) || !isset($right['sort_order'])) {
            return 0;
        }

        if ($left['sort_order'] == $right['sort_order']) {
            return 0;
        }

        return ($left['sort_order'] > $right['sort_order']) ? 1 : -1;
    }

    /**
     * get the totals list for Source
     * 
     * @return array
     */
    protected function _getTotalsList()
    {
        $totals = Mage::getConfig()->getNode('global/invoicepdf/totals')->asArray();
        usort($totals, array($this, '_sortTotalsList'));
        $totalModels = array();
        foreach ($totals as $totalInfo) {
            if (!empty($totalInfo['model'])) {
                $totalModel = Mage::getModel($totalInfo['model']);
                if ($totalModel instanceof Mage_Sales_Model_Order_Pdf_Total_Default) {
                    $totalInfo['model'] = $totalModel;
                } else {
                    Mage::throwException(
                        Mage::helper('sales')->__(
                            'Pdf total model should extend Mage_Sales_Model_Order_Pdf_Total_Default'
                        )
                    );
                }
            } else {
                $totalModel = Mage::getModel($this->_defaultTotalModel);
            }
            $totalModel->setData($totalInfo);
            $totalModels[] = $totalModel;
        }

        return $totalModels;
    }
}