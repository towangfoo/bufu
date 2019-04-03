<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Orderarchive
 */
class Amasty_Orderarchive_Helper_Archive extends Mage_Core_Helper_Abstract
{

    protected $actions = array(
        'add_to_archive'      => 'addToArchive',
        'remove_from_archive' => 'removeFromArchive',
        'remove_permanently'  => 'removePermanently',
    );

    /**
     * @var Magento_Db_Adapter_Pdo_Mysql
     */
    protected $connection;

    protected function initConnection()
    {
        $this->connection = $this->connection
            ? $this->connection
            : Mage::getSingleton('core/resource')->getConnection('core/write');
    }

    /**
     * Registered Collections for archiving
     * @var array collection Amasty_Orderarchive_Helper_Archive_BaseArchiveHelper instances
     */
    protected $registeredArchiver = array();

    public function __construct()
    {
        $this->initConnection();
        $this->registeredArchiver = $this->registerArchiver();
    }

    protected function registerArchiver()
    {
        return array(
           'order' => Mage::helper('amorderarchive/archive_order'),
           'shipment' => Mage::helper('amorderarchive/archive_shipment'),
           'invoice' => Mage::helper('amorderarchive/archive_invoice'),
           'creditmemo' => Mage::helper('amorderarchive/archive_creditmemo'),
        );
    }

    /**
     * @param array $params array parameters [description]
     *  @option array entity_id array of entities id
     *
     * @return array array contain a result of each archived table
     */
    public function addToArchive($params = array())
    {
        $result = $this->doAction('add_to_archive',  $params);
        return $result;
    }

    public function removeFromArchive($params = array())
    {
        return $this->doAction('remove_from_archive',  $params);
    }

    /**
     * @param array $params [description]
     * @option array entity_id array of entities id
     *
     * @return array
     */
    public function removePermanently($params = array())
    {
        return $this->doAction('remove_permanently', $params);
    }

    /**
     * @param string $actionKey key action
     * @param array $params
     * @return array
     * @throws Mage_Core_Exception
     */
    protected function doAction($actionKey, $params)
    {
        $result = array();

        try {
            foreach ($this->registeredArchiver as $archiverName => $archiver) {
                if(array_key_exists('order', $result) ) {
                    $params['order_id'] = $result['order'];
                }

                if (!array_key_exists($actionKey, $this->actions)) {
                    Mage::throwException(sprintf('actionKey %s not found class property', $actionKey));
                }

                $actionName = $this->actions[$actionKey];

                if (!method_exists($archiver, $actionName)) {
                    Mage::throwException(sprintf('Method %s not found in %s', $actionName, get_class($archiver)));
                }

                $result[$archiverName] = $archiver->$actionName($params, $this->connection);
            }
            $this->connection->commit();

        }
        catch (Exception $e) {
            $this->connection->rollback();
            Mage::throwException($e->getMessage());
        }

        return $result;
    }

    public function prepareParams(array$params)
    {

    }

}