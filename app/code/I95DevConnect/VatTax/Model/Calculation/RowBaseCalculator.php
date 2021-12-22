<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax
 */

namespace I95DevConnect\VatTax\Model\Calculation;

use Magento\Tax\Api\Data\QuoteDetailsItemInterface;
use Magento\Tax\Api\Data\AppliedTaxInterfaceFactory;
use Magento\Tax\Api\Data\AppliedTaxRateInterfaceFactory;
use Magento\Tax\Api\Data\TaxDetailsItemInterfaceFactory;
use Magento\Tax\Api\TaxClassManagementInterface;
use Magento\Tax\Model\Calculation;

/**
 * Class for calulating Row Base tax price
 */
class RowBaseCalculator extends \Magento\Tax\Model\Calculation\RowBaseCalculator
{
    /**
     * Custom tax calculation model
     * @var \I95DevConnect\VatTax\Model\TaxCalculation
     */
    protected $customTax;

    /**
     * Quote session object
     *
     * @var \Magento\Backend\Model\Session\Quote
     */
    public $session;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    public $checkoutSession;

    /**
     * @var CustomerModelSession
     */
    public $customerSession;

    public $calculationTool;
    public $config;
    public $storeId;

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
     * @param \I95DevConnect\VatTax\Model\TaxCalculation $taxCaluculation
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\App\State $appState
     * @param \I95DevConnect\VatTax\Helper\Data $helperData
     * @param \Magento\Customer\Model\Session $customerSession
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
        \I95DevConnect\VatTax\Model\TaxCalculation $taxCaluculation,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\State $appState,
        \I95DevConnect\VatTax\Helper\Data $helperData,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\DataObject $addressRateRequest = null
    ) {
        $this->session = $quoteSession;
        $this->customerSession = $customerSession;
        $this->customTax = $taxCaluculation;
        $this->checkoutSession = $checkoutSession;
        $this->appState = $appState;
        $this->helperData = $helperData;

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
        $customerId = $this->customerId;
        if ($customerId == "") {
            return parent::calculateWithTaxNotInPrice($item, $quantity, $round = true);
        }

        $taxRateRequest = $this->getAddressRateRequest()->setProductClassId(
            $this->taxClassManagement->getTaxClassId($item->getTaxClassKey())
        );

        return $this->helperData->calculateWithTaxNotInPrice(
            $this,
            $taxRateRequest,
            $item,
            $quantity,
            $round,
            $this->storeId
        );
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
        
        if ($this->customerId == "") {
            return parent::calculateWithTaxInPrice($item, $quantity, $round = true);
        }

        $taxRateRequest = $this->getAddressRateRequest()->setProductClassId(
            $this->taxClassManagement->getTaxClassId($item->getTaxClassKey())
        );

        $sku = $item->getSku();
        if ($sku) {
            $rate = $storeRate = $this->customTax->getTax($sku, $customerId);
        } else {
            return parent::calculateWithTaxInPrice($item, $quantity, $round = true);
        }

        // Calculate $priceInclTax
        $calculatePriceInclTax = $this->helperData->calculatePriceInclTax(
            $this,
            $item,
            $rate,
            $storeRate,
            $round,
            $quantity
        );
        $discountTaxCompensationAmount = $calculatePriceInclTax['discountTaxCompensationAmount'];
        $price = $calculatePriceInclTax['price'];
        $rowTax = $calculatePriceInclTax['rowTax'];
        $priceInclTax = $calculatePriceInclTax['priceInclTax'];

        // Calculate applied taxes
        /** @var  \Magento\Tax\Api\Data\AppliedTaxInterface[] $appliedTaxes */
        $appliedRates = $this->calculationTool->getAppliedRates($taxRateRequest);
        $appliedTaxes = $this->getAppliedTaxes($rowTax, $rate, $appliedRates);

        return $this->taxDetailsItemDataObjectFactory->create()
            ->setCode($item->getCode())
            ->setType($item->getType())
            ->setRowTax($rowTax)
            ->setPrice($price)
            ->setPriceInclTax($priceInclTax)
            ->setRowTotal($price * $quantity)
            ->setRowTotalInclTax($priceInclTax * $quantity)
            ->setDiscountTaxCompensationAmount($discountTaxCompensationAmount)
            ->setAssociatedItemCode($item->getAssociatedItemCode())
            ->setTaxPercent($rate)
            ->setAppliedTaxes($appliedTaxes);
    }

    public function gettaxDetailsItemDataObj()
    {
        return $this->taxDetailsItemDataObjectFactory->create();
    }

    public function calculateAppliedTax($unitTaxAfterDiscount, $quantity, $appliedRate)
    {
        return $this->getAppliedTax(
            $unitTaxAfterDiscount * $quantity,
            $appliedRate
        );
    }

    public function taxRoundAmount(
        $unitTaxPerRate,
        $taxId,
        $deltaRoundingType,
        $round,
        $item
    ) {
        return $this->roundAmount(
            $unitTaxPerRate,
            $taxId,
            false,
            $deltaRoundingType,
            $round,
            $item
        );
    }

    public function getCustomTax($sku, $customerId)
    {
        return $this->customTax->getTax($sku, $customerId);
    }
}
