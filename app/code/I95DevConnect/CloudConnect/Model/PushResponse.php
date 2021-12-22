<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_CloudConnect
 */

namespace I95DevConnect\CloudConnect\Model;

use \I95DevConnect\CloudConnect\Api\PushResponseInterface;
use \I95DevConnect\MessageQueue\Helper\Data;

/**
 * class to push the response from magento to cloud
 */
class PushResponse extends AbstractAgentCron implements PushResponseInterface
{
    /**
     * @var string
     */
    public $schedulerType = 'PushResponse';

    /**
     * @var ServiceMethod\ServiceMethodFactory
     */
    public $serviceMethod;

    /**
     * @var \I95DevConnect\I95DevServer\Model\ServiceMethod\ForwardSync\MQToErp\SendIds
     */
    public $sendIds;

    /**
     * @var string
     */
    public $logFilename = 'PushResponse';

    /**
     * Constructor for DI
     * @param \I95DevConnect\CloudConnect\Model\LoggerFactory $logger
     * @param \I95DevConnect\CloudConnect\Model\RequestFactory $request
     * @param \I95DevConnect\CloudConnect\Model\Service $service
     * @param \I95DevConnect\CloudConnect\Model\ServiceMethod\ServiceMethodFactory $serviceMethod
     * @param \I95DevConnect\CloudConnect\Helper\Data $cloudHelper
     * @param \I95DevConnect\MessageQueue\Model\ReadCustomXml $readCustomXml
     * @param \I95DevConnect\CloudConnect\Helper\ConfigHelper $configHelper
     * @param \I95DevConnect\I95DevServer\Model\ServiceMethod\ForwardSync\MQToErp\SendIds $sendIds
     * @param \I95DevConnect\CloudConnect\Api\Data\RequestInterfaceFactory $requestInterface
     * @param \I95DevConnect\MessageQueue\Helper\Data $mqHelper
     * @param \I95DevConnect\MessageQueue\Api\I95DevErpMQRepositoryInterfaceFactory $messageQueueModel
     */
    public function __construct(
        \I95DevConnect\CloudConnect\Model\LoggerFactory $logger,
        \I95DevConnect\CloudConnect\Model\RequestFactory $request,
        \I95DevConnect\CloudConnect\Model\Service $service,
        \I95DevConnect\CloudConnect\Model\ServiceMethod\ServiceMethodFactory $serviceMethod,
        \I95DevConnect\CloudConnect\Helper\Data $cloudHelper,
        \I95DevConnect\MessageQueue\Model\ReadCustomXml $readCustomXml,
        \I95DevConnect\CloudConnect\Helper\ConfigHelper $configHelper,
        \I95DevConnect\I95DevServer\Model\ServiceMethod\ForwardSync\MQToErp\SendIds $sendIds,
        \I95DevConnect\CloudConnect\Api\Data\RequestInterfaceFactory $requestInterface,
        \I95DevConnect\MessageQueue\Helper\Data $mqHelper,
        \I95DevConnect\MessageQueue\Api\I95DevErpMQRepositoryInterfaceFactory $messageQueueModel
    ) {
        $this->logger = $logger;
        $this->request = $request;
        $this->service = $service;
        $this->cloudHelper = $cloudHelper;
        $this->readCustomXml = $readCustomXml;
        $this->serviceMethod = $serviceMethod;
        $this->configHelper = $configHelper;
        $this->sendIds = $sendIds;
        $this->requestInterface = $requestInterface;
        $this->mqHelper = $mqHelper;
        $this->messageQueueModel = $messageQueueModel;
    }

    /**
     * {@inheritDoc}
     */
    public function syncResponse()
    {
        $this->startCronProcess();
    }

    /**
     * Method to initiate job
     * @param $schedulerId
     * @param $schedulerData
     */
    protected function initiateJob($schedulerId, $schedulerData)
    {
        try {
            $this->processDataToCloud($schedulerId);
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->create()->createLog(
                'PushDataCron',
                $ex->getMessage(),
                \I95DevConnect\CloudConnect\Model\Logger::EXCEPTION,
                'critical'
            );
        }
    }

    /**
     * @param $schedulerId
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function processDataToCloud($schedulerId)
    {
        $entities = $this->readCustomXml->getXmlDataOrderBySyncOrder();
        $reverseSkipEntities = $this->configHelper->getReverseSkipEntities();

        foreach ($entities as $entity_code => $entity_value) {

            if (!in_array($entity_code, $reverseSkipEntities)) {
                $mqCollection = $this->messageQueueModel->create()->getCollection();
                $mqCollection->getSelect()->where(
                    '(status = ' . Data::SUCCESS . ') '
                    . ' OR (status = ' . Data::ERROR
                    . ' AND counter < 5)'
                );
                $mqCollection->addFieldToFilter('entity_code', $entity_code);
                $mqCollection->getSelect()->order('msg_id', 'ASC');

                if (!empty($mqCollection) && $mqCollection->getSize() > 0) {
                    $packetSize = $this->cloudHelper->getPacketSize();
                    $packets = array_chunk($mqCollection->getData(), $packetSize);
                    foreach ($packets as $packetdata) {

                        $request = $this->requestInterface->create();
                        $request->setContext(
                            $this->request->create()->prepareContextObject(
                                $this->schedulerType,
                                $schedulerId
                            )
                        );
                        $request->setRequestData($packetdata);

                        $this->serviceMethod->create()->cloudConnect(
                            $request,
                            $entity_code,
                            $this->schedulerType,
                            'reverseResponse'
                        );
                    }
                }
            }
        }
    }
}
