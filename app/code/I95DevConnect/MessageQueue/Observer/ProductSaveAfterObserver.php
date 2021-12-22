<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\Http;
use \I95DevConnect\MessageQueue\Helper\Data;

/**
 * Observer class to save product
 */
class ProductSaveAfterObserver implements ObserverInterface
{

    const MAGLOGNAME = 'MagentoToERP';
    const ERPLOGNAME = 'ERPToMagento';
    const I95EXC = 'i95devApiException';
    const PRODUCTQTY = 'product_qty';

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

    /**
     *
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $msgHelper;

    /**
     * @var \I95DevConnect\MessageQueue\Helper\Generic
     */
    public $generic;

    /**
     *
     * @var \Magento\Framework\App\Request\Http
     */
    public $request;

    /**
     *
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    public $stockRegistry;

    /**
     *
     * @var \Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory
     */
    public $stockItemInterfaceFactory;

    public $product = null;

    /**
     *
     * @param Data $data
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Catalog\Model\ProductFactory $itemFactory
     * @param Data $msgHelper
     * @param \I95DevConnect\MessageQueue\Helper\Generic $generic
     * @param Http $request
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory $stockItemInterfaceFactory
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Helper\Data $data,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Model\ProductFactory $itemFactory,
        \I95DevConnect\MessageQueue\Helper\Data $msgHelper,
        \I95DevConnect\MessageQueue\Helper\Generic $generic,
        Http $request,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory $stockItemInterfaceFactory
    ) {

        $this->data = $data;
        $this->coreRegistry = $coreRegistry;
        $this->itemFactory = $itemFactory;
        $this->msgHelper = $msgHelper;
        $this->generic = $generic;
        $this->request = $request;
        $this->stockRegistry = $stockRegistry;
        $this->stockItemInterfaceFactory = $stockItemInterfaceFactory;
    }

    /**
     * Stop stock update from magento
     * @param  \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $is_enabled = $this->data->isEnabled();
        if (!$is_enabled || $this->data->getGlobalValue('i95_observer_skip') ||
        $this->request->getParam('isI95DevRestReq') == 'true') {
            return;
        }
        try {
            $this->product = $observer->getEvent()->getProduct();
            $supportedArray = $this->generic->getSupportedTypesForProduct();
            $productType = $this->product->getData("type_id");
            $this->product->setData('update_by', 'Magento')->getResource()->saveAttribute($this->product, 'update_by');
            /*@updatedBy Sravani Polu Starting code for setting quantity to 0 for new product and
            not updating quantity for existing product*/
            if (in_array($productType, $supportedArray)) {
                $qty = $this->data->getGlobalValue(self::PRODUCTQTY);
                $stockData = $this->product->getStockData();
                $isInStock = null;
                if ($stockData && isset($stockData['is_in_stock'])) {
                    $isInStock = $stockData['is_in_stock'];
                }

                if ($qty === null) {
                    $this->updateStock($isInStock, 0);
                } else {
                    $oldQty = $this->data->getGlobalValue(self::PRODUCTQTY);
                    if ($stockData && isset($stockData['backorders'])) {
                        $this->updateStock($isInStock, $oldQty, $stockData['backorders']);
                    } else {
                        $this->updateStock($isInStock, $oldQty);
                    }
                }
            }
            /*End of code for setting quantity to 0 for product creation and existing quantity for product*/
            $this->data->unsetGlobalValue(self::PRODUCTQTY);
            $this->data->unsetGlobalValue('product_managestock');
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->data->logger->createLog(__METHOD__, $ex->getMessage(), self::I95EXC, 'critical');
        }
    }

    /**
     * Update stock
     *
     * @param boolean $isInStock
     * @param int $qty
     * @param string $backOrders
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function updateStock($isInStock, $qty, $backOrders = null)
    {
        $manageStock = $this->data->getGlobalValue('product_managestock');
        $stockItem = $this->stockItemInterfaceFactory->create();
        $stockItem->setUseConfigManageStock(0)
                ->setManageStock($manageStock)
                ->setIsInStock($isInStock)
                ->setQty($qty);
        if ($backOrders) {
            $stockItem->setBackorders($backOrders);
        }
        $productSku = $this->product->getData("sku");
        $this->stockRegistry->updateStockItemBySku($productSku, $stockItem);
    }
}
