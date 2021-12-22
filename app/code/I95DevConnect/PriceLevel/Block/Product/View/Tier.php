<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PriceLevel
 */

namespace I95DevConnect\PriceLevel\Block\Product\View;

use Magento\Framework\View\Element\Template;
use I95DevConnect\PriceLevel\Model\ItemPrice as ItemPrice;

/**
 * Product Price List
 * @api
 */
class Tier extends Template
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry;

    /**
     *
     * @var \I95DevConnect\PriceLevel\Model\ItemPrice
     */
    public $itemPrice;

    /**
     *
     * @var \Magento\Customer\Model\Session
     */
    public $customerSessionFactory;

    /**
     * Class constructor to include all the dependencies
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Customer\Model\SessionFactory $customerSessionFactory
     * @param ItemPrice $itemPrice
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Session $customerSession,
        ItemPrice $itemPrice,
        array $data = []
    ) {

        $this->coreRegistry = $registry;
        $this->itemPrice = $itemPrice;
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    /**
     * Get current product sku
     *
     * @return null|string
     */
    public function getProductSku()
    {
        $product = $this->coreRegistry->registry('product');
        return $product ? $product->getSku() : null;
    }

    /**
     * Get product's price levels list
     *
     * @return array
     */
    public function getProductPriceList()
    {
        $tierPrices = [];
        $finalPrices = [];
        $tierQty = [];
        $customerSession = $this->customerSession;
        if ($customerSession->isLoggedIn()) {
            $customerId = $customerSession->getCustomerId();
            $SKU = $this->getProductSku();
            $customerPriceLevel = $this->itemPrice->getCustomerPriceLevel($customerId);
            if ($customerPriceLevel == '') {
                $customerPriceLevel = $this->itemPrice->getCustomerGroupPriceLevel($customerId);
            }
            $tierPricesCollection = $this->itemPrice->getItemPriceList($customerPriceLevel, $SKU)
                    ->setOrder('qty', 'ASC')->setOrder('price', 'ASC')->getData();
            foreach ($tierPricesCollection as $tierPrices) {
                if (!in_array($tierPrices['qty'], $tierQty)) {
                    $tierQty[] = $tierPrices['qty'];
                    $finalPrices[] = $tierPrices;
                }
            }
        }
        return $finalPrices;
    }

    /**
     * Get product final price
     *
     * @return float
     */
    public function getFinalPrice()
    {
        /** @var float $productPrice is a minimal available price */
        return $this->coreRegistry->registry('product')->getFinalPrice();
    }

    /**
     * Get save percentage from price
     *
     * @param float $price
     * @return float
     */
    public function getSavePercent($price)
    {
        $savePercent = 0;
        $finalPrice = $this->getFinalPrice();
        if ($finalPrice !== 0) {
            $discount = $this->getFinalPrice() - $price;
            $savePercent = ($discount * 100) / $this->getFinalPrice();
        }
        return ceil($savePercent);
    }
}
