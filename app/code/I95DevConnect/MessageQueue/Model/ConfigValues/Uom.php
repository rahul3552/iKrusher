<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model\ConfigValues;

use I95DevConnect\MessageQueue\Helper\Data;
use \I95DevConnect\MessageQueue\Api\LoggerInterface;

/**
 * Class for Getting Supported UOM
 */
class Uom implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @var Data
     */
    public $data;

    /**
     *
     * @var LoggerInterface
     */
    public $logger;
    
    /**
     *
     * @param Data $data
     * @param LoggerInterface $logger
     */
    public function __construct(Data $data, LoggerInterface $logger)
    {
        $this->data = $data;
        $this->logger = $logger;
    }

    /**
     * Getting Supported UOM array
     *
     * @return array
     */
    public function toOptionArray()
    {
        try {
            return [
                ['value' => 'Each', 'label' => __('Each')],
            ];
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->createLog(
                __METHOD__,
                $ex->getMessage(),
                LoggerInterface::I95EXC,
                LoggerInterface::CRITICAL
            );
        }
    }
}
