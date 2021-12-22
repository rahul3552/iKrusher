<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Helper;

use \I95DevConnect\MessageQueue\Model\I95DevErpDataRepositoryFactory;
use Magento\Framework\Xml\Parser;
use \Magento\Framework\Module\Dir\Reader;
use \I95DevConnect\MessageQueue\Api\LoggerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Customer\Api\GroupRepositoryInterface;

if (!defined('DS')) {
    DEFINE('DS', DIRECTORY_SEPARATOR);
}

/**
 * Helper Class for Message Queue module
 */
class AbstractData extends \Magento\Framework\App\Helper\AbstractHelper
{

    const COMPLETE = 5;
    const SUCCESS = 4;
    const ERROR = 3;
    const PROCESSING = 2;
    const PENDING = 1;
    const RETRY_LIMIT = 5;
    const MAGLOGNAME = 'MagentoToERP';
    const ERPLOGNAME = 'ERPToMagento';
    const I95EXC = 'i95devApiException';
    const CLEAN = 'cleanMQData';
    const CUSTOM_IDENTIFIER = 'i95dev_extension';
    const TARGET_KEY = 'targetId';
    const REF_KEY = 'reference';
    const SYNCED = 'synced';
    const MODULE_NAME = 'I95DevConnect_MessageQueue';
    const MQ_XML = 'messagequeue.xml';
    const COMPONENT = 'i95dev_messagequeue/I95DevConnect_settings/component';
    const MQDATA_CLEAN_DAYS = 30;
    const TARGET_CUSTOMER_ID = 'target_customer_id';
    const MSG_ID = 'msg_id';
    const ERROR_ID = 'error_id';
    const ENTITY_CODE = 'entity_code';
    const ENTITY_NAME = 'entity_name';
    const MATRIXPRODUCT = 'matrixproduct';
    const ORIGIN = 'origin';

    /**
     * @var I95DevErpDataRepositoryFactory
     */
    public $modelEntityUpdateDataFactory;

    /**
     *
     * @var Parser $parser
     */
    public $parser;

    /**
     *
     * @var Reader $reader
     */
    public $reader;
    public $entityCode;
    public $storeManager;

    /**
     *
     * @var Helper $helper
     */
    public $helper;

    /**
     * @var MsgResponseEntityInterface $msgResponse
     */
    public $msgResponse;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    public $date;

    /**
     * @var  $msgReport
     */
    public $msgReport;

    /**
     * var $errorModel
     */
    public $errorModel;
    public $entityList;
    public $entityTypeModel;
    public $erpMessageQueue;
    public $scopeConfig;
    public $coreRegistry;
    public $regionModel;
    public $moduleManager;
    public $magentoMessageQueue;
    public $logger;
    public $isI95DevRestReq = ['isI95DevRestReq' => 'true'];
    public $resultPageFactory;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    public $urlInterface;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    public $_redirect;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    public $searchCriteriaBuilder;

    /**
     *
     * @var GroupRepositoryInterface
     */
    public $groupRepository;

    /**
     *
     * @param I95DevErpDataRepositoryFactory $modelEntityUpdateDataFactory
     * @param Parser $parser
     * @param Reader $reader
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \I95DevConnect\MessageQueue\Model\ErrorUpdateDataFactory $errorModel
     * @param PageFactory $resultPageFactory
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param \I95DevConnect\MessageQueue\Model\EntityFactory $entityTypeModel
     * @param \I95DevConnect\MessageQueue\Model\I95DevErpMQRepositoryFactory $erpMessageQueue
     * @param \I95DevConnect\MessageQueue\Model\I95DevMagentoMQRepositoryFactory $magentoMessageQueue
     * @param \Magento\Directory\Model\Region $regionModel
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param LoggerInterface $logger
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     * @param \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Registry $coreRegistry
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param GroupRepositoryInterface $groupRepository
     * @param Context $context
     * @param type $entityList
     */
    public function __construct(
        I95DevErpDataRepositoryFactory $modelEntityUpdateDataFactory,
        Parser $parser,
        Reader $reader,
        PageFactory $resultPageFactory,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \I95DevConnect\MessageQueue\Model\ErrorUpdateDataFactory $errorModel,
        \I95DevConnect\MessageQueue\Model\EntityFactory $entityTypeModel,
        \I95DevConnect\MessageQueue\Model\I95DevErpMQRepositoryFactory $erpMessageQueue,
        \I95DevConnect\MessageQueue\Model\I95DevMagentoMQRepositoryFactory $magentoMessageQueue,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Directory\Model\RegionFactory $regionModel,
        \Magento\Framework\Module\Manager $moduleManager,
        \I95DevConnect\MessageQueue\Api\LoggerInterface $logger,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        GroupRepositoryInterface $groupRepository,
        Context $context,
        $entityList = null
    ) {
        $this->modelEntityUpdateDataFactory = $modelEntityUpdateDataFactory;
        $this->parser = $parser;
        $this->reader = $reader;
        $this->date = $date;
        $this->errorModel = $errorModel;
        $this->entityList = $entityList;
        $this->entityTypeModel = $entityTypeModel;
        $this->erpMessageQueue = $erpMessageQueue;
        $this->scopeConfig = $scopeConfig;
        $this->coreRegistry = $coreRegistry;
        $this->regionModel = $regionModel;
        $this->moduleManager = $moduleManager;
        $this->magentoMessageQueue = $magentoMessageQueue;
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->urlInterface = $urlInterface;
        $this->_redirect = $redirect;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->groupRepository = $groupRepository;

        parent::__construct($context);
    }

    /**
     * Check whether the I95DevConnect_MessageQueue is enabled or not
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->scopeConfig->getValue(
            'i95dev_messagequeue/i95dev_extns/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $this->storeManager->getDefaultStoreView()->getWebsiteId()
        );
    }

    /**
     * Reads the xml data from the given xml file
     * @param string $dir
     * @param string $module
     * @param string $xml
     * @param string $node
     * @return array
     */
    public function readXml($dir, $module, $xml, $node)
    {
        $xml_data = [];
        try {
            $path = $this->reader->getModuleDir($dir, $module);
            $xmlPath = $path . DS . $xml;
            $xml_data = $this->parser->load($xmlPath)->xmlToArray();

        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->createLog(__METHOD__, $e->getMessage(), 'xmlerror', LoggerInterface::CRITICAL);
        }
        return $xml_data[$node];
    }

    /**
     * Get config entities data of messagequeue.xml
     * @return array
     */
    public function getConfigModels()
    {
        $xml_data = $this->readXml('etc', self::MODULE_NAME, self::MQ_XML, 'config');
        $configEntities = (array) $xml_data['entities'];
        foreach ($configEntities as $key => $val) {
            if ($val === 0) {
                unset($configEntities[$key]);
            }
        }
        return $configEntities;
    }

    /**
     * Get sync order
     * @return array
     */
    public function _getSyncOrder()
    {
        $xml_data = $this->readXml('etc', self::MODULE_NAME, self::MQ_XML, 'config');
        return (array) $xml_data['syncorder'];
    }

    /**
     * Get sync entities
     * @return array
     */
    public function getSyncEntities()
    {
        $canSyncEntity = [];
        $syncOrder = $this->_getSyncOrder();
        asort($syncOrder);
        $allOrderedEntites = array_keys($syncOrder);
        $availableModels = $this->getConfigModels();
        foreach ($allOrderedEntites as $entity) {
            if (array_key_exists($entity, $availableModels)) {
                $canSyncEntity[] = $entity;
            }
        }

        return $canSyncEntity;
    }

    /**
     * Get entity type list
     * @return array
     */
    public function getEntityTypeList()
    {
        $entityType = [];
        // @updatedBy Arushi Bansal
        $component = $this->getComponent();
        $collectionList = $this->entityTypeModel->create()->getCollection()
            ->setOrder(self::ENTITY_NAME, 'ASC');
        if ($component == "GP") {
            $entity = [self::MATRIXPRODUCT];
            $collectionList->addFieldToFilter(self::ENTITY_CODE, ['nin' => $entity]);
        }
        if ($collectionList->getSize() > 0) {
            foreach ($collectionList as $collection) {
                $entityType[$collection->getEntityCode()] = $collection->getEntityName();
            }
        }
        return $entityType;
    }

    /**
     * @param $supportedFor
     * @return array
     */
    public function getEntityTypeListMq($supportedFor)
    {
        $entityType = [];
        // @updatedBy Arushi Bansal
        $component = $this->getComponent();
        // @updatedBy Arushi Bansal
        $collectionList = $this->entityTypeModel->create()->getCollection()
            ->addFieldToFilter($supportedFor, true)
            ->setOrder(self::ENTITY_NAME, 'ASC');
        if ($component == "GP") {
            $entity = [self::MATRIXPRODUCT];
            $collectionList->addFieldToFilter(self::ENTITY_CODE, ['nin' => $entity]);
        }
        //@author Divya Koona. Added to exclude Customer Group entity IBMQ for NAV
        if ($component == "NAV") {
            $entity = ['CustomerGroup'];
            $collectionList->addFieldToFilter(self::ENTITY_CODE, ['nin' => $entity]);
        }
        if ($collectionList->getSize() > 0) {
            foreach ($collectionList as $collection) {
                $entityType[$collection->getEntityCode()] = $collection->getEntityName();
            }
        }
        return $entityType;
    }
    /**
     * Get Entity type inbound list
     * @return array
     */
    public function getEntityTypeInboundList()
    {
        return $this->getEntityTypeListMq("support_for_inbound");
    }

    /**
     * Get entity type outbound list
     * @return array
     */
    public function getEntityTypeOutboundList()
    {
        return $this->getEntityTypeListMq("support_for_outbound");
    }

    /**
     * Get Entity type list by sync order
     * @return array
     */
    public function getEntityTypeListBySyncOrder()
    {
        $entityType = [];
        // @updatedBy Arushi Bansal
        $component = $this->getComponent();
        $collectionList = $this->entityTypeModel->create()->getCollection()
            ->setOrder('sort_order', 'ASC');
        if ($component == "GP") {
            $entity = [self::MATRIXPRODUCT];
            $collectionList->addFieldToFilter(self::ENTITY_CODE, ['nin' => $entity]);
        }
        if ($collectionList->getSize() > 0) {
            foreach ($collectionList as $collection) {
                $entityType[$collection->getEntityCode()] = $collection->getEntityName();
            }
        }
        return $entityType;
    }

    /**
     * get Scope Config Object
     *
     * @param string $value
     * @param string $scope
     * @param null $id
     *
     * @return string
     */
    public function getscopeConfig($value, $scope, $id = null)
    {
        if ($id === null) {
            $id = $this->storeManager->getDefaultStoreView()->getWebsiteId();
        }
        return $this->scopeConfig->getValue($value, $scope, $id);
    }

    /**
     * Get global value
     * @param string $key
     * @return \Magento\Framework\Registry $coreRegistry
     */
    public function getGlobalValue($key)
    {
        $globalKey = self::CUSTOM_IDENTIFIER . '_' . $key;
        return $this->coreRegistry->registry($globalKey);
    }

    /**
     * To set global value
     * @param string $key
     * @param string $value
     */
    public function setGlobalValue($key, $value)
    {
        $globalKey = self::CUSTOM_IDENTIFIER . '_' . $key;
        $this->coreRegistry->register($globalKey, $value);
    }

    /**
     * Unset the global value
     * @param string $key
     */
    public function unsetGlobalValue($key)
    {
        $globalKey = self::CUSTOM_IDENTIFIER . '_' . $key;
        $this->coreRegistry->unregister($globalKey);
    }
}
