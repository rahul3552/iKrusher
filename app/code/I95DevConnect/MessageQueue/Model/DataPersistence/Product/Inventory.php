<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace I95DevConnect\MessageQueue\Model\DataPersistence\Product;

use \I95DevConnect\MessageQueue\Model\AbstractDataPersistence;

/**
 * Description of Inventory
 */
class Inventory
{
    public $stockItemRepo;
    public $productStock;
    public $abstractProduct;
    public $abstractDataPersistence;
    public $dataHelper;
    public $validate;
    public $eventManager;
    public $stockRegistry;

    public $validateFields = [
        'sku' => 'i95dev_prod_005',
        'qty' => 'i95dev_prod_020',
    ];

    public $stringData;

    public $productId;

    public $sku;

    /**
     * Inventory constructor.
     *
     * @param \Magento\CatalogInventory\Api\StockItemRepositoryInterfaceFactory $stockItemRepo
     * @param Product\Reverse\Stock $productStock
     * @param AbstractProduct $abstractProduct
     * @param \Magento\Framework\Event\Manager $eventManager
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Validate $validate
     * @param AbstractDataPersistence $abstractDataPersistence
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     */
    public function __construct(
        \Magento\CatalogInventory\Api\StockItemRepositoryInterfaceFactory $stockItemRepo,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Product\Product\Reverse\Stock $productStock,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Product\AbstractProduct $abstractProduct,
        \Magento\Framework\Event\Manager $eventManager,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Validate $validate,
        AbstractDataPersistence $abstractDataPersistence,
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
    ) {
        $this->stockItemRepo = $stockItemRepo;
        $this->productStock = $productStock;
        $this->abstractProduct = $abstractProduct;
        $this->abstractDataPersistence = $abstractDataPersistence;
        $this->dataHelper = $dataHelper;
        $this->validate = $validate;
        $this->eventManager = $eventManager;
        $this->stockRegistry = $stockRegistry;
    }
    /**
     * @param $stringData
     * @param $entityCode
     *
     * @return \I95DevConnect\MessageQueue\Api\I95DevResponseInterface
     */
    public function create($stringData, $entityCode)
    {
        $this->stringData = $stringData;

        try {
            $this->sku = $this->dataHelper->getValueFromArray("sku", $this->stringData);
            $this->validate->validateFields = $this->validateFields;
            $this->validateData();

            $stockItem = $this->productStock->setStockInformation(
                $this->stringData,
                true
            );

            $stockItem = $this->stockRegistry->updateStockItemBySku($this->sku, $stockItem);
            if (!(is_numeric($stockItem))) {
                throw \Magento\Framework\Exception\LocalizedException(__("inventory_sync_error"));
            }

            $this->dataHelper->unsetGlobalValue('i95_observer_skip');
            $aftereventname = 'erpconnect_messagequeuetomagento_aftersave_' . $entityCode;
            $this->eventManager->dispatch($aftereventname, ['currentObject' => $this]);

            return $this->abstractDataPersistence->setResponse(
                \I95DevConnect\MessageQueue\Helper\Data::SUCCESS,
                "Record Successfully Synced",
                $this->productId
            );
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            return $this->abstractDataPersistence->setResponse(
                \I95DevConnect\MessageQueue\Helper\Data::ERROR,
                $ex->getMessage(),
                null
            );
        }
    }

    public function validateData()
    {
        $this->validate->validateData($this->stringData);
        $this->productId = $this->abstractProduct->getProductPrimaryId($this->sku);

        if ($this->productId < 1) {
            $message = "Sku not Exists::" . $this->sku;
            throw new \Magento\Framework\Exception\LocalizedException(__($message));
        }
    }
}
