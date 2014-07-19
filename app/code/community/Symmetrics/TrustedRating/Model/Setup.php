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
 * @package   Symmetrics_TrustedRating
 * @author    symmetrics - a CGI Group brand <info@symmetrics.de>
 * @author    Siegfried Schmitz <ss@symmetrics.de>
 * @author    Andreas Timm <at@symmetrics.de>
 * @author    Toni Stache <ts@symmetrics.de>
 * @copyright 2009-2014 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://github.com/symmetrics/trustedshops_trustedrating/
 * @link      http://www.symmetrics.de/
 * @link      http://www.de.cgi.com/
 */

/**
 * Setup model
 *
 * @category  Symmetrics
 * @package   Symmetrics_TrustedRating
 * @author    symmetrics - a CGI Group brand <info@symmetrics.de>
 * @author    Siegfried Schmitz <ss@symmetrics.de>
 * @author    Andreas Timm <at@symmetrics.de>
 * @author    Toni Stache <ts@symmetrics.de>
 * @author    Ngoc Anh Doan <ngoc-anh.doan@cgi.com>
 * @copyright 2009-2014 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://github.com/symmetrics/trustedshops_trustedrating/
 * @link      http://www.symmetrics.de/
 * @link      http://www.de.cgi.com/
 */
class Symmetrics_TrustedRating_Model_Setup extends Mage_Eav_Model_Entity_Setup
{
    /**
     * Config paths for trustedratingmail
     * @deprecated since v0.2.4
     */
    const XML_PATH_TRUSTEDRATINGMAIL = 'default/trustedratingmail/emails/default';

    /**
     * Get config data
     *
     * @return array
     * @deprecated since v0.2.4
     */
    public function getConfigData()
    {
        return Mage::getConfig()->getNode('default/trustedratingmail')->asArray();
    }

    /**
     * Get config node
     *
     * @param string $node      main node
     * @param string $childNode child Node
     *
     * @return string
     * @deprecated since v0.2.4
     */
    private function getConfigNode($node, $childNode = null)
    {
        $configData = $this->getConfigData();
        if ($childNode) {
            return $configData[$node][$childNode];
        } else {
            return $configData[$node];
        }
    }

    /**
     * Get email from config
     *
     * @return string
     * @deprecated since v0.2.4
     */
    public function getConfigEmails()
    {
        return $this->getConfigNode('emails', 'default');
    }

    /**
     * Get email from config
     *
     * @param string $key Config path last key.
     *
     * @return string
     * @deprecated since v0.2.4
     */
    public function getTrustedratingEmails($key = null)
    {
        return Mage::getConfig()
            ->getNode(self::XML_PATH_TRUSTEDRATINGMAIL . ($key ? '/' . $key : ''))
            ->asArray();
    }

    /**
     * Get content of template file
     *
     * @param string $filename Name of File
     *
     * @return file
     */
    public function getTemplateContent($filename)
    {
        return file_get_contents(Mage::getBaseDir() . '/' . $filename);
    }

    /**
     * Creates or updates transaction email template.
     *
     * @param array $emailData Collected data for email template.
     *
     * @return int
     */
    public function createEmail($emailData)
    {
        $emailTemplate = Mage::getModel('core/email_template')->loadByCode($emailData['template_code']);

        if (!$emailTemplate->getId()) {
            // Set additional data for new template.
            $emailTemplate->setTemplateCode($emailData['template_code'])
                ->setTemplateType($emailData['template_type']);
        }

        $emailTemplate
            ->setTemplateSubject($emailData['template_subject'])
            ->setTemplateText($this->getTemplateContent($emailData['text']))
            ->setModifiedAt(Mage::getSingleton('core/date')->gmtDate())
            ->save();

        return $emailTemplate->getId();
    }
}
