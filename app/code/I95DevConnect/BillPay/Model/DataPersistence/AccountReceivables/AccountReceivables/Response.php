<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Model\DataPersistence\AccountReceivables\AccountReceivables;

class Response
{

    const LOG_INFO = 'i95devARResponse';
    const I95_OBSERVER_SKIP = 'i95_observer_skip';

    /**
     *
     * @var \I95DevConnect\MessageQueue\Helper\ServiceRequest
     */
    public $requestHelper;

    /**
     *
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $dataHelper;

    /**
     *
     * @var \Magento\Framework\Event\Manager
     */
    public $eventManager;

    /**
     *
     * @var \I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterfaceFactory
     */
    public $i95DevMagentoMQRepository;

    /**
     *
     * @var \I95DevConnect\MessageQueue\Api\Data\I95DevMagentoMQInterfaceFactory
     */
    public $i95DevMagentoMQData;

    public $statusCode = '5';

    /**
     *
     * @var String|null
     */
    public $updatedBy = 'ERP';

    /**
     *
     * @var int
     */
    public $productId;

    /**
     *
     * @var String|null
     */
    public $erpCode;

    /**
     *
     * @var String|null
     */
    public $targetId;

    /**
     *
     * @var String|null
     */
    public $entityCode;

    /**
     *
     * @var array
     */
    public $postData = [];

    /**
     * @var \I95DevConnect\BillPay\Model\ArPaymentFactory
     */
    protected $arPayment;

    public $abstractDataPersistence;

    /**
     *
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     * @param \I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterfaceFactory $i95DevMagentoMQRepository
     * @param \I95DevConnect\MessageQueue\Api\Data\I95DevMagentoMQInterfaceFactory $i95DevMagentoMQData
     * @param \I95DevConnect\MessageQueue\Helper\ServiceRequest $requestHelper
     * @param \Magento\Framework\Event\Manager $eventManager
     * @param \I95DevConnect\BillPay\Model\ArPaymentFactory $arPayment
     * @param \I95DevConnect\MessageQueue\Model\AbstractDataPersistence $abstractDataPersistence
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper,
        \I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterfaceFactory $i95DevMagentoMQRepository,
        \I95DevConnect\MessageQueue\Api\Data\I95DevMagentoMQInterfaceFactory $i95DevMagentoMQData,
        \I95DevConnect\MessageQueue\Helper\ServiceRequest $requestHelper,
        \Magento\Framework\Event\Manager $eventManager,
        \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger,
        \I95DevConnect\BillPay\Model\ArPaymentFactory $arPayment,
        \I95DevConnect\MessageQueue\Model\AbstractDataPersistence $abstractDataPersistence
    ) {
        $this->dataHelper = $dataHelper;
        $this->i95DevMagentoMQRepository = $i95DevMagentoMQRepository;
        $this->i95DevMagentoMQData = $i95DevMagentoMQData;
        $this->requestHelper = $requestHelper;
        $this->eventManager = $eventManager;
        $this->logger = $logger;
        $this->arPayment = $arPayment;
        $this->abstractDataPersistence = $abstractDataPersistence;
    }

    /**
     *
     * @param type $requestData
     * @param type $entityCode
     * @param type $erpCode
     * @return Sting|boolean
     */
    public function setResponse($requestData, $erpCode)
    {
        try {
            if ($erpCode) {
                $this->updatedBy = $erpCode;
            }
            $this->paymentId = $this->dataHelper->getValueFromArray("sourceId", $requestData);
            $this->messageId = $this->dataHelper->getValueFromArray("messageId", $requestData);
            $this->requestData = $requestData;
            $this->validateData();
            $this->targetId = $this->dataHelper->getValueFromArray("targetId", $requestData);
            $this->logger->create()->createLog(
                __METHOD__,
                $this->targetId,
                self::LOG_INFO,
                'info'
            );

            if ($this->targetId != '') {
                $this->saveDataInOutboundMQ();
                $this->dataHelper->unsetGlobalValue(self::I95_OBSERVER_SKIP);
                $this->dataHelper->setGlobalValue(self::I95_OBSERVER_SKIP, true);

                $this->arPaymentData->setCashReceiptNumber($this->targetId);
                $this->logger->create()->createLog(
                    __METHOD__,
                    "Sync Success",
                    self::LOG_INFO,
                    'info'
                );
                $this->arPaymentData->setTargetSyncStatus('1');
                $this->arPaymentData->save();
                $this->dataHelper->unsetGlobalValue(self::I95_OBSERVER_SKIP);

                $response['inputData'] = [];
                $response['id'] = $this->arPaymentData->getId();
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
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->create()->createLog(
                __METHOD__,
                $e->getMessage(),
                \I95DevConnect\MessageQueue\Api\LoggerInterface::I95EXC,
                'critical'
            );
            $message = $e->getMessage();
            throw new \Magento\Framework\Exception\LocalizedException(__($message));
        }
    }

    public function validateData()
    {
        try {
            $this->arPaymentData = $this->arPayment->create()->load($this->paymentId);
            if (!$this->arPaymentData->getId()) {
                $message = "Account Receivables with payment id ::" . $this->paymentId. " does not exists";
                throw new \Magento\Framework\Exception\LocalizedException(__($message));
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $message = $ex->getMessage();
            throw new \Magento\Framework\Exception\LocalizedException(__($message));
        }
    }

    public function saveDataInOutboundMQ()
    {
        try {
            $i95DevMagentoMQDataModel = $this->i95DevMagentoMQData->create();
            $i95DevMagentoMQDataModel->setMsgId($this->messageId);
            $i95DevMagentoMQDataModel->setStatus($this->statusCode);
            $i95DevMagentoMQDataModel->setUpdatedby($this->updatedBy);
            $i95DevMagentoMQDataModel->setTargetId($this->targetId);
            $this->i95DevMagentoMQRepository->create()->saveMQData($i95DevMagentoMQDataModel);
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
    }
}
