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
 * @author    Siegfried Schmitz <ss@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * System config ttf - file upload field backend model.
 *
 * @category  Symmetrics
 * @package   Symmetrics_InvoicePdf
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Siegfried Schmitz <ss@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_InvoicePdf_Model_System_Config_Backend_Font extends Mage_Core_Model_Config_Data
{

    /**
     * Save uploaded file before saving config value
     *
     * @return Mage_Adminhtml_Model_System_Config_Backend_Image
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (is_array($value) && !empty($value['delete'])) {
            $this->setValue('');
        }

        if ($_FILES['groups']['tmp_name'][$this->getGroupId()]['fields'][$this->getField()]['value']) {

            $fieldConfig = $this->getFieldConfig();
            /* @var $fieldConfig Varien_Simplexml_Element */
            
            // @codingStandardsIgnoreStart (cause of the not valid camel caps format from upload_dir)
            $uploadDir = $this->_checkUploadDir($fieldConfig->upload_dir);
            // @codingStandardsIgnoreEnd
            
            $dir = $fieldConfig->descend('upload_dir');

            /**
             * Add scope info.
             */
            if (!empty($dir['scope_info'])) {
                $uploadDir = $this->_appendScopeInfo($uploadDir);
            }

            /**
             * Take root from config.
             */
            if (!empty($dir['config'])) {
                $uploadRoot = (string)Mage::getConfig()->getNode(
                    (string)$dir['config'], $this->getScope(), $this->getScopeId()
                );
                $uploadRoot = Mage::getConfig()->substDistroServerVars($uploadRoot);
                $uploadDir = $uploadRoot . '/' . $uploadDir;
            }

            try {
                $file = array();
                $groups = $_FILES['groups']; 
                $file['tmp_name'] = $groups['tmp_name'][$this->getGroupId()]['fields'][$this->getField()]['value'];
                $file['name'] = $groups['name'][$this->getGroupId()]['fields'][$this->getField()]['value'];
                $uploader = new Varien_File_Uploader($file);
                $uploader->setAllowedExtensions($this->_getAllowedExtensions());
                $uploader->setAllowRenameFiles(true);
                $uploader->save($uploadDir);
            } catch (Exception $e) {
                Mage::throwException($e->getMessage());
                return $this;
            }

            if ($filename = $uploader->getUploadedFileName()) {
                /**
                 * Add scope info.
                 */
                if (!empty($dir['scope_info'])) {
                    $filename = $this->_prependScopeInfo($filename);
                }

                $this->setValue($filename);
            }
        }

        return $this;
    }
    
    /**
     * Check upload directory from config.
     * 
     * @param string $uploadDir Upload directory.
     *
     * @return string
     */
    protected function _checkUploadDir($uploadDir) 
    {
        if (empty($uploadDir)) {
            Mage::throwException(
                Mage::helper('catalog')->__('The base directory to upload font file is not specified.')
            );
        }
        return (string)$uploadDir;
    }

    /**
     * Prepend path with scope info.
     * E.g. 'stores/2/path' , 'websites/3/path', 'default/path'.
     *
     * @param string $path Path.
     *
     * @return string
     */
    protected function _prependScopeInfo($path)
    {
        $scopeInfo = $this->getScope();
        if ('default' != $this->getScope()) {
            $scopeInfo .= '/' . $this->getScopeId();
        }
        return $scopeInfo . '/' . $path;
    }

    /**
     * Add scope info to path.
     * E.g. 'path/stores/2' , 'path/websites/3', 'path/default'.
     *
     * @param string $path Path.
     *
     * @return string
     */
    protected function _appendScopeInfo($path)
    {
        $path .= '/' . $this->getScope();
        if ('default' != $this->getScope()) {
            $path .= '/' . $this->getScopeId();
        }
        
        return $path;
    }

    /**
     * Get allowed extensions.
     *
     * @return array
     */
    protected function _getAllowedExtensions()
    {
        return array('ttf');
    }
}
