<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PriceLevel
 */

namespace I95DevConnect\PriceLevel\Model\DataPersistence;

use \I95DevConnect\TierPrice\Model\DataPersistence\TierPrice;
use \I95DevConnect\MessageQueue\Helper\Data;
use \I95DevConnect\MessageQueue\Model\DataPersistence\Customer\CustomerGroup\Create as CustomerGroupCreate;
use \Magento\Catalog\Api\TierPriceStorageInterfaceFactory;
use \Magento\Catalog\Api\Data\TierPriceInterfaceFactory;
use \I95DevConnect\MessageQueue\Model\DataPersistence\Product\AbstractProduct;
use \I95DevConnect\MessageQueue\Model\AbstractDataPersistence;
use \I95DevConnect\MessageQueue\Api\LoggerInterface;
use \Magento\Framework\Api\SearchCriteriaBuilder;
use \Magento\Customer\Api\GroupRepositoryInterface;

/**
 * This class over rides \I95DevConnect\TierPrice\Model\DataPersistence\TierPrice,
 * It syncs erp tier prices to i9dev_tier_rices.(ERP Specific)
 * @updatedBy Debashis S. Gopal
 */
class PriceLevelTierPrice extends TierPrice
{
    /**
     *
     * @var \I95DevConnect\PriceLevel\Model\ItemPriceListDataFactory
     */
    public $i95devTierPriceFactory;

    /**
     *
     * @var \I95DevConnect\PriceLevel\Model\DataPersistence\PriceLevel
     */
    public $priceLevelCreate;

    /**
     *
     * @var string
     */
    public $sku;

    /**
     *
     * @var int
     */
    public $productId;

    /**
     *
     * @var obj
     */
    public $tierPrice;

    /**
     * PriceLevelTierPrice constructor.
     * @param Data $dataHelper
     * @param CustomerGroupCreate $customerGroupCreate
     * @param TierPriceStorageInterfaceFactory $tierPriceStorage
     * @param TierPriceInterfaceFactory $tierPriceFactory
     * @param AbstractProduct $abstractProduct
     * @param AbstractDataPersistence $abstractDataPersistence
     * @param LoggerInterface $logger
     * @param \I95DevConnect\PriceLevel\Model\ItemPriceListDataFactory $i95devTierPriceFactory
     * @param PriceLevel $priceLevelCreate
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param GroupRepositoryInterface $groupRepository
     */
    public function __construct(
        Data $dataHelper,
        CustomerGroupCreate $customerGroupCreate,
        TierPriceStorageInterfaceFactory $tierPriceStorage,
        TierPriceInterfaceFactory $tierPriceFactory,
        AbstractProduct $abstractProduct,
        AbstractDataPersistence $abstractDataPersistence,
        LoggerInterface $logger,
        \I95DevConnect\PriceLevel\Model\ItemPriceListDataFactory $i95devTierPriceFactory,
        \I95DevConnect\PriceLevel\Model\DataPersistence\PriceLevel $priceLevelCreate,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        GroupRepositoryInterface $groupRepository
    ) {
        $this->i95devTierPriceFactory = $i95devTierPriceFactory;
        $this->priceLevelCreate = $priceLevelCreate;
        parent::__construct(
            $dataHelper,
            $customerGroupCreate,
            $tierPriceStorage,
            $tierPriceFactory,
            $abstractProduct,
            $abstractDataPersistence,
            $logger,
            $searchCriteriaBuilder,
            $groupRepository
        );
    }

    /**
     * @param string $stringData
     * @return \I95DevConnect\MessageQueue\Api\I95DevResponseInterface
     * @throws \Exception
     */
    public function create($stringData)
    {
        try {
            $this->stringData = $stringData;
            $this->validateData();
            $tiers = $this->dataHelper->getValueFromArray("tierPrices", $this->stringData);
            $existingIds = [];
            foreach ($tiers as $tierData) {
                $qty = $this->dataHelper->getValueFromArray("minQty", $tierData);
                if (!$qty || $qty === 0) {
                    $qty = 1;
                }

                if (isset($qty)) {
                    $this->saveTierPrice($tierData, $qty);

                    $existingIds[] = $this->tierPrice->getId();
                } else {
                    return $this->abstractDataPersistence->setResponse(
                        Data::ERROR,
                        "minQty should not be Empty",
                        $this->productId
                    );
                }
            }
            $needToDeleteCollection = $this->i95devTierPriceFactory->create()->getCollection()
                ->addFieldToFilter('sku', $this->sku)
                ->addFieldToFilter('id', ["nin" => $existingIds]);
            if ($needToDeleteCollection->getSize() > 0) {
                foreach ($needToDeleteCollection as $singleRecord) {
                    $singleRecord->delete();
                }
            }
            return $this->abstractDataPersistence->setResponse(
                Data::SUCCESS,
                "Record Successfully Synced",
                $this->productId
            );
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            return $this->abstractDataPersistence->setResponse(
                Data::ERROR,
                __($ex->getMessage()),
                null
            );
        }
    }

    /**
     *
     * {@inheritdoc}
     */
    public function validateData()
    {
        if (!$this->dataHelper->isEnabled()) {
            throw new \Magento\Framework\Exception\LocalizedException(__("i95dev_gen_001"));
        }
        $this->sku = $this->dataHelper->getValueFromArray("targetId", $this->stringData);
        $this->productId = $this->abstractProduct->getProductPrimaryId($this->sku);

        if ((int)$this->productId < 1) {
            throw new \Magento\Framework\Exception\LocalizedException(__('i95dev_prod_016'));
        }
        $tiers = $this->dataHelper->getValueFromArray("tierPrices", $this->stringData);
        if (!empty($tiers)) {
            $this->validatePriceLevel($tiers);
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(__("tier_price_01"));
        }
    }

    /**
     * Checks price level is exists in i95dev_price_level table, If not create a new price level.
     *
     * @createdBy Debashis S. Gopal
     *
     * @param array $tiers
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validatePriceLevel($tiers)
    {
        foreach ($tiers as $tierData) {
            $targetPriceLevelKey = $this->dataHelper->getValueFromArray("priceLevelKey", $tierData);
            if (!isset($targetPriceLevelKey) || $targetPriceLevelKey == '') {
                throw new \Magento\Framework\Exception\LocalizedException(__("tier_price_02"));
            }
            $priceLevel = $this->priceLevelCreate->getPriceLevelData($targetPriceLevelKey);
            if (empty($priceLevel)) {
                $priceLevelData = [
                    'targetPricelevel' => $targetPriceLevelKey,
                    'priceLevelDescription' => $targetPriceLevelKey
                ];
                $result = $this->priceLevelCreate->create($priceLevelData, 'PriceLevel');
                if (!$result->resultData) {
                    $this->logger->createLog(
                        __METHOD__,
                        "Error Occured while creating new Prive Level",
                        LoggerInterface::I95EXC,
                        'critical'
                    );
                }
            }
        }
    }

    /**
     * @param $tierData
     * @param $qty
     * @throws \Exception
     */
    public function saveTierPrice($tierData, $qty)
    {
        $price = $this->dataHelper->getValueFromArray("price", $tierData);
        $priceLevelCode = $this->dataHelper->getValueFromArray("priceLevelKey", $tierData);
        $originalFromDate = $this->dataHelper->getValueFromArray("fromDate", $tierData);
        if (isset($originalFromDate)) {
            $fromDate = date("Y-m-d", strtotime($originalFromDate));
        } else {
            $fromDate = null;
        }
        $originalToDate = $this->dataHelper->getValueFromArray("toDate", $tierData);
        if (isset($originalToDate)) {
            $toDate = date("Y-m-d", strtotime($originalToDate));
        } else {
            $toDate = null;
        }
        $existingTierPriceCollectionList = $this->i95devTierPriceFactory->create()->getCollection()
            ->addFieldToFilter('sku', $this->sku)
            ->addFieldToFilter('qty', $qty)
            ->addFieldToFilter('pricelevel', $priceLevelCode);
        if ($fromDate === null) {
            $existingTierPriceCollectionList->addFieldToFilter('from_date', ['null' => true]);
        } else {
            $existingTierPriceCollectionList->addFieldToFilter('from_date', $fromDate);
        }

        if ($fromDate === null) {
            $existingTierPriceCollectionList->addFieldToFilter('to_date', ['null' => true]);
        } else {
            $existingTierPriceCollectionList->addFieldToFilter('to_date', $toDate);
        }

        if ($existingTierPriceCollectionList->getSize() > 0) {
            foreach ($existingTierPriceCollectionList as $existingTierPriceCollection) {
                $this->tierPrice = $this->i95devTierPriceFactory
                    ->create()
                    ->load($existingTierPriceCollection->getId());
            }
        } else {
            $this->tierPrice = $this->i95devTierPriceFactory->create();
        }
        $this->tierPrice->setData("sku", $this->sku);
        $this->tierPrice->setData("qty", $qty);
        $this->tierPrice->setData("price", $price);
        $this->tierPrice->setData("pricelevel", $priceLevelCode);
        $this->tierPrice->setData("from_date", $fromDate);
        $this->tierPrice->setData("to_date", $toDate);
        $this->tierPrice->save();
    }
}
