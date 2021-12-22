<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_I95DevServer
 */
namespace I95DevConnect\I95DevServer\Model\ServiceMethod\ForwardSync\MQToErp;

use \I95DevConnect\MessageQueue\Helper\Data;
use Magento\Framework\Event\ManagerInterface;

/**
 * Class to get IDs from Outbound MQ and send to ERP
 */
class SendIds
{
    const STATUS = "status";
    const PKTSIZE = "packetSize";
    const MSG_ID = "msg_id";
    const MSGID = "messageId";

    public $erpName = 'ERP';
    public $scopeConfig;
    public $date;
    public $packetSize;
    public $logger;
    public $helperConfig;
    public $configurable;
    public $i95DevMagentoMQRepository;
    public $erpOrderStatus;
    public $i95DevMagentoMQFactory;
    public $messageErrorModel;

    /**
     * Constructor for DI
     *
     * @param \I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterfaceFactory $i95DevMagentoMQRepository
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \I95DevConnect\MessageQueue\Api\LoggerInterface $logger
     * @param \I95DevConnect\MessageQueue\Helper\ErpOrderStatus $erpOrderStatus
     * @param \I95DevConnect\MessageQueue\Api\Data\I95DevMagentoMQInterfaceFactory $i95DevMagentoMQFactory
     * @param \I95DevConnect\MessageQueue\Model\ErrorUpdateDataFactory $messageErrorModel
     * @param \I95DevConnect\MessageQueue\Helper\Config $helperConfig
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterfaceFactory $i95DevMagentoMQRepository,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \I95DevConnect\MessageQueue\Api\LoggerInterface $logger,
        \I95DevConnect\MessageQueue\Helper\ErpOrderStatus $erpOrderStatus,
        \I95DevConnect\MessageQueue\Api\Data\I95DevMagentoMQInterfaceFactory $i95DevMagentoMQFactory,
        \I95DevConnect\MessageQueue\Model\ErrorUpdateDataFactory $messageErrorModel,
        \I95DevConnect\MessageQueue\Helper\Config $helperConfig,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable,
        ManagerInterface $eventManager
    ) {
        $this->i95DevMagentoMQRepository = $i95DevMagentoMQRepository;
        $this->date = $date;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
        $this->erpOrderStatus = $erpOrderStatus;
        $this->i95DevMagentoMQFactory = $i95DevMagentoMQFactory;
        $this->messageErrorModel = $messageErrorModel;
        $this->helperConfig = $helperConfig;
        $this->configurable = $configurable;
        $this->eventManager = $eventManager;
    }

    /**
     * Returns the list of updated outbound IDs
     *
     * @param string $entityCode
     * @param $requestData
     *
     * @return array
     * @throws \Exception
     */
    public function defaultUpdatedEntityIds($entityCode, $requestData)
    {
        try {
            $responseData = [];
            if (isset($requestData[self::PKTSIZE])) {
                $this->packetSize = $requestData[self::PKTSIZE];
                $responseData = $this->getOutboundUpdatedCollection($entityCode);
            } elseif (count($requestData['requestData']) >0) {
                $responseData = $this->getOutboundCollectionByIds($requestData['requestData']);
            } elseif ($requestData[self::PKTSIZE] === null) {
                $responseData = $this->getOutboundUpdatedCollection($entityCode);
            }
            $this->erpName = isset($requestData['erp_name']) ? $requestData['erp_name'] : __('ERP');
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->createLog(
                __METHOD__,
                $ex->getMessage(),
                Data::I95EXC,
                'critical'
            );
        }

        return $responseData;
    }

    /**
     * Get updated id collection from outbound MQ
     * @param type $entityCode
     * @return array
     * @throws \Exception
     */
    private function getOutboundUpdatedCollection($entityCode)
    {
        $responseData = [];
        try {
            $collection = $this->getUpdatedCollection($entityCode);
            if ($collection->getSize() <= 0) {
                return $responseData;
            }

            $product_mapping_counter = 0;
            foreach ($collection as $recordCollection) {
                if ($this->getDestintionId($recordCollection) > 0) {
                    continue;
                }

                /* @updatedBy Hrusikesh added condition to skip sending child product in response
                 * for configurable product Issue Id: 24586278
                 */
                $parentConfigObject = $this->configurable->getParentIdsByChild($recordCollection->getMagentoId());
                if ($this->checkParentId($parentConfigObject, $entityCode)) {
                    continue;
                }

                $orderCheckStatus = $this->checkOrder($entityCode, $recordCollection);
                if ($orderCheckStatus) {
                    continue;
                }

                if ($entityCode == "product" && $product_mapping_counter == 0) {
                    $this->eventManager->dispatch('fetch_product_mapping_forward');
                    $product_mapping_counter = 1;
                }

                $data['magentoId'] = $recordCollection->getMagentoId();
                $data[self::MSGID] = $recordCollection->getMsgId();
                $responseData[] = $data;
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->createLog(
                __METHOD__,
                $ex->getMessage(),
                Data::I95EXC,
                'critical'
            );
        }

        return $responseData;
    }

    /**
     * @param $entityCode
     * @return mixed
     */
    public function getUpdatedCollection($entityCode)
    {
        $this->erpName = $this->helperConfig->getConfigValues()->getData('component');
        $collection = $this->i95DevMagentoMQRepository->create()->getCollection();
        $collection->addFieldToSelect([self::STATUS, 'destination_msg_id', 'magento_id', self::MSG_ID]);
        $collection->addFieldToFilter("erp_code", $this->erpName);
        $collection->addFieldToFilter("entity_code", $entityCode);
        $collection->addFieldToFilter(self::STATUS, Data::PENDING);
        $collection->getSelect()->order(self::MSG_ID, 'ASC');

        if (!empty($this->packetSize)) {
            $collection->getSelect()->limit($this->packetSize);
        }

        return $collection;
    }

    /**
     * @param $entityCode
     * @param $recordCollection
     * @return bool
     */
    public function checkOrder($entityCode, $recordCollection)
    {
        if ($entityCode == "order" &&
            !empty($message = $this->erpOrderStatus->isOrderSyncable($recordCollection->getMagentoId()))) {
            $errorDataModel = $this->messageErrorModel->create();
            $errorDataModel->setMsg($message);
            $errorDataModel->save();
            $errorId = $errorDataModel->getId();
            $i95DevMagentoMQ = $this->i95DevMagentoMQFactory->create();
            $i95DevMagentoMQ->setMsgId($recordCollection->getMsgId());
            $i95DevMagentoMQ->setStatus(\I95DevConnect\MessageQueue\Helper\Data::ERROR);
            $i95DevMagentoMQ->setErrorId($errorId);
            $this->i95DevMagentoMQRepository->create()->saveMQData($i95DevMagentoMQ);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $recordCollection
     * @return int
     */
    public function getDestintionId($recordCollection)
    {
        if ($recordCollection->getData(self::STATUS) == Data::ERROR) {
            return $recordCollection->getData("destination_msg_id");
        }

        return 0;
    }

    /**
     * @param $parentConfigObject
     * @param $entityCode
     * @return bool
     */
    public function checkParentId($parentConfigObject, $entityCode)
    {
        $parentId = isset($parentConfigObject[0]) ? $parentConfigObject[0] : null;
        if ($entityCode == 'product' && $parentId !== null) {
            return true;
        }
        return false;
    }
    /**
     * get Out Bound MQ collection by Message Queue Ids
     * @param $requestData
     * @return array
     */
    private function getOutboundCollectionByIds($requestData)
    {
        $responseData = [];
        $idList = array_column($requestData, self::MSGID);
        $collection = $this->i95DevMagentoMQRepository->create()->getCollection();
        $collection->addFieldToSelect(['magento_id', self::MSG_ID]);
        $collection->addFieldToFilter(self::MSG_ID, ['in' => $idList]);
        $collection->getSelect()->order(self::MSG_ID, 'ASC');
        if ($collection->getSize() > 0) {
            foreach ($collection as $recordCollection) {
                $data['magentoId'] = $recordCollection->getMagentoId();
                $data[self::MSGID] = $recordCollection->getMsgId();
                $responseData[] = $data;
            }
        }
        return $responseData;
    }
}
