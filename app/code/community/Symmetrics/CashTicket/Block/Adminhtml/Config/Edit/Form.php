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
 * Symmetrics_CashTicket_Block_Adminhtml_Config_Edit_Form
 *
 * @category  Symmetrics
 * @package   Symmetrics_CashTicket
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Eugen Gitin <eg@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
*/
class Symmetrics_CashTicket_Block_Adminhtml_Config_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * set editForm id
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('editForm');
    }

    /**
     * Preparing the form and adding 
     * fields to it
     *
     * @return object
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));

        $fieldset = $form->addFieldset(
            'cashticket_form',
            array(
                'legend' => Mage::helper('cashticket')->__('Cash-Ticket Configuration')
            )
        );
        
        if ($this->getRequest()->getParam('id')) {
            $fieldset->addField(
                'item_id',
                'hidden',
                array(
                    'name' => 'item_id',
                )
            );
        }
        
        //add fields to fieldset
        foreach ($this->_getFormFields() as $field) {
            $params = array(
                'label' => $field['label'],
                'class' => $field['class'],
                'required' => $field['required'],
                'name' => $field['name'],
            );
            
            if ($field['options'] !== false) {
                $params['options'] = $field['options'];
            }
            
            $fieldset->addField(
                $field['code'],
                $field['type'],
                $params
            );
        }
        
        // get form values from the session
        if (Mage::getSingleton('adminhtml/session')->getCashticketData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getCashticketData());
            Mage::getSingleton('adminhtml/session')->setCashticketData(null);
        } elseif (Mage::registry('cashticket_data')) {
            $form->setValues(Mage::registry('cashticket_data')->getData());
        }
        
        $form->setAction($this->getUrl('*/*/save'));
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Get form field elements
     *
     * @return array
     */
    private function _getFormFields()
    {
        $testLabel = 'Path to PEM Certificate (Test)';
        $liveLabel = 'Path to PEM Certificate (Live)';
        return array(
            $this->_createFieldElement('enable', 'select', 'Enable', 'required-entry', true, true),
            $this->_createFieldElement('currency_code', 'select', 'Currency', 'required-entry', true, true),
            $this->_createFieldElement('merchant_id', 'text', 'Merchant ID', 'required-entry', true),
            $this->_createFieldElement('business_type', 'select', 'Business Type', 'required-entry', true, true),
            $this->_createFieldElement('reporting_criteria', 'text', 'Reporting Criteria', '', false),
            $this->_createFieldElement('locale', 'select', 'Language', 'required-entry', true, 'locale', true),
            $this->_createFieldElement('path_pem_test', 'text', $testLabel, 'required-entry', true),
            $this->_createFieldElement('path_pem_live', 'text', $liveLabel, 'required-entry', true),
            $this->_createFieldElement('path_cert', 'text', 'Path to the Server Certificate', 'required-entry', true),
            $this->_createFieldElement('sslcert_pass', 'text', 'Keyring Password', 'required-entry', true),
            $this->_createFieldElement('sandbox', 'select', 'Sandbox', 'required-entry', true, true)
        );
    }
    
    /**
     * Create field element
     *
     * @param string  $code       Code
     * @param string  $type       Ínput type 
     * @param string  $label      Label
     * @param string  $class      Class
     * @param boolean $required   Is required flag
     * @param boolean $hasOptions Has options flag
     *
     * @return array
     */
    private function _createFieldElement($code, $type, $label, $class, $required, $hasOptions = false)
    {
        $options = false;
        
        if ($hasOptions) {
            $options = $this->_getOptionsByCode($code);
        }
        
        return array(
            'code' => $code,
            'type' => $type,
            'label' => $label,
            'class' => $class,
            'required' => $required,
            'name' => $code,
            'options' => $options
        );
    }
    
    /**
     * Return option arrays by code
     *
     * @param string $code Code
     *
     * @return array | boolean
     */
    private function _getOptionsByCode($code) 
    {
        $options = array(
            'currency_code' => Mage::getModel('cashticket/source_currency')->getOptionArray(),
            'business_type' => Mage::getModel('cashticket/source_businesstype')->getOptionArray(),
            'locale' => Mage::getModel('cashticket/source_locale')->getOptionArray(),
            'enable' => array(
                  0 => Mage::helper('cashticket')->__('No'),
                  1 => Mage::helper('cashticket')->__('Yes'),
            ),
            'sandbox' => array(
                0 => Mage::helper('cashticket')->__('No'),
                1 => Mage::helper('cashticket')->__('Yes')
            )
        );
        
        if (isset($options[$code])) {
            return $options[$code];
        }
        
        return false;
    }
}
