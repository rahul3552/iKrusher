<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order;

/**
 * Class for setting quote informations to a quote.
 */
class QuoteManagement extends AbstractOrder
{
    const I95_OBSERVER_SKIP='i95_observer_skip';
    const DISCOUNT='discount';

    /**
     *
     * @var \Magento\Quote\Model\QuoteFactory
     */
    public $quoteFactory;

    /**
     *
     * @var Data
     */
    public $dataHelper;

    /**
     *
     * @var \I95DevConnect\MessageQueue\Model\SalesOrderFactory
     */
    public $customSalesOrder;

    /**
     *
     * var  \Magento\Customer\Api\Data\CustomerInterface
     */
    public $customerApiData;

    /**
     *
     * @var \Magento\Quote\Model\Quote
     */
    public $quoteModel;

    /**
     * var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     *
     * var \Magento\Quote\Api\Data\CurrencyInterface
     */
    public $currencyApiInterface;

    /**
     *
     * @var \Magento\Customer\Api\Data\CustomerInterface[]
     */
    public $customer;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $magentoCustomerModel;

    /**
     *
     * @param \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger
     * @param \I95DevConnect\MessageQueue\Helper\Generic $genericHelper
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     * @param \Magento\Customer\Model\CustomerFactory $magentoCustomerModel
     * @param \Magento\Customer\Api\Data\CustomerInterface $customerApiData
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Quote\Api\Data\CurrencyInterface $currencyApiInterface
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Validate $validate
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger,
        \I95DevConnect\MessageQueue\Helper\Generic $genericHelper,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper,
        \Magento\Customer\Model\CustomerFactory $magentoCustomerModel,
        \Magento\Customer\Api\Data\CustomerInterface $customerApiData,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Quote\Api\Data\CurrencyInterface $currencyApiInterface,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Validate $validate
    ) {
        $this->quoteFactory = $quoteFactory;
        $this->dataHelper = $dataHelper;
        $this->magentoCustomerModel = $magentoCustomerModel;
        $this->customerApiData = $customerApiData;
        $this->storeManager = $storeManager;
        $this->currencyApiInterface = $currencyApiInterface;
        parent::__construct($logger, $genericHelper, $validate);
    }

    /**
     * Set storeId and currency to quote
     *
     * @author Debashis S. Gopal
     */
    public function setStoreDetails()
    {
        $storeId = $this->storeManager->getDefaultStoreView()->getStoreId();
        $currencyEntity = $this->setCurrency();
        $this->quoteModel->setStoreId($storeId);
        $this->quoteModel->setCurrency($currencyEntity);
    }

    /**
     * Get customer data using targetCustomerId and Initialize customer object.
     *
     * @author Debashis S. Gopal
     * @param string $targetCustomerId
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerData($targetCustomerId)
    {
        $customerCollection = $this->magentoCustomerModel->create()->getCollection()
                ->addAttributeToFilter('target_customer_id', $targetCustomerId);
        $customerCollection->getSelect()->limit(1);
        if ($customerCollection->getSize() > 0) {
            $this->customer = $customerCollection->getFirstItem();
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(__('i95dev_order_030'));
        }
    }

    /**
     * Sets store currency data to quote
     *
     * @return \Magento\Quote\Api\Data\CurrencyInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @author Debashis S. Gopal
     */
    public function setCurrency()
    {
        $currencyCode = $this->storeManager->getStore()->getCurrentCurrencyCode();
        $currencyRate = $this->storeManager->getStore()->getCurrentCurrencyRate();
        $currencyInterface = $this->currencyApiInterface;
        $currencyInterface->setGlobalCurrencyCode($currencyCode);
        $currencyInterface->setBaseCurrencyCode($currencyCode);
        $currencyInterface->setStoreCurrencyCode($currencyCode);
        $currencyInterface->setQuoteCurrencyCode($currencyCode);
        $currencyInterface->setStoreToBaseRate($currencyRate);
        $currencyInterface->setStoreToQuoteRate($currencyRate);
        $currencyInterface->setBaseToGlobalRate($currencyRate);
        $currencyInterface->setBaseToQuoteRate($currencyRate);
        return $currencyInterface;
    }

    /**
     * Add discount in quote items
     *
     * @param array $discountEntity
     */
    public function setDiscountForQuoteItems($discountEntity)
    {
        $discountAmount = 0;

        foreach ($discountEntity as $eachDiscount) {
            if ($eachDiscount['discountType'] == self::DISCOUNT) {
                $discountAmount = $eachDiscount['discountAmount'];
            }
        }
        $objShippingAddress = $this->quoteModel->getShippingAddress();
        $total = $objShippingAddress->getSubtotal();
        if ($discountAmount > 0 && $total > 0) {
            $objShippingAddress->setDiscountDescription('ERP Discount');
            $objShippingAddress->addTotal(
                ['code' => self::DISCOUNT, 'title' => "Custom Discount", 'value' => -$discountAmount]
            );
            $totalDiscountAmount = $discountAmount;
            $subtotalWithDiscount = $objShippingAddress->getSubtotal() - $discountAmount;
            $baseTotalDiscountAmount = $discountAmount;
            $baseSubtotalWithDiscount = $objShippingAddress->getBaseSubtotal() - $baseTotalDiscountAmount;
            $objShippingAddress->setDiscountAmount(-$totalDiscountAmount);
            $objShippingAddress->setSubtotalWithDiscount($subtotalWithDiscount);
            $objShippingAddress->setBaseDiscountAmount(-$baseTotalDiscountAmount);
            $objShippingAddress->setBaseSubtotalWithDiscount($baseSubtotalWithDiscount);
            $objShippingAddress->setGrandTotal($objShippingAddress->getGrandTotal() - $totalDiscountAmount);
            $objShippingAddress->setBaseGrandTotal($objShippingAddress->getBaseGrandTotal() - $baseTotalDiscountAmount);
            $lineDiscount = $this->getERPLineItemsDiscounts();
            foreach ($this->quoteModel->getAllItems() as $item) {
                //We apply discount amount based on the ratio between the GrandTotal and the RowTotal
                $rate = ($item->getPrice() * $item->getQty()) / $total;
                $ratedisc = round($discountAmount * $rate, 2);
                if (array_key_exists($item->getId(), $lineDiscount)) {
                    $itemdiscountAmount = $lineDiscount[$item->getId()];
                } else {
                    $itemdiscountAmount = $ratedisc;
                }
                $item->setDiscountAmount($itemdiscountAmount);
                $item->setBaseDiscountAmount($itemdiscountAmount)->save();
            }
        } else {
            $objShippingAddress->setDiscountAmount(0);
            $objShippingAddress->setBaseDiscountAmount(0);
            foreach ($this->quoteModel->getAllItems() as $item) {
                $item->setDiscountAmount(0);
                $itembasediscountamount = 0;
                $item->setBaseDiscountAmount($itembasediscountamount)->save();
            }
        }
    }

    /**
     * Retrieve line item level discount from erp DATA string
     *
     * @author By Arushi Bansal
     * @return array
     */
    public function getERPLineItemsDiscounts()
    {
        $lineDiscount = [];
        foreach ($this->quoteModel->getAllItems() as $item) {
            $discountAmount = 0;
            foreach ($this->stringData['orderItems'] as $itemData) {
                if ($this->checkIfDiscountKeyExists($itemData, $item)) {
                    $discountEntity = $this->dataHelper->getValueFromArray(self::DISCOUNT, $itemData);
                    foreach ($discountEntity as $eachDiscount) {
                        if ($eachDiscount['discountType'] == self::DISCOUNT) {
                            $discountAmount += $eachDiscount['discountAmount'];
                        }
                    }
                    $lineDiscount[$item->getId()] = $discountAmount;
                }
            }
        }
        return $lineDiscount;
    }

    /**
     * @param $itemData
     * @param $item
     * @return bool
     */
    public function checkIfDiscountKeyExists($itemData, $item)
    {
        return (strtolower(trim($itemData['sku'])) == strtolower(trim($item->getSku()))) &&
        array_key_exists(self::DISCOUNT, $itemData);
    }
    /**
     * Adding customer details to quote.
     * @author Debashis S. Gopal
     */
    public function setCustomerDetails()
    {
        $customerData = $this->customer->getData();
        $customerObj = $this->customerApiData;
        $customerObj->setId($customerData['entity_id']);
        foreach ($customerData as $key => $data) {
            if (!is_array($data)) {
                $customerObj->setData($key, $data);
            }
        }
        $this->quoteModel->setCustomer($customerObj);
        $this->quoteModel->setCustomerId($customerData['entity_id']);
        $this->quoteModel->setCustomerEmail($customerData['email']);
    }

    /**
     * Add shipping address and billing address to quote
     * @author Debashis S. Gopal
     */
    public function setAddressDetails()
    {
        $billingAddress = $this->orderBillingAddress->addBillingAddress();
        $shippingAddress = $this->orderShippingAddress->addShippingAddress();
        $this->quoteModel->setShippingAddress($shippingAddress);
        $this->quoteModel->getBillingAddress()->addData($billingAddress);
    }

    /**
     * Adding Shipping method and shippig amount.
     * @author Debashis S. Gopal
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setShippingDetails()
    {
        $this->quoteModel->getShippingAddress()
                ->setShippingMethod($this->stringData['shippingMethod'])
                ->setShippingAmount($this->shippingAmount)
                ->setBaseShippingAmount($this->shippingAmount);
        $this->quoteModel->getShippingAddress()->setCollectShippingRates(true);
        $this->quoteModel->setTotalsCollectedFlag(false);
        $this->quoteModel->collectTotals();
        $this->quoteModel->getShippingAddress()->setCollectShippingRates(true);
        $this->quoteModel->getShippingAddress()->collectShippingRates();
        $shippingMethodRates = $this->quoteModel->getShippingAddress()
            ->getShippingRateByCode($this->stringData['shippingMethod']);
        if (!is_object($shippingMethodRates)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __("i95dev_quote_invalid_shippingMethod")
            );
        }
    }

    /**
     * Deactivate the quote once it is converted to order.
     * @author Debashis S. Gopal
     *
     * @param \Magento\Quote\Model $createdQuote
     */
    public function deactivateQuote($createdQuote)
    {
        $createdQuote->setActive(0);
        $this->dataHelper->unsetGlobalValue(self::I95_OBSERVER_SKIP);
        $this->dataHelper->setGlobalValue(self::I95_OBSERVER_SKIP, true);
        $createdQuote->save();
        $this->dataHelper->unsetGlobalValue(self::I95_OBSERVER_SKIP);
    }

    /**
     * Update Totals with shipping amount.
     * @author Debashis S. Gopal
     *
     * @param \Magento\Quote\Model\Quote\Address $objShippingAddress
     */
    public function updateShippingAmountInTotal($objShippingAddress)
    {
        $objShippingAddress->setGrandTotal($objShippingAddress->getSubtotal() + $this->shippingAmount);
        $objShippingAddress->setBaseGrandTotal($objShippingAddress->getBaseSubtotal() + $this->shippingAmount);
        $this->quoteModel->setGrandTotal($objShippingAddress->getSubtotal() + $this->shippingAmount);
        $this->quoteModel->setBaseGrandTotal($objShippingAddress->getBaseSubtotal() + $this->shippingAmount);
    }
}
