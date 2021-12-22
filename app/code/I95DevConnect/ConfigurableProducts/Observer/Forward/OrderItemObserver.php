<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_ConfigurableProducts
 */

namespace I95DevConnect\ConfigurableProducts\Observer\Forward;

use I95DevConnect\MessageQueue\Helper\Data;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Framework\Event\ObserverInterface;

/**
 * Observer class for order items
 */
class OrderItemObserver implements ObserverInterface
{
    const DISCOUNT="discount";

    /**
     * @var ProductRepositoryInterface|\Magento\Catalog\Model\ProductRepositoryFactory
     */
    public $productRepo;

    /**
     * @var Data
     */
    public $dataHelper;

    public $eavAttrModel;

    public $parentSKU;

    public $erpOrderItem;

    /**
     * OrderItemObserver constructor.
     * @param ProductRepositoryInterface $productRepo
     * @param Data $dataHelper
     * @param Attribute $eavAttrModel
     */
    public function __construct(
        ProductRepositoryInterface $productRepo,
        Data $dataHelper,
        Attribute $eavAttrModel
    ) {
        $this->productRepo = $productRepo;
        $this->dataHelper = $dataHelper;
        $this->eavAttrModel = $eavAttrModel;
    }

    /**
     * Set order item info in forward sync
     * @param \Magento\Framework\Event\Observer $observer
     * @author Divya Koona.
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $orderItem = $observer->getEvent()->getData("orderItems");
        $this->erpOrderItem = $observer->getEvent()->getData("orderItemsObj");
        $this->parentSKU = null;
        $component = $this->dataHelper->getComponent();
        if ($orderItem['product_type'] == 'configurable') {
            $itemId = $orderItem['product_id'];
            $this->parentSku = $this->getSkuById($itemId);
            $this->erpOrderItem->productEntity['sku'] = $this->parentSku;
            if ($component == 'D365FO' && isset($orderItem['product_options'])) {
                $this->setProductAttributes($orderItem, $component);
            }
        }

        $this->setProductDiscount($orderItem);
        $this->erpOrderItem->productEntity['parentSku'] = $this->parentSKU;
        //@Hrusikesh Get Variant Id By Product SKU. Only Work For AX
        $component = $this->dataHelper->getComponent();
        if ($component == 'AX' && $this->parentSKU != null) {
            $productVariantId = $this->getVariantIdBySku($orderItem->getSku());
            $this->erpOrderItem->productEntity['variantId'] = $productVariantId;
        }

        $this->setProductAttributesInfo($orderItem, $component);
    }

    /**
     * @param $orderItem
     * @param $component
     */
    public function setProductAttributesInfo($orderItem, $component)
    {
        if ($component == 'D365FO' && $this->parentSKU != null) {
            $prodOptions = $orderItem->getProductOptions();
            $attributesInfo = [];
            if (isset($prodOptions['info_buyRequest']) && isset($prodOptions['info_buyRequest']['super_attribute'])) {
                foreach ($prodOptions['info_buyRequest']['super_attribute'] as $attr => $val) {
                    $attribute = $this->eavAttrModel->load($attr);
                    $attributeCode = $attribute->getAttributeCode();
                    $optionText = $attribute->getSource()->getOptionText($val);
                    $attributesInfo[] = [
                        "attributeCode" => $attributeCode,
                        "attributeValue" => $optionText
                    ];
                }
            }

            $this->erpOrderItem->productEntity['attributes'] = $attributesInfo;
        }
    }

    /**
     * @param $orderItem
     */
    public function setProductDiscount($orderItem)
    {
        $parentPrice = 0;
        if (is_object($orderItem->getParentItem())) {
            $parentItem = $orderItem->getParentItem();
            $parentProductId = $parentItem->getProductId();
            $this->parentSKU = $this->getSkuById($parentProductId);
            $parentPrice = $parentItem->getBaseOriginalPrice();
            $parentSpecialPrice = $parentItem->getBasePrice();
            $this->erpOrderItem->productEntity['price'] = $parentPrice;
            $this->erpOrderItem->productEntity['specialPrice'] = $parentSpecialPrice;
            $discountEntity = [];
            //Discount entity field changed from discountAmount to discount and removed discount field from entity
            $this->erpOrderItem->productEntity[self::DISCOUNT] = [];
            // @updatedBy Arushi Bansal - changed as string value as issue faced in NAV
            $discountEntity['discountAmount'] = (string)(abs($parentItem->getBaseDiscountAmount()));
            $discountEntity['discountType'] = self::DISCOUNT;
            $this->erpOrderItem->productEntity[self::DISCOUNT][] = $discountEntity;
        }
    }

    /**
     * @param $orderItem
     * @param $component
     */
    public function setProductAttributes($orderItem, $component)
    {
        $productOptions = $orderItem['product_options'];
        $attributesInfo = [];
        if (isset($productOptions['attributes_info']) && !empty($productOptions['attributes_info'])) {
            foreach ($productOptions['attributes_info'] as $key => $attributeInfo) {
                $attributesInfo[$key]['attributeCode'] =
                    isset($attributeInfo['label']) ? $attributeInfo['label'] : '';
                $attributesInfo[$key]['attributeValue'] =
                    isset($attributeInfo['value']) ? $attributeInfo['value'] : '';
            }
            $this->erpOrderItem->productEntity['attributes'] = $attributesInfo;
        }
    }

    /**
     * Get product sku by id
     * @param int $id
     * @return string
     * @author Divya Koona.
     */
    public function getSkuById($id)
    {
        try {
            $result = $this->productRepo->getById($id);
            return $result->getSku();
        } catch (\Magento\Framework\Exception\NoSuchEntityException $ex) {
            throw new \Magento\Framework\Exception\NoSuchEntityException($ex->getMessage());
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            throw new \Magento\Framework\Exception\LocalizedException($ex->getMessage());
        }
    }

    /**
     * Get product variant Id from SKU. Only for AX
     * @param type $sku
     * @return text
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @author Hrusikesh Manna
     */
    public function getVariantIdBySku($sku)
    {
        try {
            $product = $this->productRepo->get($sku);
            if ($product->getvariantId()) {
                return $product->getvariantId();
            }
            return null;
        } catch (\Magento\Framework\Exception\NoSuchEntityException $ex) {
            throw new \Magento\Framework\Exception\NoSuchEntityException($ex->getMessage());
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            throw new \Magento\Framework\Exception\LocalizedException($ex->getMessage());
        }
    }
}
