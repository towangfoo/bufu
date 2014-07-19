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
 * @package   Symmetrics_StockIndicator
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Yauhen Yakimovich <yy@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * StockIndicator configuration model
 *
 * @category  Symmetrics
 * @package   Symmetrics_StockIndicator
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Yauhen Yakimovich <yy@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_StockIndicator_Model_Config
{
    /**
     * This configuration path has currently 3 key => value pairs and presents the state
     * of a product:
     *
     * - red
     * - yellow
     * - green
     *
     * Values (stock/quantity) defines when which state gets assign to a product
     *
     * @see Symmetrics_StockIndicator_Block_Abstract::setProductState()
     */
    const STOCK_INDICATOR_CONFIG_PATH = 'cataloginventory/stock_indicator';

    /**
     * The configuration path, to check whether the
     *
     * @var string
     */
    const STOCK_INDICATOR_CONFIG_VALUE_ENABLED = '/product_view_enabled';

    /**
     * The stock indicator is enabled for defined view.
     * This is defined in the backend.
     *
     * @see $_viewEnabledConfigPath
     *
     * @var 0|1
     */
    protected $_isEnabled = null;

    /**
     * Indicator is enabled or not
     *
     * @see $_isEnabled
     * @return bool 1|0
     */
    public function isEnabled()
    {
        if ($this->_isEnabled === null) {
            $path = self::STOCK_INDICATOR_CONFIG_PATH . self::STOCK_INDICATOR_CONFIG_VALUE_ENABLED;
            $this->_isEnabled = Mage::getStoreConfig($path);
        }

        return $this->_isEnabled;
    }

    /**
     * Get stock indicator configuration
     *
     * @see Symmetrics_StockIndicator_Model_Config::STOCK_INDICATOR_CONFIG_PATH
     * @return array indicator config
     */
    public function getConfig()
    {
        $indicatorConfig = Mage::getStoreConfig(self::STOCK_INDICATOR_CONFIG_PATH);

        return $indicatorConfig;
    }
}
