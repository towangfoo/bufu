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
 * Symmetrics_CashTicket_Block_Adminhtml_Config_Grid
 *
 * @category  Symmetrics
 * @package   Symmetrics_CashTicket
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Eugen Gitin <eg@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
*/
class Symmetrics_CashTicket_Block_Adminhtml_Config_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Prepare the grid
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('cashticketGrid');
        $this->setDefaultSort('currency_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Get collection of config items
     * and set it into the grid object
     *
     * @return string
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('cashticket/item')->getCollection();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Set grid columns
     *
     * @return object
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'enable', 
            array(
                'header'    => Mage::helper('cashticket')->__('Enabled'),
                'type'      => 'options',
                'index'     => 'enable',
                'sortable'  => true,
                'options'   => array(
                    '1' => Mage::helper('cashticket')->__('Yes'),
                    '0' => Mage::helper('cashticket')->__('No')
                ),
                'width'     => '100px'
            )
        );
        
        $this->addColumn(
            'currency_code', 
            array(
                'header'    => Mage::helper('cashticket')->__('Currency'),
                'type'      => 'text',
                'index'     => 'currency_code',
                'sortable'  => true
            )
        );

        $this->addColumn(
            'merchant_id', 
            array(
                'header'    => Mage::helper('cashticket')->__('Merchant ID'),
                'type'      => 'text',
                'index'     => 'merchant_id',
                'sortable'  => true
            )
        );

        $this->addColumn(
            'sandbox', 
            array(
                'header'    => Mage::helper('cashticket')->__('Sanbox'),
                'type'      => 'options',
                'index'     => 'sandbox',
                'sortable'  => true,
                'options'   => array(
                    '1' => Mage::helper('cashticket')->__('Yes'),
                    '0' => Mage::helper('cashticket')->__('No')
                ),
                'width'     => '100px'
            )
        );
        
        // add "edit" link into the action column
        $this->addColumn(
            'action',
            array(
                'header'    => Mage::helper('cashticket')->__('Action'),
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(array(
                    'caption'   => Mage::helper('catalog')->__('Edit'),
                    'url'       => array(
                        'base' => '*/*/edit'
                    ),
                    'field'   => 'id'
                )),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'config',
                'width'     => '100px'
                )
        );

        return parent::_prepareColumns();
    }

    /**
     * Set the edit url
     *
     * @param object $row row
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}