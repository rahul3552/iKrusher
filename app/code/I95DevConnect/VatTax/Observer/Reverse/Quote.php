<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax
 */

namespace I95DevConnect\VatTax\Observer\Reverse;

use Magento\Framework\Event\ObserverInterface;

/**
 * Observer class to add Tax to the Quote
 */
class Quote implements ObserverInterface
{

    public $helper;
    public $customTax;
    public $currencyHelper;
    public $currentObject;

    /**
     * Quote constructor.
     * @param \I95DevConnect\VatTax\Helper\Data $helper
     * @param \I95DevConnect\VatTax\Model\TaxCalculation $customTax
     * @param \Magento\Directory\Helper\Data $currencyHelper
     */
    public function __construct(
        \I95DevConnect\VatTax\Helper\Data $helper,
        \I95DevConnect\VatTax\Model\TaxCalculation $customTax,
        \Magento\Directory\Helper\Data $currencyHelper
    ) {
        $this->helper = $helper;
        $this->customTax = $customTax;
        $this->currencyHelper = $currencyHelper;
    }

    /**
     * Add calculated Tax to the Quote
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->currentObject = $observer->getEvent()->getData("quoteObject");

        if ($this->helper->isVatTaxEnabled()) {
            $objShippingAddress = $this->currentObject->quoteModel->getShippingAddress();
            $orderItemsArray = $this->currentObject->dataHelper->getValueFromArray(
                "orderItems",
                $this->currentObject->stringData
            );
            $currencyEntity = $this->currentObject->dataHelper->getValueFromArray(
                "currencyEntity",
                $this->currentObject->stringData
            );

            $taxArray = $this->getBaseTaxAmount($orderItemsArray, $objShippingAddress, $currencyEntity);
            $taxAmount = $taxArray["taxAmount"];
            $baseTaxAmount = $taxArray["baseTaxAmount"];

            $objShippingAddress->setTaxAmount($taxAmount);
            $objShippingAddress->setBaseTaxAmount($baseTaxAmount);
            $objShippingAddress->setGrandTotal($objShippingAddress->getGrandTotal() + $taxAmount);
            $objShippingAddress->setBaseGrandTotal($objShippingAddress->getBaseGrandTotal() + $baseTaxAmount);
            $this->currentObject->quoteModel->save();
        }
    }

    /**
     * @param $orderItemsArray
     * @param $objShippingAddress
     * @param $currencyEntity
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBaseTaxAmount($orderItemsArray, $objShippingAddress, $currencyEntity)
    {

        $taxAmount = 0;
        $baseTaxAmount = 0;
        foreach ($orderItemsArray as $eachItem) {
            foreach ($objShippingAddress->getAllItems() as $item) {
                if (strtoupper(trim($eachItem['sku'])) == strtoupper(trim($item->getSku()))) {
                    $taxRate = $this->customTax->getTax(
                        $eachItem['sku'],
                        $this->currentObject->quoteModel->getCustomer()->getId()
                    );
                    $taxamountforItem = round(($item->getPrice() * $taxRate * $item->getQty()) / 100, 2);
                    $item->setTaxPercent($taxRate);
                    $trans = $this->getTransactioalData($currencyEntity, $taxamountforItem);
                    $trans = empty($trans) ? $taxamountforItem : $trans;

                    $item->setTaxAmount($trans);
                    $item->setBaseTaxAmount($taxamountforItem)->save();
                    $taxAmount = $taxAmount + $trans;
                    $baseTaxAmount = $baseTaxAmount + $taxamountforItem;
                }
            }
        }

        return ["taxAmount" => $taxAmount, "baseTaxAmount" => $baseTaxAmount];
    }

    /**
     * @param $currencyEntity
     * @param $taxamountforItem
     */
    public function getTransactioalData($currencyEntity, $taxamountforItem)
    {
        $trans =  null;
        if (!empty($currencyEntity) && is_array($currencyEntity)) {
            $baseCurrencySymbol = isset($currencyEntity['baseCurrencySymbol']) ?
                $currencyEntity['baseCurrencySymbol'] : "";
            $transactionCurrencySymbol = isset($currencyEntity['transactionCurrencySymbol']) ?
                $currencyEntity['transactionCurrencySymbol'] : "";

            if ($baseCurrencySymbol != "" && $transactionCurrencySymbol != "") {
                $trans = $this->currencyHelper->currencyConvert(
                    $taxamountforItem,
                    $baseCurrencySymbol,
                    $transactionCurrencySymbol
                );
            }
        }

        return $trans;
    }
}
