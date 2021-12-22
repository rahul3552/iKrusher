<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order\Forward;

/**
 * Class responsible for preparing order item data which will be added in order result to ERP
 * @createdBy Sravani Polu
 */
class OrderItems
{
    public $eventManager;
    public $generic;
    public $productEntity;

    /**
     *
     * @param \Magento\Framework\Event\Manager $eventManager
     * @param \I95DevConnect\MessageQueue\Helper\Generic $generic
     */
    public function __construct(
        \Magento\Framework\Event\Manager $eventManager,
        \I95DevConnect\MessageQueue\Helper\Generic $generic
    ) {
        $this->eventManager = $eventManager;
        $this->generic = $generic;
    }

    /**
     * preparation of Order Item Entity
     * @param  array $order
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @createdBy Sravani Polu
     */
    public function getOrderItemEntities($order)
    {
        $orderItemsData = [];
        try {
            $orderItems = $order->getItems();
            $supportedProductType = $this->generic->getSupportedProductTypesForOrder();
            foreach ($orderItems as $item) {
                $this->productEntity = [];
                if (!in_array($item->getProductType(), $supportedProductType)) {
                    //@author Divya Koona. Exception message string concatenation changed.
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __("i95dev_unsupported_product %1", $item->getProductType())
                    );
                }

                $this->productEntity['sku'] = $item->getSku();
                $this->productEntity['itemId'] = $item->getItemId();
                $this->productEntity['typeId'] = $item->getProductType();
                $this->productEntity['price'] = (float)$item->getBaseOriginalPrice();
                $this->productEntity['qty'] = (int)$item->getQtyOrdered();
                $this->productEntity['itemTaxAmount'] = (float)$item->getBaseTaxAmount();
                $this->productEntity['specialPrice'] = (float)$item->getBasePrice();

                // @updatedBy Arushi B - converted discount to string to fix discount not applying issue
                $discountEntity['discountAmount'] = (float)(abs($item->getBaseDiscountAmount()));
                $discountEntity['discountType'] = 'discount';
                $this->productEntity['discount'][] = $discountEntity;
                $this->eventManager->dispatch(
                    "erpconnect_forward_orderproductinfo",
                    ['orderItems' => $item, 'orderItemsObj' => $this]
                );
                $orderItemsData[] = $this->productEntity;
            }

            return $orderItemsData;
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
    }
}
