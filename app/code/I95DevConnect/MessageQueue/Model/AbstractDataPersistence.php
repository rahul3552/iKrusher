<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model;

/**
 * Generic class for data persistance.
 */
class AbstractDataPersistence
{
    public $entityCode;
    public $jsonDecoder;
    public $stringData;
    public $i95DevResponse;
    public $messageErrorModel;
    public $i95DevErpMQFactory;
    public $logger;
    public $i95DevErpMQRepository;
    public $date;
    public $eventManager;
    public $validate;
    public $i95DevERPDataRepository;

    public $erpCode;
    public $statusCode;
    public $updatedBy;

    /**
     *
     * @param \Magento\Framework\Json\Decoder $jsonDecoder
     * @param \I95DevConnect\MessageQueue\Api\I95DevResponseInterfaceFactory $i95DevResponse
     * @param \I95DevConnect\MessageQueue\Model\ErrorUpdateDataFactory $messageErrorModel
     * @param \I95DevConnect\MessageQueue\Api\Data\I95DevErpMQInterfaceFactory $i95DevErpMQFactory
     * @param \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger
     * @param \I95DevConnect\MessageQueue\Api\I95DevErpMQRepositoryInterfaceFactory $i95DevErpMQRepository
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Event\Manager $eventManager
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Validate $validate
     * @param \I95DevConnect\MessageQueue\Api\I95DevErpDataRepositoryInterfaceFactory $i95DevERPDataRepository
     */
    public function __construct(
        \Magento\Framework\Json\Decoder $jsonDecoder,
        \I95DevConnect\MessageQueue\Api\I95DevResponseInterfaceFactory $i95DevResponse,
        \I95DevConnect\MessageQueue\Model\ErrorUpdateDataFactory $messageErrorModel,
        \I95DevConnect\MessageQueue\Api\Data\I95DevErpMQInterfaceFactory $i95DevErpMQFactory,
        \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger,
        \I95DevConnect\MessageQueue\Api\I95DevErpMQRepositoryInterfaceFactory $i95DevErpMQRepository,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Event\Manager $eventManager,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Validate $validate,
        \I95DevConnect\MessageQueue\Api\I95DevErpDataRepositoryInterfaceFactory $i95DevERPDataRepository
    ) {
        $this->jsonDecoder = $jsonDecoder;
        $this->i95DevResponse = $i95DevResponse;
        $this->messageErrorModel = $messageErrorModel;
        $this->i95DevErpMQFactory = $i95DevErpMQFactory;
        $this->logger = $logger;
        $this->i95DevErpMQRepository = $i95DevErpMQRepository;
        $this->date = $date;
        $this->eventManager = $eventManager;
        $this->validate = $validate;
        $this->i95DevERPDataRepository = $i95DevERPDataRepository;
    }

    /**
     * set entity code
     * @param string $entityCode
     */
    public function setEntityCode($entityCode)
    {
        $this->entityCode = $entityCode;
    }

    /**
     * get entity code
     * @return string
     */
    public function getEntityCode()
    {
        return $this->entityCode;
    }

    /**
     *
     * @param string $dataString
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFormattedString($dataString)
    {
        //Hrusikesh Added Try Catch Block
        try {
            if (!$dataString) {
                throw new \Magento\Framework\Exception\LocalizedException(__("Data string required is empty"));
            }
            
                return $this->jsonDecoder->decode($dataString);
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->create()->createLog(
                __METHOD__,
                $ex->getMessage(),
                \I95DevConnect\MessageQueue\Api\LoggerInterface::I95EXC,
                'critical'
            );
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
    }

     /**
      * set stringData
      * @param type $stringData
      */
    public function setStringData($stringData)
    {
        $this->stringData = $stringData;
    }

    /**
     * set response in responseInterface
     * @param string $status
     * @param string $message
     * @param string $resultData
     * @return \I95DevConnect\MessageQueue\Api\I95DevResponseInterface
     */
    public function setResponse($status, $message = null, $resultData = null)
    {
        $i95DevResponse = $this->i95DevResponse->create();
        $i95DevResponse->setStatus($status);
        $i95DevResponse->setMessage($message);
        $i95DevResponse->setResultdata($resultData);

        return $i95DevResponse;
    }

    /**
     * update Message queue table status
     * @param $status
     * @param $data
     * @param $message
     * @param int $msgId
     * @throws \Exception
     */
    public function updateErpMQStatus($status, $data, $message, $msgId)
    {
        $messageQueue = $this->i95DevErpMQRepository->create()->get($msgId);
        if ($messageQueue->getMsgId()) {
            $counter = $messageQueue->getCounter();
            $i95DevErpMQ = $this->i95DevErpMQFactory->create();
            $i95DevErpMQ->setMsgId($messageQueue->getMsgId());
            $i95DevErpMQ->setUpdatedDt($this->date->gmtDate());

            if (($status == \I95DevConnect\MessageQueue\Helper\Data::SUCCESS) ||
                ($status == \I95DevConnect\MessageQueue\Helper\Data::COMPLETE)) {
                $i95DevErpMQ->setStatus($status);
                $i95DevErpMQ->setMagentoId($data);
                $errorLog = $this->messageErrorModel->create();
                $errorLog->load($messageQueue->getErrorId());
                $errorLog->delete();
                $i95DevErpMQ->setErrorId(0);
                $this->i95DevERPDataRepository->create()->deleteMQData($messageQueue->getDataId());
                $this->logger->create()->createLog(
                    __METHOD__,
                    "updated message queue with msg id - $msgId status as : SUCCESS",
                    \I95DevConnect\MessageQueue\Api\LoggerInterface::MSGLOGNAME,
                    'success'
                );
            } else {
                $i95DevErpMQ->setStatus(\I95DevConnect\MessageQueue\Helper\Data::ERROR);
                $this->logger->create()->createLog(
                    __METHOD__,
                    $message,
                    \I95DevConnect\MessageQueue\Api\LoggerInterface::MSGLOGNAME,
                    'error'
                );
                $i95DevErpMQ->setErrorId($this->updateErrorData($message));
            }

            $i95DevErpMQ->setCounter($counter+1);
            $this->i95DevErpMQRepository->create()->saveMQData($i95DevErpMQ);
        }
    }

    /**
     * Updates error data in error report table
     *
     * @param string $message
     * @return int
     * @throws \Exception
     */
    public function updateErrorData($message)
    {
        $errorId = 0;
        if ($message) {
            $message = is_array($message) ? implode(",", $message) : $message;
            try {
                $errorDataModel = $this->messageErrorModel->create();
                $errorDataModel->setMsg($message);
                $errorDataModel->save();
                $errorId = $errorDataModel->getId();
            } catch (\Magento\Framework\Exception\LocalizedException $ex) {
                $this->logger->create()->createLog(
                    __METHOD__,
                    $ex->getMessage(),
                    \I95DevConnect\MessageQueue\Api\LoggerInterface::I95EXC,
                    'critical'
                );
            }
        }
        return $errorId;
    }

    /**
     * Sets erp code
     *
     * @param string $erpCode
     * @return $this
     */
    public function setErpCode($erpCode)
    {
        $this->erpCode = $erpCode;
        return $this;
    }

    /**
     * Returns ERP code
     *
     * @return string
     */
    public function getErpCode()
    {
        return $this->erpCode;
    }

    /**
     * Sets status code
     *
     * @param string $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * Return status code
     *
     * @return string
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * set updated by
     *
     * @param string $updatedBy
     * @return $this
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;
        return $this;
    }

    /**
     * Returns updated by
     *
     * @return string
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }
}
