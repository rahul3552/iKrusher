<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model\DataPersistence\Order;

/**
 * Class for creating Order, getting Order info and setting Order response
 */
class Order
{
    public $orderInfo;
    public $orderResponse;
    public $orderCreate;

    /**
     *
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order\Response $orderResponse
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order\Info $orderInfo
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order\Create $orderCreate
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order\Response $orderResponse,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order\Info $orderInfo,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order\Create $orderCreate
    ) {
        $this->orderResponse = $orderResponse;
        $this->orderInfo = $orderInfo;
        $this->orderCreate = $orderCreate;
    }

    /**
     * Create Order.
     *
     * @param string $stringData
     * @param string $entityCode
     * @param string $erp
     *
     * @return \I95DevConnect\MessageQueue\Api\I95DevResponseInterface
     * @throws \Exception
     */
    public function create($stringData, $entityCode, $erp = null)
    {
        return $this->orderCreate->createOrder($stringData, $entityCode, $erp);
    }

    /**
     * Get Order information
     *
     * @param int $orderId
     * @param string $entityCode
     * @param string $erpCode
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    public function getInfo($orderId, $entityCode, $erpCode = null)
    {
        return  $this->orderInfo->getInfo($orderId, $entityCode, $erpCode);
    }

    /**
     * Sets target Order information
     *
     * @param array $requestData
     * @param string $entityCode
     * @param string $erpCode
     * @return \I95DevConnect\MessageQueue\Api\I95DevResponseInterface
     * @throws \Exception
     */
    public function getResponse($requestData, $entityCode, $erpCode = null)
    {
        return $this->orderResponse->getResponse($requestData, $entityCode, $erpCode);
    }
}
