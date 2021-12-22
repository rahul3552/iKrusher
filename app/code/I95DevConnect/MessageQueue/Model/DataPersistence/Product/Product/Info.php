<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model\DataPersistence\Product\Product;

/**
 * Class which Send Product Info to ERP.
 * @updatedBy Debashis S. Gopal. Changed Api code to Interface
 */
class Info
{
    const WEIGTH = "weight";
    /**
     *
     * @var \Magento\Framework\Event\Manager
     */
    public $eventManager;
    
    /**
     *
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $dataHelper;
    
    /**
     *
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    public $productRepository;
    
    /**
     *
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    public $stockRegistry;
    
    /**
     *
     * @var \Magento\Tax\Api\TaxClassRepositoryInterface
     */
    public $taxClassRepository;
    
    /**
     *
     * @var array
     * @updatedBy Debashis S. Gopal. Missing price field added.
     */
    public $fieldMapInfo = [
        'reference' => 'name',
        'name' => 'name',
        'sku' => 'sku',
        'typeId' => 'type_id',
        'createdAt' => 'created_at',
    ];
    
    /**
     *
     * @var \Magento\Catalog\Api\Data\ProductInterface
     */
    public $productInfo;
    
    /**
     *
     * @var array
     */
    public $productData;
    const STORE_ID = 0;
    /**
     *
     * @param \Magento\Framework\Event\Manager $eventManager
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\Tax\Api\TaxClassRepositoryInterface $taxClassRepository
     */
    public function __construct(
        \Magento\Framework\Event\Manager $eventManager,
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Tax\Api\TaxClassRepositoryInterface $taxClassRepository
    ) {
        $this->eventManager = $eventManager;
        $this->dataHelper = $dataHelper;
        $this->productRepository = $productRepository;
        $this->stockRegistry = $stockRegistry;
        $this->taxClassRepository = $taxClassRepository;
    }

    /**
     * Retrieves product based on product id
     *
     * @param int $productId
     * @return array
     */
    public function getInfo($productId)
    {
        $this->validateData($productId);
        $this->prepareErpData();
        $productInfoEvent = "erpconnect_forward_productinfo";
        $this->eventManager->dispatch($productInfoEvent, ['currentObject' => $this]);
        return $this->productData;
    }

    /**
     * Validate product exists or not and if exist initialize $this->productInfo
     *
     * @param int $productId
     * $createdBy Debashis S. Gopal
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validateData($productId)
    {
        try {
            // @ Updated by Hrusikesh Manna Set Store Id 0 to get product information by id
            $this->productInfo = $this->productRepository->getById($productId, false, self::STORE_ID);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $ex) {
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
    }
    
    /**
     * Prepare product data array for ERP.
     *
     * @createdBy Debashis S. Gopal
     */
    public function prepareErpData()
    {
        $this->productData = $this->dataHelper->prepareInfoArray($this->fieldMapInfo, $this->productInfo->__toArray());
        $this->productData['price'] = (float)$this->productInfo['price'];
        $this->productData[self::WEIGTH] = isset($this->productInfo[self::WEIGTH]) ?
            (float)$this->productInfo[self::WEIGTH] : 0;
        $description = $this->productInfo->getCustomAttribute('description');
        $shortDescription = $this->productInfo->getCustomAttribute('short_description');
        $cost = $this->productInfo->getCustomAttribute('cost');
        /** @updatedBy Debashis S. Gopal. Checking of taxClassId > 0 added. **/
        $taxClass = $this->productInfo->getCustomAttribute('tax_class_id');
        $this->productData['taxClassId'] = 0;
        if ($taxClass && $taxClass->getValue() > 0) {
            $this->productData['taxClassId'] = (int)$taxClass->getValue();
        }
        /** @updatedBy kavya.k. sending 0 instead of null if product has no cost . **/
        $this->productData['cost'] = isset($cost) ? $cost->getValue() : 0;
        $this->productData['description'] = isset($description) ?
                rtrim(str_replace("&nbsp;", "", strip_tags($description->getValue()))) : '';
        $this->productData['shortDescription'] = isset($shortDescription) ?
                rtrim(str_replace("&nbsp;", "", strip_tags($shortDescription->getValue()))) : '';
        $stockData = $this->stockRegistry->getStockItemBySku(urlencode($this->productInfo->getSku()));
        if (!empty($stockData)) {
            $this->productData['backorders'] = $stockData['backorders'];
        }
    }
}
