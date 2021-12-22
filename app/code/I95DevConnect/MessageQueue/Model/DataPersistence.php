<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model;

use \I95DevConnect\MessageQueue\Api\DataPersistenceInterface;

/**
 * Data Persistence model
 */
class DataPersistence extends AbstractDataPersistence implements DataPersistenceInterface
{

    public $syncMethodNotExists = "Sync Method Not Exists";
    public $dataPersistenceHelper;
    public $MQInterface;
    public $logger;
    public $syncModel;

    /**
     *
     * @param \I95DevConnect\MessageQueue\Helper\DataPersistence $dataPersistenceHelper
     * @param \I95DevConnect\MessageQueue\Api\Data\I95DevErpMQInterfaceFactory $MQInterface
     * @param \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger
     * @param \Magento\Framework\Json\Decoder $jsonDecoder
     * @param \I95DevConnect\MessageQueue\Api\I95DevResponseInterfaceFactory $i95DevResponse
     * @param \I95DevConnect\MessageQueue\Model\ErrorUpdateDataFactory $messageErrorModel
     * @param \I95DevConnect\MessageQueue\Api\Data\I95DevErpMQInterfaceFactory $i95DevErpMQ
     * @param \I95DevConnect\MessageQueue\Api\I95DevErpMQRepositoryInterfaceFactory $i95DevErpMQRepository
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Event\Manager $eventManager
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Validate $validate
     * @param \I95DevConnect\MessageQueue\Api\I95DevErpDataRepositoryInterfaceFactory $i95DevERPDataRepository
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Helper\DataPersistence $dataPersistenceHelper,
        \I95DevConnect\MessageQueue\Api\Data\I95DevErpMQInterfaceFactory $MQInterface,
        \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger,
        \Magento\Framework\Json\Decoder $jsonDecoder,
        \I95DevConnect\MessageQueue\Api\I95DevResponseInterfaceFactory $i95DevResponse,
        \I95DevConnect\MessageQueue\Model\ErrorUpdateDataFactory $messageErrorModel,
        \I95DevConnect\MessageQueue\Api\Data\I95DevErpMQInterfaceFactory $i95DevErpMQ,
        \I95DevConnect\MessageQueue\Api\I95DevErpMQRepositoryInterfaceFactory $i95DevErpMQRepository,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Event\Manager $eventManager,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Validate $validate,
        \I95DevConnect\MessageQueue\Api\I95DevErpDataRepositoryInterfaceFactory $i95DevERPDataRepository
    ) {
        $this->dataPersistenceHelper = $dataPersistenceHelper;
        $this->MQInterface = $MQInterface;
        $this->logger = $logger;
        parent::__construct(
            $jsonDecoder,
            $i95DevResponse,
            $messageErrorModel,
            $i95DevErpMQ,
            $logger,
            $i95DevErpMQRepository,
            $date,
            $eventManager,
            $validate,
            $i95DevERPDataRepository
        );
    }

    /**
     *
     * {@inheritdoc}
     */
    public function createEntity($entityCode, $dataString, $messageId, $erpCode = null)
    {
        try {
            if ($this->checkEntityExists($entityCode)) {
                $dataString = $this->getFormattedString($dataString);
                //@Hrusikesh Updates MQ status after decode json string
                $this->saveRecord($messageId);
                return $this->syncModel->create($dataString, $entityCode, $erpCode);
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->create()->createLog(
                __METHOD__,
                $ex->getMessage(),
                \I95DevConnect\MessageQueue\Api\LoggerInterface::I95EXC,
                \I95DevConnect\MessageQueue\Api\LoggerInterface::CRITICAL
            );

            return $this->setResponse(
                \I95DevConnect\MessageQueue\Helper\Data::ERROR,
                __($ex->getMessage()),
                null
            );
        }
    }

    /**
     *
     * {@inheritdoc}
     */
    public function getEntityInfo($entityCode, $dataString, $erpCode = null, $messageId = null)
    {
        try {
            if ($this->checkEntityExists($entityCode)) {
                return $this->syncModel->getInfo($dataString, $entityCode, $erpCode, $messageId);
            } else {
                    $this->logger->create()->createLog(
                        __METHOD__,
                        __($this->syncMethodNotExists),
                        \I95DevConnect\MessageQueue\Api\LoggerInterface::I95EXC,
                        \I95DevConnect\MessageQueue\Api\LoggerInterface::CRITICAL
                    );

                    throw new \Magento\Framework\Exception\LocalizedException(__($this->syncMethodNotExists));
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
    }

    /**
     *
     * {@inheritdoc}
     */
    public function getEntityResponse($entityCode, $dataString, $erpCode = null)
    {
        try {
            if ($this->checkEntityExists($entityCode)) {
                $dataString = $this->getFormattedString($dataString);
                return $this->syncModel->getResponse($dataString, $entityCode, $erpCode);
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            return $this->setResponse(
                \I95DevConnect\MessageQueue\Helper\Data::ERROR,
                __($ex->getMessage()),
                null
            );
        }
    }

    /**
     * Checks if the given entity code is exists or not
     * @param string $entityCode
     * @return boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function checkEntityExists($entityCode)
    {
        if (isset($this->dataPersistenceHelper->entityList[$entityCode])) {
            $this->setEntityCode($entityCode);
            $entityProperties = $this->dataPersistenceHelper->entityList[$entityCode];
            if (isset($entityProperties['syncdetails'])) {
                $syncdetails = $entityProperties['syncdetails'];
                $this->syncModel = $syncdetails['classObject'];

                return true;
            } else {
                $this->logger->create()->createLog(
                    __METHOD__,
                    __($this->syncMethodNotExists),
                    \I95DevConnect\MessageQueue\Api\LoggerInterface::I95EXC,
                    \I95DevConnect\MessageQueue\Api\LoggerInterface::CRITICAL
                );

                throw new \Magento\Framework\Exception\LocalizedException(__($this->syncMethodNotExists));
            }
        }
        return false;
    }

    /**
     * Method to update status of Inbound MQ record
     * @param type $messageId
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveRecord($messageId)
    {
        try {
            $i95DevErpMQ = $this->MQInterface->create();
            $i95DevErpMQ->setMsgId($messageId);
            $i95DevErpMQ->setStatus(\I95DevConnect\MessageQueue\Helper\Data::PROCESSING);
            $this->i95DevErpMQRepository->create()->saveMQData($i95DevErpMQ);
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $message = $ex->getMessage();
            throw new \Magento\Framework\Exception\LocalizedException(__($message));
        }
    }
}
