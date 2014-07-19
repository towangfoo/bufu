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
 * @author    Benjamin Klein <bk@symmetrics.de>
 * @author    Siegfried Schmitz <ss@symmetrics.de>
 * @copyright 2009-2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

if (!window.Symmetrics) {
    window.Symmetrics = {};
}

/**
 * Symmetrics.Province
 *
 * @category  Symmetrics
 * @package   Symmetrics_TweaksGerman
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Torsten Walluhn <tw@symmetrics.de>
 * @copyright 2011 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
Symmetrics.Province = Class.create();
Object.extend(Object.extend(Symmetrics.Province.prototype, Abstract.prototype),
{
    /**
     * Constructor initialize element names.
     */
    initialize: function(countryList)
    {
        var currentUrl = window.location.href;

        if (currentUrl.include('checkout')) {
            this.isCheckout = true;
            this.countryName = 'billing:country_id';
            this.regionName = 'billing:region';
            this.regionIdName = 'billing:region_id';
        } else {
            this.isCheckout = false;
            this.countryName = 'country';
            this.regionName = 'region';
            this.regionIdName = 'region_id';
        }

        this.countries = countryList;
        this.startObserver();
    },

    /**
     * Initialize observer for dom loaded.
     */
    startObserver: function()
    {
      if (this.isCheckout) {
          this.createObserverDomLoaded();
      } else {
          document.observe('dom:loaded', (function(){
              this.createObserverDomLoaded();
          }).bind(this));
      }
    },

    /**
     * createObserverDomLoaded:waiting for observer dom loaded and create observers and start the change first time.
     *
     * @return void
     */
    createObserverDomLoaded: function()
    {
        var country = $(this.countryName);
        this.startObserveBillingRegion();

        if (this.isCheckout == true) {
            this.startObserveShippingTab();
            this.startObserveBillingButtons();
            this.startObserveShippingRegion();
        }

        if (!country) {
            this.createObserverProvinceAddress();
            this.startProvinceAdressChanging();
        } else {
            this.createObserverProvinceBilling();
            if (this.isCheckout == true) {
              this.createObserverProvinceShipping();
              this.startProvinceShippingChanging();
            }
            this.startProvinceBillingChanging();
        }
    },

    /**
     * createObserverProvinceBilling: create observer for billing process.
     *
     * @return void
     */
    createObserverProvinceBilling: function()
    {
        Event.observe($(this.countryName),'change', (function(){
            this.startProvinceBillingChanging();
        }).bind(this));
    },

    startObserveBillingRegion: function()
    {
        Event.observe($(this.regionIdName),'change', (function(){
            var selectedValue = ($(this.regionIdName).options[$(this.regionIdName).selectedIndex].value);
            if (selectedValue) {
                $(this.regionIdName + '-tmp').value = selectedValue;
            }
        }).bind(this));
    },

    startObserveShippingRegion: function()
    {
        Event.observe($('shipping:region_id'),'change', (function(){
            var selectedValue = ($('shipping:region_id').options[$('shipping:region_id').selectedIndex].value);
            if (selectedValue) {
                $('shipping[region_id]-tmp').value = selectedValue;
            }
        }).bind(this));
    },

    /**
     * startProvinceBillingChanging: start changing region_id in billing process.
     *
     * @return void
     */
    startProvinceBillingChanging: function()
    {
        if (this.isCheckout) {
            var regionName = 'billing[region_id]';
        } else {
            var regionName = this.regionIdName;
        }
        this.setRegionId(this.countryName, regionName, this.regionIdName);
    },

    /**
     * createObserverProvinceShipping: create observer for shipping process.
     *
     * @return void
     */
    createObserverProvinceShipping: function ()
    {
        Event.observe($('shipping:country_id'), 'change', (function(){
            this.startProvinceShippingChanging();
        }).bind(this));
    },

    /**
     * startProvinceShippingChanging: start changing region_id in shipping process.
     *
     * @return void
     */
    startProvinceShippingChanging: function()
    {
        this.setRegionId('shipping:country_id', 'shipping[region]', 'shipping:region_id', 'shipping:region');
    },

    /**
     * createObserverProvinceAddress: create observer for address editing process.
     *
     * @return void
     */
    createObserverProvinceAddress: function()
    {
        Event.observe($(this.countryName), 'change', (function(){
            this.startProvinceAdressChanging();
        }).bind(this));
    },

    /**
     * startProvinceAdressChanging: start changing region_id in address editing process.
     *
     * @return void
     */
    startProvinceAdressChanging: function()
    {
        this.setRegionId(this.countryName, this.regionIdName, this.regionIdName);
    },

    /**
     * startObserveShippingTab: start observing manually click on the shipping tab
     *
     * @return void
     */
    startObserveShippingTab: function()
    {
        Event.observe($$('li#opc-shipping div.step-title').first(), 'click', (function() {
            this.setShippingRegionId();
        }).bind(this));
    },

    /**
     * startObserveBillingButtons: start observing the shipping tab.
     *
     * @return void
     */
    startObserveBillingButtons: function()
    {
        Event.observe($$('div#billing-buttons-container button.button').first(), 'click', (function() {
            this.setShippingRegionId();
        }).bind(this));
    },

    /**
     * setShippingRegionId: Set region id in shipping tab.
     *
     * @return void
     */
    setShippingRegionId: function()
    {
        var selectedValue = ($('shipping:country_id').options[$('shipping:country_id').selectedIndex].value);
        if (selectedValue == 'DE') {
            this.updateRegionIdField('shipping:region_id', 'shipping[region_id]', this.getLastRegionId('shipping:region_id'));
        } else {
            $$('label[for="shipping:region"]').first().show();
            $$('label[for="shipping:region"]').first().next().show();
            $$('label[for="shipping:region"]').first().next().down().show();
        }
        $('shipping:region_id').style.display = 'none';
    },

    /**
     * setRegionId: check if country is german, hide "state/province" label and update region_id.
     * If country is not german show "state/province" label.
     *
     * @param String countryFieldName     Name of Country field.
     * @param String regionInputFieldName Name of region input field.
     * @param String regionFieldName      Name of region field.
     * @param String regionFieldNameLabel Name of region label field optional couse only one observer needs this.
     *
     * @return void
     */
    setRegionId: function(countryFieldName, regionInputFieldName, regionFieldName, regionFieldNameLabel)
    {
        try {
            if (this.checkCountryCode(countryFieldName)) {
                if (regionFieldNameLabel === undefined) {
                    this.hideTextElement(regionFieldName);
                } else {
                    this.hideTextElement(regionFieldNameLabel);
                }
                this.updateRegionIdField(regionFieldName, regionInputFieldName, this.getLastRegionId(regionFieldName));
            } else {
                this.showTextElement(regionFieldName);
            }
        } catch (exception) {}
    },

    /**
     * hideTextElement: hide the first label by name filter.
     *
     * @param String textFieldName The name of the "State/Province" field name.
     *
     * @return void
     */
    hideTextElement: function(textFieldName)
    {
        // IE - bugfix, have to hide the parent element from dropdown
        document.getElementById(textFieldName).up().style.display = 'none';
        $$('label[for="' + textFieldName + '"]').first().hide();
    },

    /**
     * showTextElement: show the first label by name filter.
     *
     * @param String textFieldName The name of the "State/Province" field name.
     *
     * @return void
     */
    showTextElement: function(textFieldName)
    {
        // IE - bugfix, have to hide the parent element from dropdown
        document.getElementById(textFieldName).up().style.display = '';
        $(textFieldName).up().previous('label').show();
        $$('label[for="' + textFieldName + '"]').first().show();
    },

    /**
     * updateRegionIdField: Hide the region field and add hidden input field with given id
     *
     * @param String fieldName       The Name of the RegionId field.
     * @param String regionFieldName Name of the new input field.
     * @param Int    regionId        Id of new RegionId.
     *
     * @return void
     */
    updateRegionIdField: function(fieldName, regionFieldName, regionId)
    {
        var inputString = '<input type="hidden" id="' + regionFieldName + '-tmp" name="' + regionFieldName +'" value="' + regionId + '">';
        //To hide this element is recomment couse Magento work with it on change Country
        $(fieldName).hide();
        if ($(regionFieldName + '-tmp')) {
            $(regionFieldName + '-tmp').value = regionId;
        } else {
            $(fieldName).insert({after: inputString});
        }
    },

    /**
     * getLastRegionId: Get the last id of region in options
     *
     * @param String fieldName The Name of the RegionId field.
     *
     * @return int
     */
    getLastRegionId: function(fieldName)
    {
        var region = $A($(fieldName).options).last();

        return region.getAttribute('value');
    },

    /**
     * checkCountryCode check if the country is germany
     *
     * @param String countryFieldName The Name of country field.
     *
     * @return boolean
     */
    checkCountryCode: function(countryFieldName)
    {
        if (this.countries.indexOf($F(countryFieldName)) != -1) {
            return true;
        }
        return false;
    }
});
