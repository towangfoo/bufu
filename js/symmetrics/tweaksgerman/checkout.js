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
 * @package   Symmetrics_TweaksGerman
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Siegfried Schmitz <ss@symmetrics.de>
 * @copyright 2009-2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

document.observe('dom:loaded', function() {
    /* Check the radiobutton "shipping to this adress" */
    $('billing:use_for_shipping_yes').writeAttribute('checked', true);
    
    /* Change the position from zip and city - fields */    
    changeElements($('billing:postcode'), $('billing:city'));
    changeElements($('shipping:postcode'), $('shipping:city'));
});

function changeElements(zipElement, cityElement) {
    if (zipElement && cityElement) {
        var cityField = cityElement.up().up();
        var zipField = zipElement.up().up().innerHTML;

        zipElement.up().up().update(cityField.innerHTML);
        cityElement.up().up().update(zipField);
    }
}
