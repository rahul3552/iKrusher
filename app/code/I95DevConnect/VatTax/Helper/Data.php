<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax
 */

namespace I95DevConnect\VatTax\Helper;

use I95DevConnect\VatTax\Model\Calculation\UnitBaseCalculator;

/**
 * Helper class to verify if Vat Tax module is enabled
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * scopeConfig for system Congiguration
     *
     * @var string
     */
    public $scopeConfig;

    /**
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Check is extension enabled
     * @return mixed
     */
    public function isVatTaxEnabled()
    {
        $isEnabled = $this->scopeConfig
            ->getValue(
                'i95devconnect_vattax/vattax_enabled_settings/enable_vattax',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
            );

        if (!$isEnabled) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __("Please Enable VatTax Module")
            );
        } else {
            return true;
        }
    }

    /**
     * @param $entityCode
     * @param $code
     * @param $createObj
     */
    public function taxSyncEventAndRegistry($entityCode, $code, $createObj)
    {
        $createObj->dataHelper->unsetGlobalValue('i95_observer_skip');

        $jsondata = json_encode(["entityCode" => $entityCode,
            "targetId" => $code,
            "source" => "ERP"]);

        $createObj->dataHelper->coreRegistry->unregister('savingSource');
        $createObj->dataHelper->coreRegistry->register('savingSource', $jsondata);

        $aftereventname = 'erpconnect_messagequeuetomagento_aftersave_' . $entityCode;
        $createObj->eventManager->dispatch($aftereventname, ['currentObject' => $createObj]);
    }

    /**
     * @param $appState
     * @param $session
     * @param $checkoutSession
     * @param $customerSession
     * @param $taxClassManagement
     * @param $item
     * @param $addressRateRequest
     * @return array|bool
     */
    public function getTaxRateObj(
        $appState,
        $customerId,
        $taxClassManagement,
        $item,
        $addressRateRequest
    ) {
        if ($customerId == "") {
            return parent::calculateWithTaxNotInPrice($item, $quantity);
        }

        $taxRateRequest = $addressRateRequest->setProductClassId(
            $taxClassManagement->getTaxClassId($item->getTaxClassKey())
        );

        return [
            'customerId' => $customerId,
            'taxRateRequest' => $taxRateRequest
        ];
    }

    /**
     * @param $calculateClass
     * @param $item
     * @param $rate
     * @param $storeRate
     * @param $round
     * @param $quantity
     * @return array
     */
    public function calculatePriceInclTax($calculateClass, $item, $rate, $storeRate, $round, $quantity)
    {
        $applyTaxAfterDiscount = $calculateClass->config->applyTaxAfterDiscount($calculateClass->storeId);
        $priceInclTax = $calculateClass->calculationTool->round($item->getUnitPrice());
        if (!$calculateClass->isSameRateAsStore($rate, $storeRate)) {
            $priceInclTax = $calculateClass->calculatePriceInclTax($priceInclTax, $storeRate, $rate, $round);
        }
        $uniTax = $calculateClass->calculationTool->calcTaxAmount($priceInclTax, $rate, true, false);
        $deltaRoundingType = self::KEY_REGULAR_DELTA_ROUNDING;
        if ($applyTaxAfterDiscount) {
            $deltaRoundingType = self::KEY_TAX_BEFORE_DISCOUNT_DELTA_ROUNDING;
        }
        $uniTax = $calculateClass->roundAmount($uniTax, $rate, true, $deltaRoundingType, $round, $item);
        $price = $priceInclTax - $uniTax;

        $discountTaxCompensationAmount = 0;
        $discountAmount = $item->getDiscountAmount();
        if ($applyTaxAfterDiscount) {
            $unitDiscountAmount = $discountAmount / $quantity;
            $taxableAmount = max($priceInclTax - $unitDiscountAmount, 0);
            $unitTaxAfterDiscount = $calculateClass->calculationTool->calcTaxAmount(
                $taxableAmount,
                $rate,
                true,
                false
            );
            $unitTaxAfterDiscount = $calculateClass->roundAmount(
                $unitTaxAfterDiscount,
                $rate,
                true,
                self::KEY_REGULAR_DELTA_ROUNDING,
                $round,
                $item
            );

            // Set discount tax compensation
            $unitDiscountTaxCompensationAmount = $uniTax - $unitTaxAfterDiscount;
            $discountTaxCompensationAmount = $unitDiscountTaxCompensationAmount * $quantity;
            $rowTax = $uniTax * $quantity;
        }

        return [
            'discountTaxCompensationAmount' => $discountTaxCompensationAmount,
            'price' => $price,
            'rowTax' => $rowTax,
            'priceInclTax' => $priceInclTax
        ];
    }

    /**
     * @param $calculateTaxClass
     * @param $item
     * @return |null
     */
    public function calculateTaxRateRequest($calculateTaxClass, $item)
    {
        $areaCode= $calculateTaxClass->appState->getAreaCode();
        if ($areaCode == "adminhtml") {
            $quote = $calculateTaxClass->session->getQuote();
            $customerId = $quote->getCustomer()->getId();
        } else {
            $quote = $calculateTaxClass->checkoutSession->getQuote();
            $customerId = $quote->getCustomer()->getId();
            if ($customerId == '') {
                $customerId = $calculateTaxClass->customerSession->getCustomerId();
            }
        }

        if ($customerId == "" || !$quote->getId()) {
            return null;
        }

        return $calculateTaxClass->i95devAddressRateRequest()->setProductClassId(
            $calculateTaxClass->taxClassManagement->getTaxClassId($item->getTaxClassKey())
        );
    }

    /**
     * @param $calculateclass
     * @param $taxRateRequest
     * @param $item
     * @param $quantity
     * @param $round
     * @return mixed
     */
    public function calculateWithTaxNotInPrice($calculateclass, $taxRateRequest, $item, $quantity, $round, $customerId)
    {

        $calculateclass->calculationTool->getRate($taxRateRequest);
        $appliedRates = $calculateclass->calculationTool->getAppliedRates($taxRateRequest);

        $applyTaxAfterDiscount = $calculateclass->config->applyTaxAfterDiscount($calculateclass->storeId);

        $discountAmount = $item->getDiscountAmount();
        $discountTaxCompensationAmount = 0;

        // Calculate $price
        $price = $calculateclass->calculationTool->round($item->getUnitPrice());
        $unitTaxes = [];
        $unitTaxesBeforeDiscount = [];
        $appliedTaxes = [];
        $taxRate = 0;
        //Apply each tax rate separately
        foreach ($appliedRates as $appliedRate) {
            $taxId = $appliedRate['id'];
            $sku = $item->getSku();
            if ($sku) {
                $taxRate = $calculateclass->getCustomTax($sku, $customerId);
            } else {
                $taxRate = $appliedRate['percent'];
            }

            $unitTaxPerRate = $calculateclass->calculationTool->calcTaxAmount($price, $taxRate, false, false);

            $deltaRoundingType = UnitBaseCalculator::KEY_REGULAR_DELTA_ROUNDING;
            if ($applyTaxAfterDiscount) {
                $deltaRoundingType = UnitBaseCalculator::KEY_TAX_BEFORE_DISCOUNT_DELTA_ROUNDING;
            }

            $unitTaxPerRate = $calculateclass->taxRoundAmount(
                $unitTaxPerRate,
                $taxId,
                $deltaRoundingType,
                $round,
                $item
            );

            $unitTaxAfterDiscount = $unitTaxPerRate;

            //Handle discount
            if ($applyTaxAfterDiscount) {
                $unitDiscountAmount = $discountAmount / $quantity;
                $taxableAmount = max($price - $unitDiscountAmount, 0);
                $unitTaxAfterDiscount = $calculateclass->calculationTool->calcTaxAmount(
                    $taxableAmount,
                    $taxRate,
                    false,
                    false
                );

                $unitTaxAfterDiscount = $calculateclass->taxRoundAmount(
                    $unitTaxAfterDiscount,
                    $taxId,
                    UnitBaseCalculator::KEY_REGULAR_DELTA_ROUNDING,
                    $round,
                    $item
                );
            }

            $appliedTaxes[$taxId] = $calculateclass->calculateAppliedTax(
                $unitTaxAfterDiscount,
                $quantity,
                $appliedRate
            );

            $unitTaxes[] = $unitTaxAfterDiscount;
            $unitTaxesBeforeDiscount[] = $unitTaxPerRate;
        }

        $unitTax = array_sum($unitTaxes);
        $unitTaxBeforeDiscount = array_sum($unitTaxesBeforeDiscount);

        $rowTax = $unitTax * $quantity;
        $priceInclTax = $price + $unitTaxBeforeDiscount;

        return $calculateclass->gettaxDetailsItemDataObj()
            ->setCode($item->getCode())
            ->setType($item->getType())
            ->setRowTax($rowTax)
            ->setPrice($price)
            ->setPriceInclTax($priceInclTax)
            ->setRowTotal($price * $quantity)
            ->setRowTotalInclTax($priceInclTax * $quantity)
            ->setDiscountTaxCompensationAmount($discountTaxCompensationAmount)
            ->setAssociatedItemCode($item->getAssociatedItemCode())
            ->setTaxPercent($taxRate)
            ->setAppliedTaxes($appliedTaxes);
    }
}
