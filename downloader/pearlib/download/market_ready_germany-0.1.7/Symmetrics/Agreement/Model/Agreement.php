<?php
/**
 * @category Symmetrics
 * @package Symmetrics_Agreement
 * @author symmetrics gmbh <info@symmetrics.de>, Eugen Gitin <eg@symmetrics.de>
 * @copyright symmetrics gmbh
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Symmetrics_Agreement_Model_Agreement extends Mage_Checkout_Model_Agreement 
{
    /**
     * Adding filter to the normal agreement window content
     *
     * @return string
     */
    public function getContent()
    {
        $content = $this->getData('content');
        $processor = Mage::getModel('core/email_template_filter');
        $html = $processor->filter($content);
        return $html;
    }
}