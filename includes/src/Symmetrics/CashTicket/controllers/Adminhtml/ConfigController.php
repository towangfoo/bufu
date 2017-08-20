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
 * Symmetrics_CashTicket_Adminhtml_ConfigController
 *
 * @category  Symmetrics
 * @package   Symmetrics_CashTicket
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Eugen Gitin <eg@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
*/
class Symmetrics_CashTicket_Adminhtml_ConfigController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Initialize the controller
     *
     * @return object
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('cashticket/items')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Items Manager'), 
                Mage::helper('adminhtml')->__('Item Manager')
            );

        return $this;
    }

    /**
     * Default action
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_initAction()->renderLayout();
    }

    /**
     * Redirect to edit action
     *
     * @return void
     */
    public function newAction()
    {
        $this->_forward('edit');
    }
    
    /**
     * Edit form action
     *
     * @return string
     */
    public function editAction()
    {
        $itemId = $this->getRequest()->getParam('id');
        // load the item model by id
        $model = Mage::getModel('cashticket/item')->load($itemId);
        // if id was found
        if ($model->getId() || $itemId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);

            if (!empty($data)) {
                $model->setData($data);
            }

            // set the model to register for later use
            Mage::register('cashticket_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('cashticket/items');

            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Item Manager'), 
                Mage::helper('adminhtml')->__('Item Manager')
            );
            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Item News'), 
                Mage::helper('adminhtml')->__('Item News')
            );

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('cashticket/adminhtml_config_edit'));

            $this->renderLayout();
        } else {
            // otherwise show error
            $error = Mage::helper('cashticket')->__('Item does not exist');
            Mage::getSingleton('adminhtml/session')->addError($error);
            $this->_redirect('*/*/');   
        }
    }

    /**
     * Save form action
     *
     * @return void
     */
    public function saveAction()
    {
        // if have data in post
        if ($data = $this->getRequest()->getPost()) {
            // load the config item model and set the id
            $model = Mage::getModel('cashticket/item');
            $model->setData($data)->setId($this->getRequest()->getParam('item_id'));
            
            try {
                // get all enabled configs for the currency
                $collection = Mage::getModel('cashticket/item')->getCollection()
                    ->addFilter('currency_code', $model->getCurrencyCode())
                    ->addFilter('enable', 1);

                $size = $collection->getSize();
                $ids = $collection->getAllIds();

                // if there are already saved active config items
                if ($size > 0 && $ids[0] != $model->getItemId()) {
                    // throw error
                    $errorMessage = 'You have already an active configuration for currency %s.';
                    $error = Mage::helper('cashticket')->__($errorMessage, $model->getCurrencyCode());
                    Mage::throwException($error);                
                }
                
                // set update and creation time to now if saving the new item
                if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }

                $model->save();

                // add success message
                $successMessage = 'Configuration for %s was successfully saved';
                $success = Mage::helper('cashticket')->__($successMessage, $model->getData('currency_code'));
                Mage::getSingleton('adminhtml/session')->addSuccess($success);
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                // if save and continue
                if ($this->getRequest()->getParam('back')) {
                    // redirect to the edit form with the same id
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return true;
                }

                $this->_redirect('*/*/');
                return true;
                
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('item_id')));
                return false;
            }
        }
        $errorMessage = Mage::helper('cashticket')->__('Unable to find item to save');
        Mage::getSingleton('adminhtml/session')->addError($errorMessage);
        $this->_redirect('*/*/');
    }

    /**
     * Delete action
     *
     * @return void
     */
    public function deleteAction()
    {
        // if id is set
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                // delete config item
                $model = Mage::getModel('cashticket/item');
                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();
                $success = Mage::helper('adminhtml')->__('Item was successfully deleted');
                Mage::getSingleton('adminhtml/session')->addSuccess($success);
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        // redirect to back to grid
        $this->_redirect('*/*/');
    }
}