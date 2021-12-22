<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Observer\OutboundObserver;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\Http;

/**
 * Base Observer class
 */
abstract class BaseObserver implements ObserverInterface
{

    public $dataHelper;
    public $baseHelperData;
    public $magentoMessageQueue;
    private $magentoId;
    private $entityCode;
    private $updatedBy = 'Magento';
    private $statusCode = '1';
    public $observerRouting;
    public $erpCode="ERP";
    public $dataObject;
    public $i95DevMagentoMQRepo;

    /**
     *
     * @var array
     */
    private $sourceData;

    public $logger;
    public $request;
    public $helperConfig;

    public $i95DevMagentoMQFactory;

    public $generic;
    public $transactions;
    public $scopeConfig;
    public $storeManager;
    public $entityTypeModel;
    
    /**
     * @var \Magento\Framework\Event\Manager
     */
    public $eventManager;
    public $additionalInfo = "";

    /**
     * BaseObserver constructor.
     *
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     * @param \I95DevConnect\MessageQueue\Api\Data\I95DevMagentoMQInterfaceFactory $i95DevMagentoMQFactory
     * @param \I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterfaceFactory $i95DevMagentoMQRepo
     * @param \I95DevConnect\MessageQueue\Model\Logger $logger
     * @param \I95DevConnect\MessageQueue\Helper\Generic $generic
     * @param \Magento\Sales\Api\Data\TransactionSearchResultInterfaceFactory $transactions
     * @param Http $request
     * @param \I95DevConnect\MessageQueue\Helper\Config $helperConfig
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \I95DevConnect\MessageQueue\Model\EntityFactory $entityTypeModel
     * @param \Magento\Framework\Event\Manager $eventManager
     * @param null $observerRouting
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper,
        \I95DevConnect\MessageQueue\Api\Data\I95DevMagentoMQInterfaceFactory $i95DevMagentoMQFactory,
        \I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterfaceFactory $i95DevMagentoMQRepo,
        \I95DevConnect\MessageQueue\Model\Logger $logger,
        \I95DevConnect\MessageQueue\Helper\Generic $generic,
        \Magento\Sales\Api\Data\TransactionSearchResultInterfaceFactory $transactions,
        Http $request,
        \I95DevConnect\MessageQueue\Helper\Config $helperConfig,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \I95DevConnect\MessageQueue\Model\EntityFactory $entityTypeModel,
        \Magento\Framework\Event\Manager $eventManager,
        $observerRouting = null
    ) {
        $this->dataHelper = $dataHelper;
        $this->i95DevMagentoMQFactory = $i95DevMagentoMQFactory;
        $this->i95DevMagentoMQRepo = $i95DevMagentoMQRepo;
        $this->observerRouting = $observerRouting;
        $this->logger = $logger;
        $this->request = $request;
        $this->generic = $generic;
        $this->transactions = $transactions;
        $this->helperConfig = $helperConfig;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->entityTypeModel = $entityTypeModel;
        $this->eventManager = $eventManager;
    }

    /**
     * Get Magento id
     * @return string
     */
    public function getMagentoId()
    {
        return $this->magentoId;
    }

    /**
     * Get entity code
     * @return string
     */
    public function getEntityCode()
    {
        return $this->entityCode;
    }

    /**
     * Set entity code
     * @param string $entityCode
     */
    public function setEntitycode($entityCode)
    {
        $this->entityCode = $entityCode;
    }

    /**
     * Set dataObject
     * @param obj $dataObject
     */
    public function setDataObject($dataObject)
    {
        $this->dataObject = $dataObject;
    }

    /**
     * To get the dataObject
     * @return obj
     */
    public function getDataObject()
    {
        return $this->dataObject;
    }

    /**
     * To set the Magento Id
     * @param string $magentoId
     */
    public function setMagentoId($magentoId)
    {
        $this->magentoId = $magentoId;
    }

    /**
     * Set the source data
     * @param string $sourceData
     */
    public function setSourceData($sourceData)
    {
        $this->sourceData = $sourceData;
    }

    /**
     * Get the source data
     */
    public function getSourceData()
    {
        return $this->sourceData;
    }
    
    /**
     * get additional info
     *
     * @return null|string
     */
    public function getAdditionalInfo()
    {
        return $this->additionalInfo;
    }

    /**
     * set additional info
     *
     * @param null|string $additionalInfo
     * @return void
     */
    public function setAdditionalInfo($additionalInfo)
    {
        $this->additionalInfo = $additionalInfo;
    }

    /**
     * To save record
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveRecord()
    {
        try {
            $mageId = $this->getMagentoId();
            $entity_code = $this->getEntityCode();
            if ($this->validateRecord() === false) {
                return;
            }
            $i95DevMagentoMQ = $this->i95DevMagentoMQFactory->create();
            $this->erpCode = $this->helperConfig->getConfigValues()->getData('component');
            $i95DevMagentoMQ->setErpCode(__($this->erpCode));
            $i95DevMagentoMQ->setEntitycode($entity_code);
            $i95DevMagentoMQ->setMagentoId($mageId);
            $i95DevMagentoMQ->setStatus($this->statusCode);
            $i95DevMagentoMQ->setUpdatedBy($this->updatedBy);
            $additional = $this->getAdditionalInfo();
            if ($additional) {
                $i95DevMagentoMQ->setAdditionalInfo($additional);
            }
            $this->i95DevMagentoMQRepo->create()->saveMQData($i95DevMagentoMQ);
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $message = $ex->getMessage();
            throw new \Magento\Framework\Exception\LocalizedException(__($message));
        }
    }

    /**
     * get the values from array
     * @param array $array
     * @param string $key
     * @return string
     */
    public function getValueFromArray($array, $key)
    {
        $value = null;
        if (is_array($array) && isset($array[$key])) {
            $value = $array[$key];
        }
        return $value;
    }

    /**
     * check method exist or not
     * @param obj $classObject
     * @param string $methodName
     * @return boolean
     */
    public function methodExists($classObject, $methodName)
    {
        if (is_object($classObject) && method_exists($classObject, $methodName)) {
            return true;
        }
        return false;
    }

    /**
     * Validate record
     * @return boolean
     */
    public function validateRecord()
    {
        if ($this->entityCode == "product") {
            $productType = $this->dataObject->getTypeId();
            if ($productType === "configurable") {
                return false;
            }
        }
        return true;
    }
}
