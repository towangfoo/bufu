<?php
/**
 * @category Symmetrics
 * @package Symmetrics_Agreement
 * @author symmetrics gmbh <info@symmetrics.de>, Eugen Gitin <eg@symmetrics.de>
 * @copyright symmetrics gmbh
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Symmetrics_Agreement_Model_Setup extends Mage_Eav_Model_Entity_Setup
{
    public function createCmsPage($pageData)
    {
        if (is_array($pageData)) {
            foreach($pageData as $key => $value) {
                $data[$key] = $value;
            }
            $data['stores'] = array('0');
            $data['is_active'] = '1';
        }
        else {
            return;
        }

        $model = Mage::getModel('cms/page');
        $page = $model->load($pageData['identifier']);

        if (! $page->getId()) {
            $model->setData($data)->save();
        }
        else {
            $data['page_id'] = $page->getId();
            $model->setData($data)->save();
        }
    }

    public function createCmsBlock($blockData)
    {
        $blockData['stores'] = array('0');
        $blockData['is_active'] = '1';

        $model = Mage::getModel('cms/block');
        $block = $model->load($blockData['identifier']);

        if (! $block->getId()) {
            $model->setData($blockData)->save();
        }
        else {
            $data['block_id'] = $block->getId();
            $model->setData($blockData)->save();
        }
    }
    
    public function createAgreement($agreementData)
    {
        $agreementData['is_active'] = '1';
        $agreementData['is_html'] = '1';
        $agreementData['stores'] = array('0');
        
        $model = Mage::getSingleton('checkout/agreement');
        $model->setData($agreementData);
        
        $model->save();
    }
}