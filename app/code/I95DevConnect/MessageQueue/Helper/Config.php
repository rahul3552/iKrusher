<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Helper;

use Magento\Framework\App\Helper\Context;

/**
 * Helper Class returns the configuration of connector
 */
class Config extends \Magento\Framework\App\Helper\AbstractHelper
{

    public $logger;

    /**
     * scopeConfig for system Congiguration
     *
     * @var string
     */
    public $scopeConfig;
    /*
     * core /write  for database
     */
    public $resource;

    /*
     * connection for database connection
     */
    public $connection;

    /**
     * MageCustomerApi
     *
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $data;

    /**
     *
     * @var \Magento\Framework\DataObject
     */
    public $obj;

    /**
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \I95DevConnect\MessageQueue\Helper\Data $data
     * @param \Magento\Framework\DataObject $obj
     * @param Context $context
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \I95DevConnect\MessageQueue\Helper\Data $data,
        \Magento\Framework\DataObject $obj,
        Context $context
    ) {
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->resource = $resource;
        $this->connection = $resource->getConnection('write');
        $this->data = $data;
        $this->obj = $obj;
        parent::__construct($context);
    }

    /**
     * Will return the component from connector configurations
     *
     * @return \Magento\Framework\DataObject
     */
    public function getConfigValues()
    {
        try {
            $this->obj->setData('component', 'BC');
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->data->createLog(__METHOD__, $ex->getMessage(), "i95devException", 'critical');
        }
        return $this->obj;
    }
}
