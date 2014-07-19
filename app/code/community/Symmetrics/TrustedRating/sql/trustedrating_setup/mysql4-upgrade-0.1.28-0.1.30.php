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
 * Set the "include shippings after" date
 *
 * @category  Symmetrics
 * @package   Symmetrics_TrustedRating
 * @author    symmetrics - a CGI Group brand <info@symmetrics.de>
 * @author    Eric Reiche <er@symmetrics.de>
 * @copyright 2009-2014 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://github.com/symmetrics/trustedshops_trustedrating/
 * @link      http://www.symmetrics.de/
 * @link      http://www.de.cgi.com/
 */

$installer = $this;
$installer->startSetup();

$todayDate = Mage::app()->getLocale()->date();

$oldDateFormats = array(
    'datelimit_y',
    'datelimit_m',
    'datelimit_d',
    'datelimit_h',
    'datelimit_i'
);

$prefixOld = 'trustedrating/status/';

$oldDate = array();

$configCollection = Mage::getModel('core/config_data')->getCollection();
$configCollection->addFieldToFilter('path', array('like' => $prefixOld . '%'))
    ->load();

foreach ($configCollection as $configValue) {
    $oldPath = str_replace($prefixOld, '', $configValue->getPath());
    $value = $configValue->getValue();
    $oldDate[$oldPath] = $value;
    unset($oldPath, $value);
}
$incomplete = true;
if (empty($oldDate)) {
    $incomplete = false;
    foreach ($oldDateFormats as $format) {
        if ($incomplete) {
            break;
        }
        if (!array_key_exists($format, $oldDate)) {
            $incomplete = true;
        }
    }
}
if ($incomplete) {
    $newDate = new Zend_Date();
} else {
    $datearray = array(
        'year' => $oldDate['datelimit_y'],
        'month' => $oldDate['datelimit_m'],
        'day' => $oldDate['datelimit_d'],
        'hour' => $oldDate['datelimit_h'],
        'minute' => $oldDate['datelimit_i'],
        'second' => 0);
    $newDate = new Zend_Date($datearray);
}
$newDate = $newDate->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);;

$installer->setConfigData('trustedrating/data/active_since', $newDate);

$installer->endSetup();
