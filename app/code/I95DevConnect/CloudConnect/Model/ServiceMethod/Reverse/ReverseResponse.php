<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_CloudConnect
 */

namespace I95DevConnect\CloudConnect\Model\ServiceMethod\Reverse;

use \I95DevConnect\CloudConnect\Model\Logger;
use \I95DevConnect\MessageQueue\Helper\Data;
use \Magento\Framework\Event\Manager;

/**
 * Model class to send response to cloud from Inbound MQ
 */
class ReverseResponse
{

    private $i95DevRepository;
    public $cloudHelper;
    public $i95DevResponse;
    public $requestInterface;
    public $mqData;
    public $messageQueueModel;
    public $erpName = 'ERP';
    public $logger;
    public $jsonHelper;
    public $abstractServicemethod;

    public $msgIdConst = "msg_id";
    public $errorIdConst = "error_id";
    public $targetIdConst = "target_id";
    public $magentoIdConst = "magento_id";
    public $eventManager;

    /**
     * Constructor for DI
     * @param \I95DevConnect\CloudConnect\Helper\Data $cloudHelper
     * @param \I95DevConnect\CloudConnect\Api\Data\RequestInterfaceFactory $requestInterface
     * @param Logger $logger
     * @param \I95DevConnect\MessageQueue\Api\I95DevErpMQRepositoryInterfaceFactory $messageQueueModel
     * @param \I95DevConnect\CloudConnect\Model\Service $service
     * @param \I95DevConnect\I95DevServer\Api\I95DevServerRepositoryInterfaceFactory $i95DevRepository
     * @param \I95DevConnect\MessageQueue\Model\ErrorUpdateData $errorUpdateData
     * @param \I95DevConnect\I95DevServer\Model\ServiceMethod\AbstractServiceMethod $abstractServicemethod
     */
    public function __construct(
        \I95DevConnect\CloudConnect\Helper\Data $cloudHelper,
        \I95DevConnect\CloudConnect\Api\Data\RequestInterfaceFactory $requestInterface,
        Logger $logger,
        \I95DevConnect\MessageQueue\Api\I95DevErpMQRepositoryInterfaceFactory $messageQueueModel,
        \I95DevConnect\CloudConnect\Model\Service $service,
        \I95DevConnect\I95DevServer\Api\I95DevServerRepositoryInterfaceFactory $i95DevRepository,
        \I95DevConnect\MessageQueue\Model\ErrorUpdateData $errorUpdateData,
        \I95DevConnect\I95DevServer\Model\ServiceMethod\AbstractServiceMethod $abstractServicemethod,
        Manager $eventManager
    ) {
        $this->cloudHelper = $cloudHelper;
        $this->requestInterface = $requestInterface;
        $this->messageQueueModel = $messageQueueModel;
        $this->logger = $logger;
        $this->service = $service;
        $this->i95DevRepository = $i95DevRepository;
        $this->errorUpdateData = $errorUpdateData;
        $this->abstractServicemethod = $abstractServicemethod;
        $this->eventManager = $eventManager;
    }

    /**
     * Method to send response to cloud from Inbound MQ
     * @param object $request
     * @param string $entity
     * @param string $requestType
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sync($request, $entity, $requestType)
    {
        try {
            if ($this->cloudHelper->isEnabled()) {
                $mqCollectionData = $request->getRequestData();

                if (!empty($mqCollectionData)) {
                    $cloudResult = $this->sendReverseResponse($mqCollectionData, $requestType, $entity, $request);

                    if (isset($cloudResult->ResultData) && $cloudResult->ResultData) {
                        $cloudResult = $this->processResultData($cloudResult);

                        if ($cloudResult->requestData) {
                            $this->i95DevRepository->create()->serviceMethod(
                                'setMessageQueueResponseAckList',
                                json_encode($cloudResult),
                                $this->cloudHelper->getErpComponent()
                            );
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $this->logger->createLog(
                __METHOD__,
                $e->getMessage(),
                Logger::EXCEPTION,
                'critical'
            );
        }
    }

    /**
     * @param $cloudResult
     * @return mixed
     */
    public function processResultData($cloudResult)
    {
        foreach ($cloudResult->ResultData as $key => $requestData) {
            $cloudResult->requestData[$key] = $cloudResult->ResultData[$key];
            $cloudResult->requestData[$key]->messageId = $this->messageQueueModel->create()->load(
                $cloudResult->requestData[$key]->messageId,
                'destination_msg_id'
            )->getMsgId();
            unset($cloudResult->ResultData[$key]);
        }

        return $cloudResult;
    }

    /**
     * prepare response data
     * @param $record
     * @param $destinationMsgId
     * @param $entity
     * @return bool
     */
    public function prepareResponseData($record, $destinationMsgId, $entity)
    {
        $this->data = [];
        $this->data = $this->cloudHelper->prepareDataObject();
        if ($record['status'] == Data::SUCCESS) {
            $this->data->setSourceId($record[$this->magentoIdConst]);
            $this->data->setReference($record['ref_name']);
            $this->data->setTargetId($record[$this->targetIdConst]);
            $this->data->setMessageId($destinationMsgId);
        } else {
            $this->data->setSourceId('');
            $this->data->setReference($record['ref_name']);
            $this->data->setTargetId($record[$this->targetIdConst]);
            $this->data->setMessageId($destinationMsgId);
            $errorData = $this->errorUpdateData->load($record[$this->errorIdConst])->getData();
            if (isset($errorData['msg'])) {
                $this->data->setMessage(__($errorData['msg']));
            }
        }

        if ($entity == 'Customer') {
            $pendingAddress = $this->getMqAddressDataByParentId(
                Data::PENDING,
                $record[$this->msgIdConst]
            );
            if (count($pendingAddress) > 0) {
                return false;
            }
            $addresses = $this->getAddressResponse($record);
            if (!empty($addresses)) {
                $inputData['addresses'] = $addresses;
                $this->data->setInputData(
                    $this->abstractServicemethod->encryptAES(json_encode($inputData))
                );
            }
            $this->eventManager->dispatch(
                "after_prepare_mq_response",
                [
                    'currentObject' => $this,
                    'entity' => 'Customer',
                    'mqData' => $record
                ]
            );
        }
        return $this->data;
    }

    /**
     * @param $mqCollectionData
     * @param $requestType
     * @param $entity
     * @param $request
     * @return \I95DevConnect\CloudConnect\Model\type|Object|null
     * @return \I95DevConnect\CloudConnect\Model\type|Object|null
     */
    public function sendReverseResponse($mqCollectionData, $requestType, $entity, $request)
    {

        $totalrecord = count($mqCollectionData);
        for ($i = 0; $i <= $totalrecord; $i++) {

            if (isset($mqCollectionData[$i])) {
                $record = $mqCollectionData[$i];
                $destinationMsgId = (int)$record['destination_msg_id'];

                $data = $this->prepareResponseData($record, $destinationMsgId, $entity);

                if (!$data) {
                    continue;
                }

                $responseData[] = $data;
                $devResponse = $this->requestInterface->create();
                $packetSize = $this->cloudHelper->getPacketSize();
                $devResponse->setContext($request->Context);
                $devResponse->setPacketSize($packetSize);
                $devResponse->setRequestData($responseData);

                $cloudResult = $this->service->makeServiceCall(
                    $requestType,
                    $entity,
                    $devResponse,
                    $request->Context->SchedulerId
                );
            }
        }

        return $cloudResult;
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
        return $this->messageQueueModel->create()->getCollection()
            ->addFieldToSelect([$this->msgIdConst, $this->targetIdConst, $this->magentoIdConst, $this->errorIdConst])
            ->addFieldToFilter('entity_code', 'address')
            ->addFieldToFilter(
                'status',
                [
                    'in' => $status
                ]
            )
            ->addFieldToFilter('parent_msg_id', $parent_id)
            ->getData();
    }

    /**
     * Retrieve address responses which are not in pending state.
     *
     * @param array $record
     * @return array
     * @author Debashis S. Gopal
     */
    public function getAddressResponse($record)
    {
        $status = [Data::SUCCESS, Data::COMPLETE, Data::ERROR];
        $collectionArr = $this->getMqAddressDataByParentId($status, $record[$this->msgIdConst]);
        $addresses = [];

        foreach ($collectionArr as $address) {
            $msgAddress = '';
            if (isset($address[$this->errorIdConst])) {
                $errorData = $this->errorUpdateData->load($address[$this->errorIdConst]);

                $list = explode(",", $errorData->getMsg());
                if (!empty($list)) {
                    foreach ($list as $lt) {
                        if ($msgAddress != "") {
                            $msgAddress .= ", ";
                        }
                        $msgAddress .= __(trim($lt));
                    }
                }
            }
            $addresses[] = [
                'messageId' => $address[$this->msgIdConst],
                'targetId' => $address[$this->targetIdConst],
                'reference' => $record[$this->targetIdConst],
                'sourceId' => $address[$this->magentoIdConst],
                'entityName' => "address",
                'message' => $msgAddress
            ];
        }
        return $addresses;
    }
}
