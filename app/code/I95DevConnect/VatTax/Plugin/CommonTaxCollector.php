<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax
 */

namespace I95DevConnect\VatTax\Plugin;

/**
 * Plugin class to add sku and price of item for tax calculation
 */
class CommonTaxCollector
{

    /**
     * Arround map item
     * @param \Magento\Tax\Model\Sales\Total\Quote\CommonTaxCollector $subject
     * @param \Closure $proceed
     * @param \Magento\Tax\Api\Data\QuoteDetailsItemInterfaceFactory $itemDataObjectFactory
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param $priceIncludesTax
     * @param $useBaseCurrency
     * @param null $parentCode
     * @return mixed
     */
    public function aroundMapItem(
        \Magento\Tax\Model\Sales\Total\Quote\CommonTaxCollector $subject,  //NOSONAR
        \Closure $proceed,
        \Magento\Tax\Api\Data\QuoteDetailsItemInterfaceFactory $itemDataObjectFactory,
        \Magento\Quote\Model\Quote\Item\AbstractItem $item,
        $priceIncludesTax,
        $useBaseCurrency,
        $parentCode = null
    ) {
        $returnValue = $proceed($itemDataObjectFactory, $item, $priceIncludesTax, $useBaseCurrency, $parentCode);
        $returnValue->setSku($item->getSku());
        $returnValue->setPrice($item->getPrice());
        return $returnValue;
    }
}
