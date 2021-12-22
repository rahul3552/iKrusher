<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax
 */

namespace I95DevConnect\VatTax\Model;

use Magento\Tax\Api\Data\QuoteDetailsItemInterface;
use Magento\Tax\Api\Data\AppliedTaxInterfaceFactory;
use Magento\Tax\Api\Data\AppliedTaxRateInterfaceFactory;
use Magento\Tax\Api\Data\TaxDetailsItemInterfaceFactory;
use Magento\Tax\Api\TaxClassManagementInterface;
use Magento\Tax\Model\Calculation;

/**
 * Class for Total Base Calculator
 */
class TotalBaseCalculator extends \Magento\Tax\Model\Calculation\TotalBaseCalculator
{

    /**
     * Quote session object
     *
     * @var \Magento\Backend\Model\Session\Quote
     */
    public $session;

    /**
     * Custom tax calculation model
     * @var \I95DevConnect\VatTax\Model\TaxCalculation
     */
    protected $customTax;

    /**
     * @var CustomerModelSession
     */
    protected $customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    public $taxClassManagement;

    /**
     *
     * @param TaxClassManagementInterface $taxClassService
     * @param TaxDetailsItemInterfaceFactory $taxDetailsItemDataObjectFactory
     * @param AppliedTaxInterfaceFactory $appliedTaxDataObjectFactory
     * @param AppliedTaxRateInterfaceFactory $appliedTaxRateDataObjectFactory
     * @param Calculation $calculationTool
     * @param \Magento\Tax\Model\Config $config
     * @param int $storeId
     * @param \Magento\Backend\Model\Session\Quote $quoteSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \I95DevConnect\VatTax\Model\TaxCalculation $taxCaluculation
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\App\State $appState
     * @param \I95DevConnect\VatTax\Helper\Data $helperData
     * @param \Magento\Framework\DataObject $addressRateRequest
     */
    public function __construct(
        TaxClassManagementInterface $taxClassService,
        TaxDetailsItemInterfaceFactory $taxDetailsItemDataObjectFactory,
        AppliedTaxInterfaceFactory $appliedTaxDataObjectFactory,
        AppliedTaxRateInterfaceFactory $appliedTaxRateDataObjectFactory,
        Calculation $calculationTool,
        \Magento\Tax\Model\Config $config,
        $storeId,
        \Magento\Backend\Model\Session\Quote $quoteSession,
        \Magento\Customer\Model\Session $customerSession,
        \I95DevConnect\VatTax\Model\TaxCalculation $taxCaluculation,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\State $appState,
        \I95DevConnect\VatTax\Helper\Data $helperData,
        \Magento\Framework\DataObject $addressRateRequest = null
    ) {
        $this->session = $quoteSession;
        $this->checkoutSession = $checkoutSession;
        $this->appState = $appState;
        $this->helperData = $helperData;
        $this->customerSession = $customerSession;
        $this->customTax = $taxCaluculation;
        $this->taxClassManagement = $taxClassService;

        parent::__construct(
            $taxClassService,
            $taxDetailsItemDataObjectFactory,
            $appliedTaxDataObjectFactory,
            $appliedTaxRateDataObjectFactory,
            $calculationTool,
            $config,
            $storeId,
            $addressRateRequest
        );
    }

    /**
     * Calculate tax details for quote item with tax not in price with given quantity
     *
     * @param \Magento\Tax\Api\Data\QuoteDetailsItemInterface $item
     * @param int $quantity
     * @param bool $round
     * @return \Magento\Tax\Api\Data\TaxDetailsItemInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @createdBy Ranjith Kumar Rasakatla
     */
    protected function calculateWithTaxNotInPrice(QuoteDetailsItemInterface $item, $quantity, $round = true)
    {
        $itemdiscountAmount = 0;
        $customerId = $this->customerId;

        if ($customerId == "" || $item->getSku() === null) {
            return parent::calculateWithTaxNotInPrice($item, $quantity, $round = true);
        }

        $taxRateRequest = $this->getAddressRateRequest()->setProductClassId(
            $this->taxClassManagement->getTaxClassId($item->getTaxClassKey())
        );

        $appliedRates = $this->calculationTool->getAppliedRates($taxRateRequest);

        $applyTaxAfterDiscount = $this->config->applyTaxAfterDiscount($this->storeId);
        $discountAmount = $item->getDiscountAmount();
        $discountTaxCompensationAmount = 0;

        // Calculate $rowTotal
        $price = $this->calculationTool->round($item->getUnitPrice());
        $rowTotal = $price * $quantity;
        $rowTaxes = [];
        $rowTaxesBeforeDiscount = [];
        $rowTaxesPercent = [];
        $appliedTaxes = [];
        //Apply each tax rate separately
        foreach ($appliedRates as $appliedRate) {
            $taxId = $appliedRate['id'];
            $sku = $item->getSku();
            $taxRate = $this->customTax->getTax($sku, $customerId);
            $rowTaxPerRate = $this->calculationTool->calcTaxAmount($rowTotal, $taxRate, false, false);
            $deltaRoundingType = self::KEY_REGULAR_DELTA_ROUNDING;
            if ($applyTaxAfterDiscount) {
                $deltaRoundingType = self::KEY_TAX_BEFORE_DISCOUNT_DELTA_ROUNDING;
            }

            $rowTaxPerRate = $this->roundAmount($rowTaxPerRate, $taxId, false, $deltaRoundingType, $round, $item);
            $rowTaxAfterDiscount = $rowTaxPerRate;

            //Handle discount
            if ($applyTaxAfterDiscount) {
                $taxableAmount = max($rowTotal - $discountAmount - $itemdiscountAmount, 0);
                $rowTaxAfterDiscount = $this->calculationTool->calcTaxAmount(
                    $taxableAmount,
                    $taxRate,
                    false,
                    false
                );
                $rowTaxAfterDiscount = $this->roundAmount(
                    $rowTaxAfterDiscount,
                    $taxId,
                    false,
                    self::KEY_REGULAR_DELTA_ROUNDING,
                    $round,
                    $item
                );
            }

            $appliedTaxes[$taxId] = $this->getAppliedTax(
                $rowTaxAfterDiscount,
                $appliedRate
            );

            $rowTaxes[] = $rowTaxAfterDiscount;
            $rowTaxesBeforeDiscount[] = $rowTaxPerRate;
            $rowTaxesPercent[] = $taxRate;
        }

        $rowTax = array_sum($rowTaxes);
        $rowTaxBeforeDiscount = array_sum($rowTaxesBeforeDiscount);
        $rowTaxPercent = array_sum($rowTaxesPercent);
        $rowTotalInclTax = $rowTotal + $rowTaxBeforeDiscount;
        $priceInclTax = $rowTotalInclTax / $quantity;
        if ($round) {
            $priceInclTax = $this->calculationTool->round($priceInclTax);
        }

        return $this->taxDetailsItemDataObjectFactory->create()
            ->setCode($item->getCode())
            ->setType($item->getType())
            ->setRowTax($rowTax)
            ->setPrice($price)
            ->setPriceInclTax($priceInclTax)
            ->setRowTotal($rowTotal)
            ->setRowTotalInclTax($rowTotalInclTax)
            ->setDiscountTaxCompensationAmount($discountTaxCompensationAmount)
            ->setAssociatedItemCode($item->getAssociatedItemCode())
            ->setTaxPercent($rowTaxPercent)
            ->setAppliedTaxes($appliedTaxes);
    }

    /**
     * Calculate tax details for quote item with tax in price with given quantity
     *
     * @param \Magento\Tax\Api\Data\QuoteDetailsItemInterface $item
     * @param int $quantity
     * @param bool $round
     * @return \Magento\Tax\Api\Data\TaxDetailsItemInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @createdBy Ranjith Kumar Rasakatla
     */
    protected function calculateWithTaxInPrice(QuoteDetailsItemInterface $item, $quantity, $round = true)
    {
        $getTaxRateObj= $this->helperData->getTaxRateObj(
            $this->appState,
            $this->customerId,
            $this->taxClassManagement,
            $item,
            $this->getAddressRateRequest()
        );
        if (!isset($getTaxRateObj['customerId'])) {
            return parent::calculateWithTaxInPrice($item, $quantity, $round = true);
        }

        $sku = $item->getSku();
        $rate = $storeRate = $this->customTax->getTax($sku, $getTaxRateObj['customerId']);

        $discountTaxCompensationAmount = 0;
        $applyTaxAfterDiscount = $this->config->applyTaxAfterDiscount($this->storeId);
        $discountAmount = $item->getDiscountAmount();

        // Calculate $rowTotalInclTax
        $priceInclTax = $this->calculationTool->round($item->getUnitPrice());
        $rowTotalInclTax = $priceInclTax * $quantity;
        if (!$this->isSameRateAsStore($rate, $storeRate)) {
            $priceInclTax = $this->calculatePriceInclTax($priceInclTax, $storeRate, $rate, $round);
            $rowTotalInclTax = $priceInclTax * $quantity;
        }
        $rowTaxExact = $this->calculationTool->calcTaxAmount($rowTotalInclTax, $rate, true, false);
        $deltaRoundingType = self::KEY_REGULAR_DELTA_ROUNDING;
        if ($applyTaxAfterDiscount) {
            $deltaRoundingType = self::KEY_TAX_BEFORE_DISCOUNT_DELTA_ROUNDING;
        }
        $rowTax = $this->roundAmount($rowTaxExact, $rate, true, $deltaRoundingType, $round, $item);
        $rowTotal = $rowTotalInclTax - $rowTax;
        $price = $rowTotal / $quantity;
        if ($round) {
            $price = $this->calculationTool->round($price);
        }

        //Handle discount
        if ($applyTaxAfterDiscount) {
            $taxableAmount = max($rowTotalInclTax - $discountAmount, 0);
            $rowTaxAfterDiscount = $this->calculationTool->calcTaxAmount(
                $taxableAmount,
                $rate,
                true,
                false
            );
            $rowTaxAfterDiscount = $this->roundAmount(
                $rowTaxAfterDiscount,
                $rate,
                true,
                self::KEY_REGULAR_DELTA_ROUNDING,
                $round,
                $item
            );
            // Set discount tax compensation
            $discountTaxCompensationAmount = $rowTax - $rowTaxAfterDiscount;
            $rowTax = $rowTaxAfterDiscount;
        }

        // Calculate applied taxes
        /** @var  \Magento\Tax\Api\Data\AppliedTaxInterface[] $appliedTaxes */
        $appliedRates = $this->calculationTool->getAppliedRates($getTaxRateObj['taxRateRequest']);
        $appliedTaxes = $this->getAppliedTaxes($rowTax, $rate, $appliedRates);

        return $this->taxDetailsItemDataObjectFactory->create()
            ->setCode($item->getCode())
            ->setType($item->getType())
            ->setRowTax($rowTax)
            ->setPrice($price)
            ->setPriceInclTax($priceInclTax)
            ->setRowTotal($rowTotal)
            ->setRowTotalInclTax($rowTotalInclTax)
            ->setDiscountTaxCompensationAmount($discountTaxCompensationAmount)
            ->setAssociatedItemCode($item->getAssociatedItemCode())
            ->setTaxPercent($rate)
            ->setAppliedTaxes($appliedTaxes);
    }

    public function i95DevAddressRateRequest()
    {
        return $this->getAddressRateRequest();
    }
}
