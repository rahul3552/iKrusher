<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Controller\Adminhtml\MessageQueue;

use Magento\Backend\App\Action\Context;
use \Magento\Framework\Controller\ResultFactory;

/**
 * Controller for mass syncing of message queue records
 */
class MassSync extends \Magento\Backend\App\Action
{
    const MASSACTION_PREPARE_KEY='massaction_prepare_key';

    public $messageManager;
    public $abstractDataPersistence;
    public $logger;
    public $i95DevErpMQRepository;
    public $dataPersistence;
    public $i95DevErpMQFactory;
    public $statusCode = 2;

    /**
     *
     * @param Context $context
     * @param \I95DevConnect\MessageQueue\Model\AbstractDataPersistence $abstractDataPersistence
     * @param \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger
     * @param \I95DevConnect\MessageQueue\Api\I95DevErpMQRepositoryInterfaceFactory $i95DevErpMQRepository
     * @param \I95DevConnect\MessageQueue\Api\DataPersistenceInterfaceFactory $dataPersistence
     * @param \I95DevConnect\MessageQueue\Api\Data\I95DevErpMQInterfaceFactory $i95DevErpMQFactory
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     */
    public function __construct(
        Context $context,
        \I95DevConnect\MessageQueue\Model\AbstractDataPersistence $abstractDataPersistence,
        \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger,
        \I95DevConnect\MessageQueue\Api\I95DevErpMQRepositoryInterfaceFactory $i95DevErpMQRepository,
        \I95DevConnect\MessageQueue\Api\DataPersistenceInterfaceFactory $dataPersistence,
        \I95DevConnect\MessageQueue\Api\Data\I95DevErpMQInterfaceFactory $i95DevErpMQFactory,
        \Magento\Framework\Controller\ResultFactory $resultFactory
    ) {
        parent::__construct($context);
        $this->messageManager = $context->getMessageManager();
        $this->abstractDataPersistence = $abstractDataPersistence;
        $this->logger = $logger;
        $this->i95DevErpMQRepository = $i95DevErpMQRepository;
        $this->dataPersistence = $dataPersistence;
        $this->i95DevErpMQFactory = $i95DevErpMQFactory;
        $this->resultFactory = $resultFactory;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('I95DevConnect_MessageQueue::report');
    }

    /**
     * Sync records
     *
     * @return \Magento\Backend\Model\View\Result\Page
     * @throws \Exception
     */
    public function execute()
    {
        $msgId = null;
        try {
            $selectedIds = [];
            $data = $this->getRequest()->getPostValue();
            if (isset($data[self::MASSACTION_PREPARE_KEY]) && !empty($data[$data[self::MASSACTION_PREPARE_KEY]])) {
                $selectedIds = $data[$data[self::MASSACTION_PREPARE_KEY]];
                sort($selectedIds);
                foreach ($selectedIds as $msgId) {
                    $messageQueue = $this->i95DevErpMQRepository->create()->get($msgId);
                    if ($messageQueue->getMsgId() && $messageQueue->getDataString()
                        && $messageQueue->getStatus() != \I95DevConnect\MessageQueue\Helper\Data::PROCESSING) {
                        // @Hrusikesh removed save message queue status to processing
                        //@Hrusikesh Added MessageId as extra parameter
                        $response = $this->dataPersistence->create()->createEntity(
                            $messageQueue->getEntityCode(),
                            $messageQueue->getDataString(),
                            $messageQueue->getMsgId(),
                            null
                        );

                        if (isset($response)) {
                            $this->abstractDataPersistence->updateErpMQStatus(
                                $response->getStatus(),
                                $response->getResultdata(),
                                $response->getMessage(),
                                $msgId
                            );
                        } else {
                            $this->abstractDataPersistence->updateErpMQStatus(
                                \I95DevConnect\MessageQueue\Helper\Data::ERROR,
                                null,
                                'Some issue occur while saving data. Please contact admin.',
                                $msgId
                            );
                        }
                    }
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->create()->createLog(
                __METHOD__,
                $e->getMessage(),
                \I95DevConnect\MessageQueue\Api\LoggerInterface::I95EXC,
                'critical'
            );
            $this->abstractDataPersistence->updateErpMQStatus(
                \I95DevConnect\MessageQueue\Helper\Data::ERROR,
                null,
                $e->getMessage(),
                $msgId
            );
        }
        $this->messageManager->addSuccess(
            "Please check the Updated status for selected IDS :- ".implode(",", $selectedIds)
        );

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }
}
