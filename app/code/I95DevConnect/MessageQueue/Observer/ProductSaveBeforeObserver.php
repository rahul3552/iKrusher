<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\Http;

/**
 * Observer class to save product
 */
class ProductSaveBeforeObserver implements ObserverInterface
{

    const MAGLOGNAME = 'MagentoToERP';
    const ERPLOGNAME = 'ERPToMagento';
    const I95EXC = 'i95devApiException';

    /**
     * Product save processed flag code
     */
    const PRODUCT_SAVE_FLAG = 'product_save_processed';

    /**
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $data;

    /**
     * @var Registry
     */
    public $coreRegistry;

    /**
     * @var ItemFactory
     */
    public $itemFactory;
    public $productFactory;

    /**
     * @var \I95DevConnect\MessageQueue\Helper\Generic
     */
    public $generic;
    public $request;

    public $stockData;

    public $msgHelper;

    /**
     * ProductSaveBeforeObserver constructor.
     *
     * @param \I95DevConnect\MessageQueue\Helper\Data $data
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Catalog\Model\ProductFactory $itemFactory
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockData
     * @param \Magento\Catalog\Model\Product $productFactory
     * @param \I95DevConnect\MessageQueue\Helper\Data $msgHelper
     * @param Http $request
     * @param \I95DevConnect\MessageQueue\Helper\Generic $generic
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Helper\Data $data,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Model\ProductFactory $itemFactory,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockData,
        \Magento\Catalog\Model\Product $productFactory,
        \I95DevConnect\MessageQueue\Helper\Data $msgHelper,
        Http $request,
        \I95DevConnect\MessageQueue\Helper\Generic $generic
    ) {

        $this->data = $data;
        $this->coreRegistry = $coreRegistry;
        $this->itemFactory = $itemFactory;
        $this->stockData = $stockData;
        $this->productFactory = $productFactory;
        $this->msgHelper = $msgHelper;
        $this->generic = $generic;
        $this->request = $request;
    }

    /**
     * Save i95Dev Custom attributes
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $is_enabled = $this->data->isEnabled();
        if (!$is_enabled) {
            return;
        }
        if ($this->data->getGlobalValue('i95_observer_skip') || $this->request->getParam('isI95DevRestReq') == 'true') {
            return;
        }
        $supportedArray = $this->generic->getSupportedTypesForProduct();
        try {
            $product = $observer->getEvent()->getProduct();
            $productType = $product->getData("type_id");
            if (in_array($productType, $supportedArray)) {
                $productId = $this->productFactory->getIdBySku(trim($product->getData("sku")));
                if ($productId != "") {
                    $stockItem = $this->stockData->getStockItemBySku($product->getData("sku"));
                    if ($stockItem) {
                        $this->data->setGlobalValue('product_qty', $stockItem->getQty());
                        $this->data->setGlobalValue('product_managestock', $stockItem->getManageStock());
                    }
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
                $this->data->logger->createLog(__METHOD__, $ex->getMessage(), self::I95EXC, 'critical');
        }
    }
}
