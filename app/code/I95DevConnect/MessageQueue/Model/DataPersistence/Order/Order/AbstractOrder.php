<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 * @author Divya Koona. Removed isTargetCustomerAvailable function.
 */

namespace I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order;

/**
 * Class AbstractOrder contains method to initialize currentObject
 */
class AbstractOrder
{
    public $currentObject;
    public $logger;
    public $genericHelper;
    public $targetFieldErp = 'targetId';
    public $stringData;
    public $entityCode;
    /**
     *
     * @var postData[]
     */
    public $postData = [];

    public $validate;

    /**
     *
     * @param \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger
     * @param \I95DevConnect\MessageQueue\Helper\Generic $genericHelper
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Validate $validate
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger,
        \I95DevConnect\MessageQueue\Helper\Generic $genericHelper,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Validate $validate
    ) {
        $this->logger = $logger;
        $this->genericHelper = $genericHelper;
        $this->validate = $validate;
    }

    /**
     * Initialize $this->currentObject
     * @param array $orderObject
     * @return $this
     * @author Debashis S. Gopal
     */
    public function currentObject($orderObject)
    {
        $this->currentObject = $orderObject;
        return $this;
    }
}
