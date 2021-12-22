<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Plugin\Product\CatalogInventory;

use \Magento\Framework\Api\SearchCriteriaBuilder;
use \Magento\Catalog\Model\ProductRepository;

/**
 * Fix for RestSku
 */
class RestSkuFix
{
    /**
     *
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    public $searchCriteriaBuilder;

    /**
     *
     * @var \Magento\Catalog\Model\ProductRepository
     */
    public $productRepository;

    /**
     *
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ProductRepository $productRepository
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ProductRepository $productRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->productRepository = $productRepository;
    }

    /**
     * To get the stock items by SKU
     *
     * @param \Magento\CatalogInventory\Model\StockRegistry $stockRegistry
     * @param string $productSku
     * @param int $scopeId
     *
     * @return array
     */
    public function beforeGetStockItemBySku(
        \Magento\CatalogInventory\Model\StockRegistry $stockRegistry,            //NOSONAR
        string $productSku,
        int $scopeId = null
    ) {
        return [$this->getProductBySku($productSku), $scopeId];
    }

    /**
     * To get stock status by SKU
     * @param \Magento\CatalogInventory\Model\StockRegistry $stockRegistry
     * @param string $productSku
     * @param int $scopeId
     * @return array
     */
    public function beforeGetStockStatusBySku(
        \Magento\CatalogInventory\Model\StockRegistry $stockRegistry,   //NOSONAR
        $productSku,
        $scopeId = null
    ) {
        return [$this->getProductBySku($productSku), $scopeId];
    }

    /**
     * To get Product stock status by SKU
     * @param \Magento\CatalogInventory\Model\StockRegistry $stockRegistry
     * @param string $productSku
     * @param int $scopeId
     * @return array
     */
    public function beforeGetProductStockStatusBySku(
        \Magento\CatalogInventory\Model\StockRegistry $stockRegistry,     //NOSONAR
        $productSku,
        $scopeId = null
    ) {
        return [$this->getProductBySku($productSku), $scopeId];
    }

    /**
     * Update stock item by SKU
     * @param \Magento\CatalogInventory\Model\StockRegistry $stockRegistry
     * @param string $productSku
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem
     * @return array
     */
    public function beforeUpdateStockItemBySku(
        \Magento\CatalogInventory\Model\StockRegistry $stockRegistry,    //NOSONAR
        $productSku,
        \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem
    ) {
        return [$this->getProductBySku($productSku), $stockItem];
    }

    /**
     * To get Product by SKU
     * @param string $sku
     * @return array
     * @updatedBy Debashis S. Gopal. Use of repository interface instead of api call.
     */
    public function getProductBySku($sku)
    {
        $itemData = [];
        $searchCriteria = $this->searchCriteriaBuilder
                                ->addFilter('sku', $sku, 'eq')
                                ->create();
        $searchResults = $this->productRepository->getList($searchCriteria);
        $itemInfo = $searchResults->getItems();
        if (!empty($itemInfo) && isset($itemInfo[0])) {
            $itemData = $itemInfo[0];
        }

        if (!empty($itemData)) {
            $productSku = urldecode($itemData['sku']);
        } else {
            $productSku = urldecode($sku);
        }
        return $productSku;
    }
}
