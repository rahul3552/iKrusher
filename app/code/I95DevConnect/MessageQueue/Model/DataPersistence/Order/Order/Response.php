<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order;

/**
 * Class responsible for saving erp responses in order
 */
class Response
{

    private $orderStatusHelper;
    public $dataHelper;
    public $eventManager;
    public $i95DevMagentoMQRepository;
    public $i95DevMagentoMQData;
    public $statusCode = '5';
    public $updatedBy = 'ERP';
    public $orderId;
    public $erpCode;
    public $targetId;
    public $entityCode;
    public $stringData;

    /**
     * scopeConfig for system Congiguration
     *
     * @var string
     */
    public $scopeConfig;

    /**
     * @var customSalesOrder
     */
    public $customSalesOrder;

    /**
     * @var \I95DevConnect\MessageQueue\Model\AbstractDataPersistence
     */
    public $abstractDataPersistence;

    public $messageId;
    const SKIPOBRVR = "i95_observer_skip";

    /**
     *
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     * @param \I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterfaceFactory $i95DevMagentoMQRepository
     * @param \I95DevConnect\MessageQueue\Api\Data\I95DevMagentoMQInterfaceFactory $i95DevMagentoMQData
     * @param \I95DevConnect\MessageQueue\Helper\ErpOrderStatus $orderStatusHelper
     * @param \Magento\Framework\Event\Manager $eventManager
     * @param \I95DevConnect\MessageQueue\Model\SalesOrder $customSalesOrder
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \I95DevConnect\MessageQueue\Model\AbstractDataPersistence $abstractDataPersistence
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper,
        \I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterfaceFactory $i95DevMagentoMQRepository,
        \I95DevConnect\MessageQueue\Api\Data\I95DevMagentoMQInterfaceFactory $i95DevMagentoMQData,
        \I95DevConnect\MessageQueue\Helper\ErpOrderStatus $orderStatusHelper,
        \Magento\Framework\Event\Manager $eventManager,
        \I95DevConnect\MessageQueue\Model\SalesOrder $customSalesOrder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \I95DevConnect\MessageQueue\Model\AbstractDataPersistence $abstractDataPersistence
    ) {
        $this->dataHelper = $dataHelper;
        $this->i95DevMagentoMQRepository = $i95DevMagentoMQRepository;
        $this->i95DevMagentoMQData = $i95DevMagentoMQData;
        $this->orderStatusHelper = $orderStatusHelper;
        $this->eventManager = $eventManager;
        $this->customSalesOrder = $customSalesOrder;
        $this->scopeConfig = $scopeConfig;
        $this->abstractDataPersistence = $abstractDataPersistence;
    }

    /**
     * Sets target order details in order
     *
     * @param array $requestData
     * @param string $entityCode
     * @param string $erpCode
     *
     * @return \I95DevConnect\MessageQueue\Api\I95DevResponseInterface
     * @throws \Exception
     * @author Divya Koona. Removed of inserting gp_orderprocess_flag column value to i95dev_sales_flat_order table
     */
    public function getResponse($requestData, $entityCode, $erpCode)
    {
        try {
            $this->stringData = $requestData;
            $this->orderId = $this->dataHelper->getValueFromArray("sourceId", $requestData);
            $this->targetId = $this->dataHelper->getValueFromArray("targetId", $requestData);
            $this->messageId = $this->dataHelper->getValueFromArray("messageId", $requestData);
            $this->entityCode = $entityCode;

            //Updated By Sravani Polu, Changed API call to Interface call to get order interface
            $order = $this->orderStatusHelper->getOrderByIncrementId($this->orderId);
            if (is_object($order) && $order->getEntityId()) {
                if (!isset($erpCode)) {
                    $this->erpCode = "ERP";
                } else {
                    $this->erpCode = $erpCode;
                }

                $this->saveDataInOutboundMQ();
                $this->dataHelper->unsetGlobalValue(self::SKIPOBRVR);
                $this->dataHelper->setGlobalValue(self::SKIPOBRVR, true);

                if ($this->targetId != "") {
                    $customOrder = $this->getCustomOrder($order->getIncrementId());
                    $customOrder->setTargetOrderId($this->targetId);
                    $customOrder->setUpdateBy($this->erpCode);
                    $origin = $customOrder->getData('origin');
                    if (empty($origin)) {
                        $customOrder->setData('origin', 'website');
                    }
                    $customOrder->save();
                }

                $this->dataHelper->unsetGlobalValue(self::SKIPOBRVR);
                $orderResponseEvent = "erpconnect_forward_orderresponse";
                $this->eventManager->dispatch($orderResponseEvent, ['currentObject' => $this]);
                return $this->abstractDataPersistence->setResponse(
                    \I95DevConnect\MessageQueue\Helper\Data::SUCCESS,
                    __("Response send successfully"),
                    null
                );
            } else {
                return $this->abstractDataPersistence->setResponse(
                    \I95DevConnect\MessageQueue\Helper\Data::ERROR,
                    __("Some error occured in response sync"),
                    null
                );
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            return $this->abstractDataPersistence->setResponse(
                \I95DevConnect\MessageQueue\Helper\Data::ERROR,
                __($ex->getMessage()),
                null
            );
        }
    }

    /**
     * Retrieve i95dev custom order by source order id
     * @param int $sourceOrderId
     * @return \I95DevConnect\MessageQueue\Model\SalesOrder
     */
    public function getCustomOrder($sourceOrderId)
    {
        $customOrderModel = $this->customSalesOrder;
        $customOrderData = $customOrderModel->getCollection()
                        ->addFieldToSelect('id')
                        ->addFieldToFilter('source_order_id', $sourceOrderId);
        $customOrderData->getSelect()->limit(1);
        $customOrderData = $customOrderData->getData();

        $customOrderId = (isset($customOrderData[0]['id']) ? $customOrderData[0]['id'] : '');

        return $customOrderModel->load($customOrderId);
    }

    /**
     * Update outbound table once the order synced to ERP
     */
    public function saveDataInOutboundMQ()
    {
        $i95DevMagentoMQ = $this->i95DevMagentoMQData->create();
        $i95DevMagentoMQ->setMsgId($this->messageId);
        $i95DevMagentoMQ->setStatus($this->statusCode);
        $i95DevMagentoMQ->setUpdatedby($this->updatedBy);
        $i95DevMagentoMQ->setTargetId($this->targetId);

        $this->i95DevMagentoMQRepository->create()->saveMQData($i95DevMagentoMQ);
    }
}
