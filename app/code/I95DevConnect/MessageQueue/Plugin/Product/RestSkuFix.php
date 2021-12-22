<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Plugin\Product;

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
     * To get the product details
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param string $sku
     * @param bool $editMode
     * @param int|null $storeId
     * @param bool $forceReload
     * @return array
     */
    public function beforeGet(
        \Magento\Catalog\Model\ProductRepository $productRepository,      //NOSONAR
        $sku,
        $editMode = false,
        $storeId = null,
        $forceReload = false
    ) {
        $product = $this->getProductBySku($sku);
        if (!empty($product)) {
            $sku = urldecode($product['sku']);
        } else {
            $sku = urldecode($sku);
        }
        return [$sku, $editMode, $storeId, $forceReload];
    }

    /**
     * To save the product SKU
     *
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param bool $saveOptions
     */
    public function beforeSave(
        \Magento\Catalog\Model\ProductRepository $productRepository,         //NOSONAR
        \Magento\Catalog\Api\Data\ProductInterface $product,
        $saveOptions = false //NOSONAR
    ) {
        $productModel = $this->getProductBySku($product->getSku());
        if (!empty($productModel)) {
            $product->setSku(urldecode($productModel['sku']));
        } else {
            $product->setSku(urldecode($product->getSku()));
        }
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
        return $itemData;
    }
}
