<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_I95DevServer
 */
namespace I95DevConnect\I95DevServer\Model\ServiceMethod;

use I95DevConnect\I95DevServer\Model\ServiceMethod\ReverseSync\ErpToMQ\Generic;
use I95DevConnect\MessageQueue\Api\I95DevErpMQRepositoryInterface;
use I95DevConnect\MessageQueue\Api\I95DevReverseResponseInterfaceFactory;
use I95DevConnect\MessageQueue\Helper\Data;
use I95DevConnect\MessageQueue\Helper\DataPersistence;
use I95DevConnect\MessageQueue\Model\ErrorUpdateDataFactory;
use I95DevConnect\MessageQueue\Model\I95DevResponse;
use I95DevConnect\MessageQueue\Model\Logger;
use Magento\Framework\Event\Manager;

/**
 * Class for ERP to Magento sync
 */
class ReverseSync extends AbstractServiceMethod
{

    const MSG = "message";
    const TARGETID = "targetId";
    const SOURCEID = "sourceId";
    const MSGID = "messageId";
    const MSG_ID = "msg_id";
    const MSGID_C = "MessageId";
    const ISCHILD = "isChild";
    const STATUS = "status";
    const RESULT = "result";
    const TARGET_ID = "target_id";
    const ENTITY_NAME = "entityName";
    const ENTITY_CODE = "entity_code";
    const ERROR_ID = "error_id";
    const MGT_ID = "magento_id";
    const INPUT_DATA = "inputData";

    public $genericErpToMQ;
    public $genericMQToMagento;
    public $persistenceHelper;
    public $dataHelper;
    public $i95DevErpMQ;
    public $currentEntityProperties;
    public $responseData;
    public $i95DevRevResponse;
    public $parentData = null;
    public $inputData = [];
    public $childEntityName;
    public $errorUpdateData;
    public $recordResponse;

    /**
     * @var Manager
     */
    public $eventManager;

    /**
     * Constructor for DI
     *
     * @param Logger $logger
     * @param I95DevResponse $i95DevResponse
     * @param Generic $genericErpToMQ
     * @param ReverseSync\MQToEcomm\Generic $genericMQToMagento
     * @param DataPersistence $persistenceHelper
     * @param Data $dataHelper
     * @param I95DevErpMQRepositoryInterface $i95DevErpMQ
     * @param I95DevReverseResponseInterfaceFactory $i95DevRevResponse
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     * @param ErrorUpdateDataFactory $errorUpdateData
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Manager $eventManager
     */
    public function __construct(
        Logger $logger,
        I95DevResponse $i95DevResponse,
        Generic $genericErpToMQ,
        \I95DevConnect\I95DevServer\Model\ServiceMethod\ReverseSync\MQToEcomm\Generic $genericMQToMagento,
        DataPersistence $persistenceHelper,
        Data $dataHelper,
        I95DevErpMQRepositoryInterface $i95DevErpMQ,
        I95DevReverseResponseInterfaceFactory $i95DevRevResponse,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        ErrorUpdateDataFactory $errorUpdateData,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Manager $eventManager
    ) {

        $this->genericErpToMQ = $genericErpToMQ;
        $this->genericMQToMagento = $genericMQToMagento;
        $this->persistenceHelper = $persistenceHelper;
        $this->dataHelper = $dataHelper;
        $this->i95DevErpMQ = $i95DevErpMQ;
        $this->i95DevRevResponse = $i95DevRevResponse;
        $this->errorUpdateData = $errorUpdateData;
        $this->eventManager = $eventManager;
        parent::__construct($logger, $i95DevResponse, $scopeConfigInterface, $storeManager);
    }

    /**
     * Method to insert ERP string in to Inbound MQ table
     *
     * @param string $methodName
     * @param string $inputString
     * @param object $currentMethodProperties
     * @param null|string $erpName
     * @return Object
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function syncDataToMQ($methodName, $inputString, &$currentMethodProperties, $erpName = null)
    {
        $this->erpName = !empty($erpName)?$erpName : __('ERP');
        $this->currentMethodProperties = $currentMethodProperties;

        $recordStatus = true;
        $recordMessage = "";

        $this->setCurrentEntityCode($currentMethodProperties);

        $inputArray = $this->convertInputStringToArray($inputString);

        /**
         * @updatedBy Debashis S. Gopal. Return false with error message. If decoding failed for $inputString.
         */
        if (empty($inputArray)) {
            $this->setResponse(false, 'Unable decode input string.', []);
            return $this->i95DevResponse;
        }

        $finalString = $this->processInputArray($inputArray);
        foreach ($finalString as $singleRecord) {
            $messageId = null;
            try {
                $entityRoute = $this->initialEntityRoute($currentMethodProperties, $singleRecord, $this->inputData);
                $messageId = $entityRoute[self::MSGID];
            } catch (\Magento\Framework\Exception\LocalizedException $ex) {
                $recordMessage = $ex->getMessage();
                $recordStatus = false;
                $messageId = null;
                $this->logger->createLog(
                    __METHOD__,
                    $methodName." :: ".$ex->getMessage(),
                    \I95DevConnect\MessageQueue\Api\LoggerInterface::I95EXC,
                    'critical'
                );
            }

            if (isset($currentMethodProperties['isParent']) || isset($currentMethodProperties[self::ISCHILD])) {
                $this->callCreateResponse($currentMethodProperties, $messageId, $singleRecord);
            } else {
                $this->responseData[] = ($this->parentData !== null) ?
                    $this->parentData : $this->createResponse($messageId, $singleRecord);
            }
        }

        $this->setResponse($recordStatus, $recordMessage, $this->responseData);

        return $this->i95DevResponse;
    }

    /**
     * @param $messageId
     * @param $singleRecord
     * @param $currentMethodProperties
     */
    public function callCreateResponse($currentMethodProperties, $messageId, $singleRecord)
    {
        if (isset($currentMethodProperties['isParent'])) {
            $this->parentData = $this->createResponse($messageId, $singleRecord);
        } elseif (isset($currentMethodProperties[self::ISCHILD])) {
            $this->childEntityName = $currentMethodProperties[self::ISCHILD];
            $this->inputData[] = $this->createResponse($messageId, $singleRecord);
        }
    }

    /**
     * @param $currentMethodProperties
     */
    public function setCurrentEntityCode($currentMethodProperties)
    {
        $currentEntityCode = '';
        if (isset($currentMethodProperties['entityCode'])) {
            $currentEntityCode = $currentMethodProperties['entityCode'];
        } else {
            $this->setResponse("Error", "Entity code not exists", null);
        }
        $this->getCurrentEnityProperties($currentEntityCode);
    }
    /**
     * @param $currentMethodProperties
     * @param $singleRecord
     * @param $inputData
     * @return array
     */
    public function initialEntityRoute($currentMethodProperties, $singleRecord, $inputData)
    {
        if (isset($currentMethodProperties['classObject']) && isset($currentMethodProperties['methodName'])
        ) {
            $this->parentData = null;
            /* Called for customer and address entity */
            $customClassModelObject = $currentMethodProperties['classObject'];
            $methodName = $currentMethodProperties['methodName'];
            $messageId = $customClassModelObject->$methodName($singleRecord);

            if (isset($currentMethodProperties['isDivided']) && count($this->inputData) > 0) {
                $inputData[$this->childEntityName] = $this->inputData;
                $this->parentData->setInputdata($this->encryptAES(json_encode($inputData)));
            }
        } else {
            /* Called for all entity - Generic function call */
            $messageId = $this->genericErpToMQ->defaultMessageQueueInsert(
                $singleRecord,
                $this
            );
        }
        return [self::MSGID =>$messageId];
    }

    /**
     * @param $inputArray
     * @return array
     */
    public function processInputArray($inputArray)
    {
        if (isset($inputArray['RequestData'])) {
            $requestDataArr = $inputArray['RequestData'];
        } elseif (isset($inputArray['requestData'])) {
            $requestDataArr = $inputArray['requestData'];
        } else {
            $requestDataArr = [];
        }

        return $this->getFinalString($inputArray, $requestDataArr);
    }

    /**
     * @param $inputArray
     * @param $requestDataArr
     * @return array
     */
    public function getFinalString($inputArray, $requestDataArr)
    {
        $finalString = [];
        if (!empty($requestDataArr) && count($requestDataArr) > 0) {
            foreach ($requestDataArr as $requestData) {
                if (isset($requestData['InputData'])) {
                    $inputDataString = $requestData['InputData'];
                } elseif (isset($requestData['inputData'])) {
                    $inputDataString = $requestData['inputData'];
                } else {
                    $inputDataString = '';
                }
                $decryptedString = $this->convertInputStringToArray($this->decryptDES($inputDataString));
                if (isset($requestData[self::MSGID]) && $requestData[self::MSGID] !== 0) {
                    $decryptedString['DestinationId'] = $requestData[self::MSGID];
                }
                $finalString[] = $decryptedString;
            }
        } else {
            $finalString = $inputArray;
        }

        return $finalString;
    }

    /**
     *
     * @param int $messageId
     * @param array $singleRecord
     * @param string $inputData
     * @return object
     */
    public function createResponse($messageId, $singleRecord, $inputData = null)
    {
        $i95DevRevResponseData = null;
        if (isset($messageId)) {
            if (is_numeric($messageId)) {
                $recordStatus = true;
                $recordMessage = "";
            } else {
                if (is_array($messageId)) {
                    $recordStatus = false;
                    $recordMessage = $messageId[self::MSG];
                    $messageId = $messageId[self::MSGID];
                } else {
                    $recordStatus = false;
                    $recordMessage = $messageId;
                }
            }

            $i95DevRevResponseData = $this->i95DevRevResponse->create();
            $i95DevRevResponseData->setResult($recordStatus);
            $i95DevRevResponseData->setMessageid($messageId);
            $i95DevRevResponseData->setMessage($recordMessage);
            if (isset($singleRecord[self::TARGETID])) {
                $i95DevRevResponseData->setTargetid($singleRecord[self::TARGETID]);
            }
            if (isset($this->currentMethodProperties[self::ISCHILD]) && isset($singleRecord['targetCustomerId'])) {
                $i95DevRevResponseData->setTargetcustomerid($singleRecord['targetCustomerId']);
            }
            if (!empty($inputData)) {
                $i95DevRevResponseData->setInputdata($inputData);
            }
        }
        return $i95DevRevResponseData;
    }

    /**
     * business layer to sync data from MQ to magento
     */
    public function syncMQtoMagento()
    {
        if ($this->dataHelper->isEnabled()) {
            try {
                $mqCollection = $this->getMQPendingDataCollection();

                if ($mqCollection->getSize() > 0) {
                    $this->genericMQToMagento->reverseSyncMQtoMagento($mqCollection);
                }
            } catch (\Magento\Framework\Exception\LocalizedException $ex) {
                $this->logger->createLog(
                    __METHOD__,
                    $ex->getMessage(),
                    \I95DevConnect\MessageQueue\Api\LoggerInterface::I95EXC,
                    'critical'
                );
            }
        }
    }

    /**
     * get the MQ data collection that needs to be synced to magento
     * @return object
     */
    private function getMQPendingDataCollection()
    {
        $packetSize = $this->scopeConfigInterface->getValue(
            'i95dev_messagequeue/i95dev_extns/packet_size',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $this->storeManager->getDefaultStoreView()->getWebsiteId()
        );

        if (!$packetSize) {
            $packetSize = 50;
        }

        $retryLimit = $this->scopeConfigInterface->getValue(
            'i95dev_messagequeue/I95DevConnect_mqsettings/retry_limit',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $this->storeManager->getDefaultStoreView()->getWebsiteId()
        );

        if (!$retryLimit) {
            $retryLimit = Data::RETRY_LIMIT;
        }

        $i95DevErpMQColl =  $this->i95DevErpMQ->getCollection();
        $i95DevErpMQColl->addFieldToSelect(self::MSG_ID, self::MSG_ID)
            ->addFieldToFilter(
                self::STATUS,
                [
                    ['eq' => Data::PENDING],
                    ['eq' => Data::ERROR]
                ]
            )
            ->addFieldToFilter(
                'counter',
                ['lt' => $retryLimit]
            )
            ->addFieldToFilter(
                'entity_code',
                [
                    ['neq' => 'inventory'],
                    ['neq' => 'tierprice'],
                    ['neq' => 'pricelevel']
                ]
            )
            ->setOrder(self::MSG_ID, 'ASC'); //@updatedBy Debashis S. Gopal. Sort order added in collection
        $i95DevErpMQColl->getSelect()
            ->limit($packetSize);

        return $i95DevErpMQColl;
    }

    /**
     * Method to get MQ status
     * @param string $finalRequest
     * @return object
     */
    public function getMessageQueueStatus($finalRequest)
    {
        $collection = $this->initAcknowledgement($finalRequest);
        $result = [];
        if ($collection) {
            foreach ($collection as $singleRecordCollection) {
                $this->recordResponse = [];
                $this->recordResponse[self::RESULT] = false;
                $status = $singleRecordCollection->getStatus();
                if ($status == Data::SUCCESS ||
                    $status == Data::COMPLETE) {
                    $this->recordResponse[self::RESULT] = true;
                }
                $this->recordResponse[self::MSGID] = $singleRecordCollection->getId();
                $this->recordResponse[self::ERROR_ID] = $singleRecordCollection->getErrorId();

                $msg = $this->getErrorMsg($this->recordResponse);

                $this->recordResponse[self::MSG] = $msg;
                $this->recordResponse[self::TARGETID] = $singleRecordCollection->getTargetId();
                $this->recordResponse[self::SOURCEID] = $singleRecordCollection->getMagentoId();
                $this->recordResponse[self::ENTITY_NAME] = $singleRecordCollection->getEntityCode();

                if ($singleRecordCollection->getEntityCode() == 'Customer') {
                    $this->recordResponse = $this->getCustomerStatus($singleRecordCollection, $this->recordResponse);
                    if (!$this->recordResponse) {
                        continue;
                    }
                }

                $responseEvent = "erpconnect_send_reverse_response";
                $this->eventManager->dispatch($responseEvent, ['responseObject' => $this]);

                $result[] = $this->recordResponse;
            }
            $this->setResponse(true, "", $result);
        }

        return $this->i95DevResponse;
    }

    /**
     * Method to set Acknowledgement
     * @param string $finalRequest
     * @return object
     */
    public function setMessageQueueAck($finalRequest)
    {
        $collection = $this->initAcknowledgement($finalRequest);
        $result = [];
        if ($collection) {
            foreach ($collection as $singleRecordCollection) {
                $recordResponse = [];
                $recordResponse[self::RESULT] = false;
                $status = $singleRecordCollection->getStatus();
                if ($status == Data::SUCCESS ||
                    $status == Data::COMPLETE) {
                    $singleRecordCollection->setStatus(Data::COMPLETE);
                    $singleRecordCollection->save();
                    $recordResponse[self::RESULT] = true;
                }

                $recordResponse[self::MSGID] = $singleRecordCollection->getId();
                $recordResponse[self::TARGETID] = $singleRecordCollection->getTargetId();
                $recordResponse[self::SOURCEID] = $singleRecordCollection->getMagentoId();
                $recordResponse[self::ENTITY_NAME] = $singleRecordCollection->getEntityCode();

                if ($singleRecordCollection->getEntityCode() == 'Customer') {
                    $recordResponse = $this->setMessageQueueAckForCustomer($singleRecordCollection, $recordResponse);
                }

                if (!$recordResponse) {
                    continue;
                }

                $result[] = $recordResponse;
            }
            $this->setResponse(true, "", $result);
        }

        return $this->i95DevResponse;
    }

    /**
     * @param $singleRecordCollection
     * @param $recordResponse
     * @return array
     */
    public function setMessageQueueAckForCustomer($singleRecordCollection, $recordResponse) //NOSONAR
    {
        $status = [
            Data::PENDING,
        ];
        $pendingAddress = $this->getMqAddressDataByParentId(
            $status,
            $recordResponse[self::MSGID]
        )->getData();

        if (count($pendingAddress) > 0) {
            return null;
        }

        $status = [
            Data::SUCCESS,
            Data::COMPLETE,
            Data::ERROR
        ];
        $collectionArr = $this->getMqAddressDataByParentId($status, $recordResponse[self::MSGID]);

        $addresses = $this->prepareAddressAcknowledgement($collectionArr);

        if (!empty($addresses)) {
            $recordResponse['responseData']['addresses'] = $addresses;
        }

        return $recordResponse;
    }

    /**
     * @param $collectionArr
     * @return mixed
     */
    public function prepareAddressAcknowledgement($collectionArr)
    {
        $addresses = null;
        foreach ($collectionArr as $address) {
            $msgAddress = $this->getErrorMsg($address);
            $addressStatus = $address[self::STATUS];
            $addresses = $this->saveAddress($addressStatus, $address, $msgAddress);
        }
        return $addresses;
    }

    /**
     * @param $addressStatus
     * @param $address
     * @param $msgAddress
     * @return array
     * @return array
     */
    public function saveAddress($addressStatus, $address, $msgAddress)
    {
        $addresses = [];
        if ($addressStatus == Data::SUCCESS ||
            $addressStatus == Data::COMPLETE) {
            $address->setStatus(Data::COMPLETE);
            $address->save();

            $addresses[] = [
                self::RESULT => true,
                self::MSGID => $address[self::MSG_ID],
                self::TARGETID => $address[self::TARGET_ID],
                self::SOURCEID => $address[self::MGT_ID],
                self::ENTITY_NAME => $address[self::ENTITY_CODE],
                self::MSG => $msgAddress
            ];
        }

        return $addresses;
    }
    /**
     * @param $singleRecordCollection
     * @param $recordResponse
     * @return bool
     */
    public function getCustomerStatus($singleRecordCollection, $recordResponse)
    {
        $status = [Data::PENDING];
        $pendingAddress = $this->getMqAddressDataByParentId(
            $status,
            $recordResponse[self::MSGID]
        )->getData();

        if (count($pendingAddress) > 0) {
            return false;
        }

        $status = [
            Data::SUCCESS,
            Data::COMPLETE,
            Data::ERROR
        ];
        $collectionArr = $this->getMqAddressDataByParentId(
            $status,
            $recordResponse[self::MSGID]
        )->getData();

        $addresses = $this->processErroredAddressStatus($collectionArr, $singleRecordCollection);

        /** @author Debashis S. Gopal. Sending address response as encrypted string as expected by ERP **/
        if (!empty($addresses)) {
            $inputData['addresses'] = $addresses;
            $recordResponse[self::INPUT_DATA] = $this->encryptAES(json_encode($inputData));
        }
        return $recordResponse;
    }

    /**
     * @param $collectionArr
     * @param $singleRecordCollection
     * @return mixed
     */
    public function processErroredAddressStatus($collectionArr, $singleRecordCollection)
    {
        $addresses = [];
        foreach ($collectionArr as $address) {
            $msgAddress = $this->getErrorMsg($address);
            $addresses[] = [
                self::MSGID => $address[self::MSG_ID],
                self::TARGETID => $address[self::TARGET_ID],
                'reference' => $singleRecordCollection->getTargetId(),
                self::SOURCEID => $address[self::MGT_ID],
                self::ENTITY_NAME => $address[self::ENTITY_CODE],
                self::MSG => $msgAddress
            ];
        }
        return $addresses;
    }

    /**
     * common code for reverse acknowledgement and response
     *
     * @param string $finalRequest
     * @return boolean/Object
     * @createdBy Arushi Bansal
     */
    public function initAcknowledgement($finalRequest)
    {
        $dataString = $this->convertInputStringToArray($finalRequest);
        $msgId = [];
        if (isset($dataString['requestData'])) {
            foreach ($dataString['requestData'] as $value) {
                $msgId[] = $value[self::MSGID];
            }
        }

        $collection = $this->i95DevErpMQ->getCollection();
        $collection
            ->addFieldToSelect([self::STATUS, self::ERROR_ID, self::TARGET_ID, self::ENTITY_CODE, self::MGT_ID]);
        $collection->addFieldToFilter("msg_id", ["in" => implode(',', $msgId)]);
        if ($collection->getSize() > 0) {
            return $collection;
        } else {
            $this->setResponse(false);
            return false;
        }
    }

    /**
     * get inbound messagequeue address by status
     * @param array $status
     * @param int $parent_id
     * @return object
     * @createdBy Arushi Bansal
     */
    public function getMqAddressDataByParentId($status, $parent_id)
    {
        $cols = [self::MSG_ID, self::TARGET_ID, self::MGT_ID , self::ENTITY_CODE , self::ERROR_ID, self::STATUS];
        return $this->i95DevErpMQ->getCollection()
            ->addFieldToSelect($cols)
            ->addFieldToFilter(self::ENTITY_CODE, 'address')
            ->addFieldToFilter(
                self::STATUS,
                [
                    'in' => $status
                ]
            )
            ->addFieldToFilter('parent_msg_id', $parent_id);
    }

    /**
     * @param $recordResponse
     * @return string
     */
    public function getErrorMsg($recordResponse)
    {
        $msg = '';
        if (isset($recordResponse[self::ERROR_ID])) {
            $errorData = $this->errorUpdateData->create()->load($recordResponse[self::ERROR_ID]);
            $list = explode(",", $errorData->getMsg());
            if (!empty($list)) {
                foreach ($list as $lt) {
                    if ($msg != "") {
                        $msg .= ", ";
                    }
                    $msg .= __(trim($lt));
                }
            }
        }

        return $msg;
    }
}
