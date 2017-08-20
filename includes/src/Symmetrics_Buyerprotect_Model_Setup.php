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
 * Setup class for Symmetrics_Buyerprotect
 *
 * @category  Symmetrics
 * @package   Symmetrics_Buyerprotect
 * @author    symmetrics - a CGI Group brand <info@symmetrics.de>
 * @author    Ngoc Anh Doan <nd@symmetrics.de>
 * @author    Benjamin Klein <bk@symmetrics.de>
 * @copyright 2010-2013 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_Buyerprotect_Model_Setup extends Mage_Catalog_Model_Resource_Eav_Mysql4_Setup
{
    /*
     * Some constants for migration skripts.
     */
    const MIGRATION_TEMPLATE_PATH = '/app/locale/de_DE/migrations/';
    const MIGRATION_TEMPLATE_SUFFIX = '.html';
    
    /**
     * Creates a new email template
     *
     * @param string $templateCode code/identifier of template
     * @param array  $content      index of 'template_subject' and 'template_text' are needed
     * @param int    $templateType 1|2 text|html email
     *
     * @return bool
     */
    public function createEmailTemplate($templateCode, array $content, $templateType = 2)
    {
        if (!isset ($content['template_subject']) || !isset ($content['template_text'])) {
            return false;
        }

        /* @var $emailTemplate Mage_Core_Model_Email_Template */
        $emailTemplate = Mage::getModel('core/email_template');

        // Check if template exists
        if ($emailTemplate->loadByCode($templateCode)->hasData()) {
            return false;
        }

        $emailTemplate->setTemplateCode($templateCode)
            ->setTemplateSubject($content['template_subject'])
            ->setTemplateText($content['template_text'])
            ->setTemplateType($templateType)
            ->setAddedAt(new Zend_Db_Expr('Now()'))
            ->setModifiedAt(new Zend_Db_Expr('Now()'))
            ->save();

        return true;
    }

    /**
     * Updates an existing email template and create if neccessary
     *
     * @param string $templateCode code/identifier of template
     * @param array  $content      index of 'template_subject' and 'template_text' are needed
     *
     * @return bool
     */
    public function updateEmailTemplate($templateCode, array $content)
    {
        if (!isset ($content['template_subject']) || !isset ($content['template_text'])) {
            return false;
        }

        /* @var $emailTemplate Mage_Core_Model_Email_Template */
        $emailTemplate = Mage::getModel('core/email_template');

        // Check if template exists
        // if not, create template
        if (!$emailTemplate->loadByCode($templateCode)->hasData()) {
            return $this->createEmailTemplate($templateCode, $content);
        }

        $emailTemplate->setTemplateCode($templateCode)
            ->setTemplateSubject($content['template_subject'])
            ->setTemplateText($content['template_text'])
            ->setModifiedAt(new Zend_Db_Expr('Now()'))
            ->save();

        return true;
    }
}