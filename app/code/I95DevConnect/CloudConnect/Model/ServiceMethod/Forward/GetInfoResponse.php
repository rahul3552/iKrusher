<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_CloudConnect
 */

namespace I95Devconnect\CloudConnect\Model\ServiceMethod\Forward;

use I95Devconnect\CloudConnect\Model\Logger;
use I95DevConnect\MessageQueue\Api\Data\I95DevMagentoMQInterfaceFactory;
use I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterfaceFactory;
use I95DevConnect\MessageQueue\Helper\Data;
use I95DevConnect\MessageQueue\Model\ErrorUpdateData;

/**
 * Class to get Response Information
 */
class GetInfoResponse
{

    public $cloudHelper;
    public $erpName = 'ERP';
    public $logger;
    public $jsonHelper;
    public $messageErrorModel;
    public $magentoMQData;
    public $i95DevMagentoMQ;

    /**
     * Constructor for DI
     * @param \I95DevConnect\CloudConnect\Helper\Data $cloudHelper
     * @param I95DevMagentoMQRepositoryInterfaceFactory $i95DevMagentoMQ
     * @param I95DevMagentoMQInterfaceFactory $magentoMQData
     * @param ErrorUpdateData $messageErrorModel
     * @param Logger $logger
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     */
    public function __construct(
        \I95DevConnect\CloudConnect\Helper\Data $cloudHelper,
        I95DevMagentoMQRepositoryInterfaceFactory $i95DevMagentoMQ,
        I95DevMagentoMQInterfaceFactory $magentoMQData,
        ErrorUpdateData $messageErrorModel,
        Logger $logger,
        \Magento\Framework\Json\Helper\Data $jsonHelper
    ) {
        $this->cloudHelper = $cloudHelper;
        $this->i95DevMagentoMQ = $i95DevMagentoMQ;
        $this->magentoMQData = $magentoMQData;
        $this->messageErrorModel = $messageErrorModel;
        $this->logger = $logger;
        $this->jsonHelper = $jsonHelper;
    }

    /**
     * forward cloud msg id sync implementation
     * @param object $request
     * @param string $entity
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sync($request, $entity)
    {
        try {
            if (is_array($request) && !empty($request)) {
                foreach ($request as $record) {
                    if ($record['result'] && $record['sourceId']) {
                        $sourceId = $record['sourceId'];
                        $mqRecordCollection = $this->i95DevMagentoMQ->create()->getCollection();
                        $mqRecordCollection->addFieldToSelect('msg_id')
                        ->addFieldToFilter("entity_code", $entity)
                        ->addFieldToFilter("erp_code", $this->cloudHelper->getErpComponent())
                        ->addFieldToFilter("magento_id", $sourceId)
                        ->addFieldToFilter("status", ["in" => [Data::SUCCESS, Data::ERROR]])
                        ->setOrder('msg_id', 'DESC');

                        $mqRecordCollection->getSelect()->limit(1);
                        
                        $msgId =  $mqRecordCollection->getFirstItem()->getMsgId();
                        
                        if (empty($record['message'])) {
                            $i95DevMagentoMQObj = $this->i95DevMagentoMQ->create();
                            $magentoMQDataObj = $this->magentoMQData->create();
                            $magentoMQDataObj->setMsgId($msgId);
                            $magentoMQDataObj->setDestinationMsgId($record['messageId']);
                            $i95DevMagentoMQObj->saveMQData($magentoMQDataObj);
                        } else {
                            $errorDataModel = $this->messageErrorModel->create();
                            $errorDataModel->setMsg($record['message']);
                            $errorDataModel->save();
                            $errorId = $errorDataModel->getId();
                            $i95DevMagentoMQObj = $this->i95DevMagentoMQ->create();
                            $magentoMQDataObj = $this->magentoMQData->create();
                            $magentoMQDataObj->setMsgId($sourceId);
                            $magentoMQDataObj->setErrorId($errorId);
                            $magentoMQDataObj->setDestinationMsgId($msgId);
                            $i95DevMagentoMQObj->saveMQData($magentoMQDataObj);
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
        return true;
    }
}
