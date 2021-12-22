<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PriceLevel
 */

namespace I95DevConnect\PriceLevel\Model\DataPersistence;

use \I95DevConnect\MessageQueue\Helper\Data;
use I95DevConnect\PriceLevel\Helper\Data as PriceLevelHelper;
use I95DevConnect\MessageQueue\Model\AbstractDataPersistence;

/**
 * Class for syncing Price Level
 * @updatedBy Debashis S. Gopal
 */
class PriceLevel extends AbstractDataPersistence
{
    /**
     *
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $dataHelper;

    /**
     *
     * @var \I95DevConnect\PriceLevel\Model\PriceLevelDataFactory
     */
    public $priceLevelDataFactory;

    /**
     *
     * @var \I95DevConnect\PriceLevel\Model\PriceLevelData
     */
    public $priceLevel = null;
    
    /**
     *
     * @var PriceLevelHelper
     */
    public $priceLevelHelper;
    
    /**
     *
     * @var array
     */
    public $validateFields = [
        'priceLevelDescription' => 'i95dev_pricelevel_001'
    ];

    /**
     *
     * @param Data $dataHelper
     * @param \I95DevConnect\PriceLevel\Model\PriceLevelDataFactory $priceLevelDataFactory
     * @param \Magento\Framework\Json\Decoder $jsonDecoder
     * @param \I95DevConnect\MessageQueue\Api\I95DevResponseInterfaceFactory $i95DevResponse
     * @param \I95DevConnect\MessageQueue\Model\ErrorUpdateDataFactory $messageErrorModel
     * @param \I95DevConnect\MessageQueue\Api\Data\I95DevErpMQInterfaceFactory $i95DevErpMQ
     * @param \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger
     * @param \I95DevConnect\MessageQueue\Api\I95DevErpMQRepositoryInterfaceFactory $i95DevErpMQRepository
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Event\Manager $eventManager
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Validate $validate
     * @param \I95DevConnect\MessageQueue\Api\I95DevErpDataRepositoryInterfaceFactory $i95DevERPDataRepository
     * @param PriceLevelHelper $priceLevelHelper
     */
    public function __construct(
        Data $dataHelper,
        \I95DevConnect\PriceLevel\Model\PriceLevelDataFactory $priceLevelDataFactory,
        \Magento\Framework\Json\Decoder $jsonDecoder,
        \I95DevConnect\MessageQueue\Api\I95DevResponseInterfaceFactory $i95DevResponse,
        \I95DevConnect\MessageQueue\Model\ErrorUpdateDataFactory $messageErrorModel,
        \I95DevConnect\MessageQueue\Api\Data\I95DevErpMQInterfaceFactory $i95DevErpMQ,
        \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger,
        \I95DevConnect\MessageQueue\Api\I95DevErpMQRepositoryInterfaceFactory $i95DevErpMQRepository,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Event\Manager $eventManager,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Validate $validate,
        \I95DevConnect\MessageQueue\Api\I95DevErpDataRepositoryInterfaceFactory $i95DevERPDataRepository,
        PriceLevelHelper $priceLevelHelper
    ) {
        $this->dataHelper = $dataHelper;
        $this->priceLevelDataFactory = $priceLevelDataFactory;
        $this->priceLevelHelper = $priceLevelHelper;
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
     * Create Price Level
     *
     * @param string $stringData
     * @param string $entityCode
     *
     * @return \I95DevConnect\MessageQueue\Api\I95DevResponseInterface
     * @throws \Exception
     */
    public function create($stringData, $entityCode)
    {
        if (!$this->priceLevelHelper->isEnabled()) {
            return $this->setResponse(
                \I95DevConnect\MessageQueue\Helper\Data::ERROR,
                __("I95DevConnect Price Group extension is currently disabled. Enable the extension to proceed sync."),
                null
            );
        }
        
        $this->setStringData($stringData);
        try {
            $this->validate->validateFields = $this->validateFields;
            $this->validate->validateData($this->stringData);
            $priceLevelCode = $this->dataHelper->getValueFromArray("targetId", $this->stringData);
            $priceLevelData = $this->getPriceLevelData($priceLevelCode);
            if (!empty($priceLevelData)) {
                $pricelevelId = $priceLevelData[0]['pricelevel_id'];
                $this->priceLevel = $this->priceLevelDataFactory->create()->load($pricelevelId);
            } else {
                $this->priceLevel = $this->priceLevelDataFactory->create();
            }
            $this->priceLevel->setData(
                "pricelevel_code",
                $this->dataHelper->getValueFromArray("targetId", $this->stringData)
            );
            $this->priceLevel->setData(
                "description",
                $this->dataHelper->getValueFromArray("priceLevelDescription", $this->stringData)
            );
            $beforeeventname = 'i95dev_messagequeuetomagento_beforesave_' . $this->getEntityCode();
            $this->eventManager->dispatch($beforeeventname, ['currentObject' => $this]);
            $this->priceLevel->save();
            $aftereventname = 'i95dev_messagequeuetomagento_aftersave_' . $this->getEntityCode();
            $this->eventManager->dispatch($aftereventname, ['currentObject' => $this]);

            return $this->setResponse(
                Data::SUCCESS,
                "Record Successfully Synced",
                $this->priceLevel->getId()
            );
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            return $this->setResponse(
                Data::ERROR,
                __($entityCode." :: ".$ex->getMessage()),
                null
            );
        }
    }

    /**
     * Retrieve i95dev price level based on price level code
     *
     * @param string $priceLevelCode
     * @return array|null
     */
    public function getPriceLevelData($priceLevelCode)
    {
        $existingPriceLevelCollectionList = $this->priceLevelDataFactory->create()
                        ->getCollection()
                        ->addFieldToFilter(
                            'pricelevel_code',
                            $priceLevelCode
                        );
        if ($existingPriceLevelCollectionList->getSize() > 0) {
            return $existingPriceLevelCollectionList->getData();
        } else {
            return null;
        }
    }
}
