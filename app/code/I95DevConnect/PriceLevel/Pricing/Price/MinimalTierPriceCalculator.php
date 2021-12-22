<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PriceLevel
 */

namespace I95DevConnect\PriceLevel\Pricing\Price;

use Magento\Framework\Pricing\SaleableInterface;
use Magento\Framework\Pricing\Adjustment\CalculatorInterface;
use \I95DevConnect\PriceLevel\Model\ItemPrice;
use \I95DevConnect\PriceLevel\Helper\Data;
use Magento\Customer\Model\SessionFactory;

/**
 * MinimalTierPriceCalculator shows minimal value of Tier Prices.
 */
class MinimalTierPriceCalculator extends \Magento\Catalog\Pricing\Price\MinimalTierPriceCalculator
{
   /**
    *
    * @var \I95DevConnect\PriceLevel\Model\ItemPrice
    */
    public $i95PriceList;

    /**
     * @var \I95DevConnect\PriceLevel\Helper\Data
     */
    public $priceLevelHelper;

    /**
     *
     * @var Magento\Customer\Model\SessionFactory
     */
    public $customerSessionFactory;

    /**
     * Class constructor to include all the dependencies
     *
     * @param CalculatorInterface $calculator
     * @param ItemPrice $i95PriceList
     * @param Data $priceLevelHelper
     * @param SessionFactory $customerSessionFactory
     */
    public function __construct(
        CalculatorInterface $calculator,
        ItemPrice $i95PriceList,
        Data $priceLevelHelper,
        SessionFactory $customerSessionFactory
    ) {
        $this->calculator = $calculator;
        $this->i95PriceList = $i95PriceList;
        $this->priceLevelHelper = $priceLevelHelper;
        $this->customerSessionFactory = $customerSessionFactory;
        parent::__construct($calculator);
    }

    /**
     * Get raw value of "as low as" as a minimal among tier prices.
     *
     * @param SaleableInterface $saleableItem
     *
     * @return float|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getValue(SaleableInterface $saleableItem)
    {
        $isEnabled = $this->priceLevelHelper->isEnabled();
        if ($isEnabled) {
            $tierPriceList = $this->i95PriceList->getItemPriceListDisplay(
                $this->customerSessionFactory->create()->getId(),
                $saleableItem->getSku()
            );
            $tierPrices = [];
            foreach ($tierPriceList as $tierPrice) {
                $price = $this->priceLevelHelper->convertPrice($tierPrice['price']);
                $tierPrices[] = $price;
            }
            return $tierPrices ? min($tierPrices) : null;
        } else {
            return parent::getValue($saleableItem);
        }
    }
}
