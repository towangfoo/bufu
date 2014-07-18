<?php
/**
 * Bufu_Core_Model_Email_Template
 */

/**
 * Template model
 *
 * Example:
 *
 * // Loading of template
 * $emailTemplate  = Mage::getModel('core/email_template')
 *    ->load(Mage::getStoreConfig('path_to_email_template_id_config'));
 * $variables = array(
 *    'someObject' => Mage::getSingleton('some_model')
 *    'someString' => 'Some string value'
 * );
 * $emailTemplate->send('some@domain.com', 'Name Of User', $variables);
 *
 * @category   Mage
 * @package    Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Bufu_Core_Model_Email_Template extends Mage_Core_Model_Email_Template
{
    /**
     * Retrieve mail object instance
     *   use SMTP Auth and a real buschfunk.com based mail account for send
     *
     * @return Zend_Mail
     */
    public function getMail()
    {
        if (is_null($this->_mail)) {

           /*Start Hack*/
           // use mail server settings from config for all mail delivery
           $my_smtp_host = Mage::getStoreConfig('system/smtp/host');
           $my_smtp_port = Mage::getStoreConfig('system/smtp/port');
           $my_smtp_auth = strtolower(Mage::getStoreConfig('system/smtp/auth'));
           $my_smtp_username = Mage::getStoreConfig('system/smtp/username');
           $my_smtp_password = Mage::getStoreConfig('system/smtp/password');

           $config = array(
                    'port' => $my_smtp_port,
                    'auth' => $my_smtp_auth,
                    'username' => $my_smtp_username,
                    'password' => $my_smtp_password
                );
            $transport = new Zend_Mail_Transport_Smtp($my_smtp_host, $config);
            Zend_Mail::setDefaultTransport($transport);
            /*End Hack*/

            $this->_mail = new Zend_Mail('utf-8');
        }
        return $this->_mail;
    }
}