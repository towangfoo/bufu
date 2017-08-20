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
 * @package   Symmetrics_Agreement
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Eric Reiche <er@symmetrics.de>
 * @author    Eugen Gitin <eg@symmetrics.de>
 * @copyright 2009-2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * Setup model for creating agreements + cms blocks and pages
 *
 * @category  Symmetrics
 * @package   Symmetrics_Agreement
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Eric Reiche <er@symmetrics.de>
 * @author    Eugen Gitin <eg@symmetrics.de>
 * @copyright 2009-2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_Agreement_Model_Setup extends Mage_Eav_Model_Entity_Setup
{
    /**
     * Collect data and create agreement
     *
     * @param array $agreementData agreement data
     *
     * @return void
     */
    public function createAgreement($agreementData)
    {
        $agreementData['is_active'] = '1';
        $agreementData['is_html'] = '1';
        $agreementData['stores'] = array('0');
        
        $agreement = Mage::getModel('checkout/agreement');
        $agreement->setData($agreementData)
            ->save();
    }
    
    /**
     * Collect data and create CMS page
     *
     * @param array $pageData cms page data
     *
     * @return void
     */
    public function createCmsPage($pageData)
    {
        if (!is_array($pageData)) {
            return null;
        }
        $pageData['stores'] = array('0');
        $pageData['is_active'] = '1';

        $model = Mage::getModel('cms/page');
        $page = $model->load($pageData['identifier']);

        if (!$page->getId()) {
            $model->setData($pageData)->save();
        } else {
            $pageData['page_id'] = $page->getId();
            $model->setData($pageData)->setId($pageData['page_id'])->save();
        }
    }
    
    /**
     * Collect data and create CMS block
     *
     * @param array $blockData cms block data
     *
     * @return void
     */
    public function createCmsBlock($blockData)
    {
        $model = Mage::getModel('cms/block');
        $block = $model->load($blockData['identifier']);
        // do not overwrite existing blocks
        if (!$block->getId()) {
            $blockData['stores'] = array('0');
            $blockData['is_active'] = '1';

            $model->setData($blockData)->save();
        }
    }
}
