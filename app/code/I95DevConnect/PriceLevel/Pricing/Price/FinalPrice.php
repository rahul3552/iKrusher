<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PriceLevel
 */

namespace I95DevConnect\PriceLevel\Pricing\Price;

use Magento\Catalog\Pricing\Price\FinalPriceInterface;
use Magento\Framework\Pricing\Price\AbstractPrice;
use Magento\Catalog\Pricing\Price\BasePrice;
use Magento\Framework\Pricing\Adjustment\CalculatorInterface;
use Magento\Framework\Pricing\SaleableInterface;
use \I95DevConnect\PriceLevel\Helper\Data;

/**
 * Final price model
 */
class FinalPrice extends AbstractPrice implements FinalPriceInterface
{

    /**
     * Price type final
     */
    const PRICE_CODE = 'final_price';

    /**
     * @var BasePrice
     */
    public $basePrice;

    /**
     * @var \Magento\Framework\Pricing\Amount\AmountInterface
     */
    public $minimalPrice;

    /**
     * @var \Magento\Framework\Pricing\Amount\AmountInterface
     */
    public $maximalPrice;
    public $session;
    public $itemPrice;

    /**
     * @var PriceInfoInterface
     */
    public $priceInfo;

    /**
     * @var Data
     */
    public $data;

    /**
     * Class constructor to include all the dependencies
     *
     * @param \Magento\Customer\Model\SessionFactory $session
     * @param \I95DevConnect\PriceLevel\Model\ItemPrice $itemPrice
     * @param Data $data
     * @param CalculatorInterface $calculator
     * @param SaleableInterface $saleableItem
     * @param float $quantity
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        \Magento\Customer\Model\SessionFactory $session,
        \I95DevConnect\PriceLevel\Model\ItemPrice $itemPrice,
        Data $data,
        CalculatorInterface $calculator,
        SaleableInterface $saleableItem,
        $quantity,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
    ) {
        parent::__construct($saleableItem, $quantity, $calculator, $priceCurrency);
        $this->session = $session;
        $this->itemPrice = $itemPrice;
        $this->data = $data;
    }

    /**
     * Get Price of a product for the customer
     *
     * @return float|bool
     */
    public function getValue()
    {
        if ($this->session->create()->isLoggedIn()) {
            $customerId =  $this->session->create()->getCustomer()->getId();
            $qty = 1;
            $finalPrice = $this->itemPrice->getItemFinalPrice($this->product, $customerId, $qty);
            $finalPrice = $this->data->convertPrice($finalPrice);
            if ($finalPrice !== 0) {
                return $finalPrice;
            } else {
                return max(0, $this->getBasePrice()->getValue());
            }
        } else {
            return max(0, $this->getBasePrice()->getValue());
        }
    }

    /**
     * Get Minimal Price Amount for the product
     *
     * @return \Magento\Framework\Pricing\Amount\AmountInterface
     */
    public function getMinimalPrice()
    {
        if (!$this->minimalPrice) {
            $minimal_price = $this->product->getMinimalPrice();
            if ($minimal_price === null) {
                $minimal_price = $this->getValue();
            } else {
                $minimal_price = $this->priceCurrency->convertAndRound($minimal_price);
            }
            $this->minimalPrice = $this->calculator->getAmount($minimal_price, $this->product);
        }
        return $this->minimalPrice;
    }

    /**
     * Get Maximal Price Amount for the product
     *
     * @return \Magento\Framework\Pricing\Amount\AmountInterface
     */
    public function getMaximalPrice()
    {
        if (!$this->maximalPrice) {
            $this->maximalPrice = $this->calculator->getAmount($this->getValue(), $this->product);
        }
        return $this->maximalPrice;
    }

    /**
     * Retrieve base price instance lazily
     *
     * @return BasePrice|\Magento\Framework\Pricing\Price\PriceInterface
     */
    public function getBasePrice()
    {
        if (!$this->basePrice) {
            $this->basePrice = $this->priceInfo->getPrice(BasePrice::PRICE_CODE);
        }
        return $this->basePrice;
    }
}
