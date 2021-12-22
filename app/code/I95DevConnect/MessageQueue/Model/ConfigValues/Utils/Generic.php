<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model\ConfigValues\Utils;

use Magento\Framework\Model\AbstractModel;
use \I95DevConnect\MessageQueue\Helper\Data;
use \Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Simplexml\Config;
use \I95DevConnect\MessageQueue\Api\LoggerInterface;

/**
 * Class Generic for getting configuration value.
 */
class Generic extends AbstractModel
{

    /**
     * @var Data $data
     */
    public $data;

    /**
     *
     * @var Reader $reader
     */
    public $reader;

    /**
     *
     * @var Config $config
     */
    public $config;

    /**
     *
     * @var LoggerInterface
     */
    public $logger;

    /**
     *
     * @param Data $data
     * @param Reader $reader
     * @param Config $config
     * @param LoggerInterface $logger
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Data $data,
        Reader $reader,
        Config $config,
        LoggerInterface $logger,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry
    ) {
        $this->data = $data;
        $this->reader = $reader;
        $this->config = $config;
        $this->logger = $logger;

        parent::__construct($context, $registry);
    }

    /**
     * Gets setting.xml data as array by node
     *
     * @param  String node
     * @return array
     */
    public function getSettings($node = null)
    {
        $xmlData = null;
        try {
            $xmlData = $this->config->getNode($node);
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->createLog(
                __METHOD__,
                $ex->getMessage(),
                LoggerInterface::I95EXC,
                LoggerInterface::CRITICAL
            );
        }
        return $xmlData->asArray();
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'invoice', 'label' => __('Invoice')],
            ['value' => 'shipment', 'label' => __('Shipment')]
        ];
    }
}
