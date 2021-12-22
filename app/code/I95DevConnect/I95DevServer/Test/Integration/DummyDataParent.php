<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_I95DevServer
 */

namespace I95DevConnect\I95DevServer\Test\Integration;

/**
 * shipment test case for reverse flows
 */
class DummyDataParent
{
    const SHIPPING_STR = "shipping";
    const FLATRATE_STR = "flatrate_flatrate";
    const TAX_PROD_SKU = "taxProductSku";
    const CUSTOM_PRICE = "custom_price";
    const DATE_FORMATE = 'Y-m-d H:i:s';
    const PHONE_NUMBER = '99658584545';
    const CUSTOMER_FNAME = "Hrusikesh";
    const CUSTOMER_LNAME = "Manna";

    public $customerFactory;
    public $customerAddressFactory;
    public $productFactory;
    public $quote;
    public $quoteManagement;
    public $quoteItemModel;
    public $quoteAddressInterfaceFactory;
    public $orderModel;
    public $stockRegistry;
    public $customSalesOrder;
    public $i95devServerRepo;
    public $erpMessageQueue;
    public $errorUpdateData;
    public $customSalesInvoice;
    public $eavAttribute;
    public $attributeOption;
    public $productAttributeOption;
    public $invoiceItem;
    public $invoiceRepo;
    public $customInvoice;
    public $baseHelperData;
    public $scopeConfig;
    public $typeConfigurableFactory;
    public $entityAttributeFactory;
    public $productResource;
    public $attributeCreate;
    public $quoteToOrder;
    public $orderStatusHistoryFactory;
    public $orderManagement;

    /**
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Model\AddressFactory $customerAddressFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Quote\Model\QuoteFactory $quote
     * @param \Magento\Quote\Api\CartManagementInterfaceFactory $quoteManagement
     * @param \Magento\Quote\Model\Quote\ItemFactory $quoteItemModel
     * @param \Magento\Quote\Api\Data\AddressInterfaceFactory $quoteAddressInterfaceFactory
     * @param \Magento\Sales\Model\Order $orderModel
     * @param \I95DevConnect\MessageQueue\Model\SalesOrderFactory $customSalesOrder
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \I95DevConnect\MessageQueue\Model\ChequeNumber $chequeNumberModel
     * @param \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\ConfigurableFactory $typeConfigurableFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\AttributeFactory $entityAttributeFactory
     * @param \Magento\Catalog\Model\ResourceModel\ProductFactory $productResource
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Product\Product\Reverse\Attribute $attributeCreate
     * @param \Magento\Quote\Api\CartManagementInterface $quoteToOrder
     * @param \Magento\Sales\Api\Data\OrderStatusHistoryInterfaceFactory $orderStatusHistoryFactory
     * @param \Magento\Sales\Api\OrderManagementInterface $orderManagement
     * @param \I95DevConnect\MessageQueue\Model\ErrorUpdateData $errorUpdateData
     *
     * @param \I95DevConnect\MessageQueue\Model\I95DevErpMQRepository $erpMessageQueue
     * @param \I95DevConnect\I95DevServer\Model\ServiceMethod\AbstractServiceMethod $abstractServiceMethod
     * @author Arushi Bansal
     */
    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\AddressFactory $customerAddressFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Quote\Model\QuoteFactory $quote,
        \Magento\Quote\Api\CartManagementInterfaceFactory $quoteManagement,
        \Magento\Quote\Model\Quote\ItemFactory $quoteItemModel,
        \Magento\Quote\Api\Data\AddressInterfaceFactory $quoteAddressInterfaceFactory,
        \Magento\Sales\Model\Order $orderModel,
        \I95DevConnect\MessageQueue\Model\SalesOrderFactory $customSalesOrder,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \I95DevConnect\MessageQueue\Model\ChequeNumber $chequeNumberModel,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\ConfigurableFactory $typeConfigurableFactory,
        \Magento\Eav\Model\ResourceModel\Entity\AttributeFactory $entityAttributeFactory,
        \Magento\Catalog\Model\ResourceModel\ProductFactory $productResource,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Product\Product\Reverse\Attribute $attributeCreate,
        \Magento\Quote\Api\CartManagementInterface $quoteToOrder,
        \Magento\Sales\Api\Data\OrderStatusHistoryInterfaceFactory $orderStatusHistoryFactory,
        \Magento\Sales\Api\OrderManagementInterface $orderManagement,
        \I95DevConnect\MessageQueue\Model\ErrorUpdateData $errorUpdateData,
        \I95DevConnect\MessageQueue\Model\I95DevErpMQRepository $erpMessageQueue,
        \I95DevConnect\I95DevServer\Model\ServiceMethod\AbstractServiceMethod $abstractServiceMethod
    ) {
        $this->customerFactory = $customerFactory;
        $this->customerAddressFactory = $customerAddressFactory;
        $this->productFactory = $productFactory;
        $this->quote = $quote;
        $this->quoteManagement = $quoteManagement;
        $this->quoteItemModel = $quoteItemModel;
        $this->quoteAddressInterfaceFactory = $quoteAddressInterfaceFactory;
        $this->orderModel = $orderModel;
        $this->customSalesOrder = $customSalesOrder;
        $this->stockRegistry = $stockRegistry;
        $this->chequeNumberModel = $chequeNumberModel;
        $this->typeConfigurableFactory = $typeConfigurableFactory;
        $this->entityAttributeFactory = $entityAttributeFactory;
        $this->productResource = $productResource;
        $this->attributeCreate = $attributeCreate;
        $this->quoteToOrder = $quoteToOrder;
        $this->orderStatusHistoryFactory = $orderStatusHistoryFactory;
        $this->orderManagement = $orderManagement;
        $this->errorUpdateData = $errorUpdateData;
        $this->erpMessageQueue = $erpMessageQueue;
        $this->abstractServiceMethod = $abstractServiceMethod;
    }

    /**
     * Create a dummy customer without address
     *
     * @return void
     * @throws \Exception
     * @author Debashis S. Gopal
     */
    public function createCustomerWithoutAddress()
    {
        $Fnames = self::CUSTOMER_FNAME;
        $Lnames = self::CUSTOMER_LNAME;
        $_customerEmail = 'hrusikesh.manna@jiva.com';
        $this->customer = $this->customerFactory->create();
        $this->customer->setWebsiteId(1)
            ->setEntityTypeId(1)
            ->setAttributeSetId(1)
            ->setEmail($_customerEmail)
            ->setPassword('password')
            ->setGroupId(1)
            ->setStoreId(1)
            ->setIsActive(1)
            ->setFirstname($Fnames)
            ->setLastname($Lnames)
            ->setRefName(60001)
            ->setTargetCustomerId('C00011');
        $this->customer->save();
        $this->customerId = $this->customer->getId();
    }

    /**
     * create dummy customer for test cases
     *
     * @author Arushi Bansal.Updated by Debashis, Used customerFactory instead of customer.
     */
    public function createCustomer()
    {
        $this->createCustomerWithoutAddress();
        $this->addCustomerAddress();
    }

    /**
     * Add address to customer
     *
     * @return void
     */
    public function addCustomerAddress()
    {
        $this->customerAddress = $this->customerAddressFactory->create();
        $this->customerAddress->setCustomerId($this->customerId)
            ->setTargetAddressId(2)
            ->setRefName(60001)
            ->setFirstname(self::CUSTOMER_FNAME)
            ->setLastname(self::CUSTOMER_LNAME)
            ->setCountryId('CA')
            ->setRegionId(66)
            ->setRegion('Alberta')
            ->setPostcode('GB-W1 3AL')
            ->setCity('London')
            ->setTelephone('0038511223344')
            ->setFax('0038511223355')
            ->setStreet("28 Baker Street")
            ->setIsDefaultBilling('1')
            ->setIsDefaultShipping('1')
            ->setSaveInAddressBook('1');
        $this->customerAddress->save();
        $this->addressId = $this->customerAddress->getId();
    }

    /**
     * set shipping address for customer
     * @return boolean
     * @author Hrusikesh Manna
     */
    public function setShippingAddress()
    {
        $customerId = self::$customerId;
        $entity_id = self::$addressId;
        $region_id = 0;
        $region = 'CA';
        $postcode = '88976';
        $street = 'street';
        $lastname = 'lastname';
        $city = 'city';
        $email = 'gghddd@mail.com';
        $telephone = '545546654';
        $firstname = 'firstname';
        $country_id = 'US';
        $address = $this->quoteAddressInterfaceFactory->create();
        $address->setCustomerId($customerId);
        $address->setCustomerAddressId($entity_id);
        $address->setRegionId($region_id);
        $address->setRegion($region);
        $address->setPostcode($postcode);
        $address->setStreet($street);
        $address->setLastname($lastname);
        $address->setCity($city);
        $address->setEmail($email);
        $address->setTelephone($telephone);
        $address->setFirstname($firstname);
        $address->setCountryId($country_id);
        $address->setAddressType(self::SHIPPING_STR);
        $this->_quoteModel->setShippingAddress($address);
        return true;
    }

    /**
     * create simple product for test case
     *
     * @param string $sku
     * @param array $attributeWithKey
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @author Arushi Bansal
     */
    public function createSingleSimpleProduct($sku, $attributeWithKey = [])
    {
        $sku = (string) $sku;
        // @hrusikesh Condition to check for vat tax order forward sync
        $attributes = [];
        if (!empty($attributeWithKey)) {
            $attributes = $this->attributeCreate->processAttributeWithKey($attributeWithKey);
        }
        $this->productSKU = $sku;
        $_product = $this->productFactory->create();
        $_product->setName($sku);
        $_product->setTypeId('simple');
        $_product->setAttributeSetId(4);
        $_product->setSku($sku);
        $_product->setWebsiteIds([1]);
        $_product->setVisibility(4);
        $_product->setPrice(10);
        $_product->setStatus(1);
        if (!empty($attributes)) {
            foreach ($attributes as $value) {
                $_product->setCustomAttribute($value["attributeCode"], $value["value"]);
            }
        }
        $_product->save();
        $this->productId = $_product->getId();

        $this->addInventory();
        return $this->productId;
    }

    /**
     * add inventory for writing invoice test cases
     * @author Arushi Bansal
     */
    public function addInventory()
    {
        $stockItem = $this->stockRegistry->getStockItem($this->productId);
        $stockItem->setData('is_in_stock', 1);
        $stockItem->setData('qty', 100);
        $stockItem->setData('manage_stock', 1);
        $stockItem->setData('use_config_notify_stock_qty', 1);
        $stockItem->setData('backorders', 1);
        $stockItem->save();
    }

    /**
     * create order for registered customer
     *
     * @param string $targetOrderId
     * @param boolean $isConfigurableProduct
     * @param array $requestData
     * @return \Magento\Sales\Api\Data\OrderInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    public function createSingleOrder($targetOrderId, $isConfigurableProduct = null, $requestData = [])
    {
        $this->customerObj = $this->customerFactory->create()->load($this->customerId);
        $this->_quoteModel = $this->quote->create();
        $this->_quoteModel->setStoreId(1);
        $this->_quoteModel->setCustomerId($this->customerId);
        $this->addAddressToQuote($requestData);
        $this->addItemToQuote($isConfigurableProduct, $requestData);
        $shippingAmount = 20;
        $this->_quoteModel->getShippingAddress()
            ->setShippingMethod(self::FLATRATE_STR)
            ->setShippingAmount($shippingAmount)
            ->setBaseShippingAmount($shippingAmount);
        $this->_quoteModel->getShippingAddress()->setCollectShippingRates(true);
        $this->_quoteModel->setTotalsCollectedFlag(false);
        $this->_quoteModel->collectTotals();
        $this->_quoteModel->setItemsCount(1);
        $this->_quoteModel->setItemsQty(2);
        $this->_quoteModel->setIsVirtual(0);
        $this->_quoteModel->setIsActive(1);
        $this->_quoteModel->setInventoryProcessed(false);
        $this->_quoteModel->setCustomerEmail($this->customerObj->getEmail());
        $this->_quoteModel->getPayment()->setMethod('cashondelivery');
        $objShippingAddress = $this->_quoteModel->getShippingAddress();
        $objShippingAddress->setShippingAmount(20);
        $objShippingAddress->setBaseShippingAmount(20);
        $this->_quoteModel->save();

        $objShippingAddress->setGrandTotal($objShippingAddress->getSubtotal() + $shippingAmount);
        $objShippingAddress->setBaseGrandTotal($objShippingAddress->getBaseSubtotal() + $shippingAmount);
        $this->_quoteModel->setGrandTotal($objShippingAddress->getSubtotal() + $shippingAmount);
        $this->_quoteModel->setBaseGrandTotal($objShippingAddress->getBaseSubtotal() + $shippingAmount);

        $this->_quoteModel->save();
        $quoteId = $this->_quoteModel->getId();
        if (!empty($requestData) && isset($requestData['discountAmount'])) {
            $this->addDiscount($requestData);
            $createdQuote = $this->quote->create()->load($quoteId);
            $this->order = $this->quoteToOrder->submit($createdQuote, []);

            $this->order->getGrandTotal();

            return $this->order;
        }

        $order = $this->quoteToOrder->submit($this->_quoteModel);

        $this->order = $this->orderModel->load($order->getId());

        $this->orderId = $this->order->getId();
        if (isset($requestData['comment'])) {
            $this->addComment($requestData['comment']);
        }
        $this->saveCustomOrderInformation($targetOrderId);

        return $this->order;
    }

    /**
     * Add shipping and billing address to quote object
     *
     * @param $requestData
     * @return void
     */
    public function addAddressToQuote($requestData)
    {
        $address = $this->quoteAddressInterfaceFactory->create();
        $address->setCustomerId($this->customerId);
        $address->setCustomerAddressId($this->addressId);
        $address->setRegionCode('NY');
        $address->setRegion('NY');
        $address->setPostcode('88976');
        $address->setStreet('Test street');
        $address->setLastname($this->customerObj->getLastname());
        $address->setCity('city');
        $address->setEmail($this->customerObj->getEmail());
        $address->setTelephone(self::PHONE_NUMBER);
        $address->setFirstname($this->customerObj->getFirstname());
        $address->setCountryId('US');
        $address->setAddressType("billing");
        $this->_quoteModel->setBillingAddress($address);
        if (!empty($requestData) && isset($requestData['isDifferentAddress'])) {
            $this->addShippingAddressToQuote();
            return;
        }
        $address->setAddressType(self::SHIPPING_STR);
        $this->_quoteModel->setShippingAddress($address);
    }

    /**
     * Add new shipping address to quote
     *
     * @return void
     */
    public function addShippingAddressToQuote()
    {
        $address = $this->quoteAddressInterfaceFactory->create();
        $address->setCustomerId($this->customerId);
        $address->setRegionCode('NY');
        $address->setRegion('NY');
        $address->setPostcode('90001');
        $address->setStreet('Test street new');
        $address->setLastname($this->customerObj->getLastname());
        $address->setCity('New York');
        $address->setEmail($this->customerObj->getEmail());
        $address->setTelephone(self::PHONE_NUMBER);
        $address->setFirstname($this->customerObj->getFirstname());
        $address->setCountryId('US');
        $address->setAddressType(self::SHIPPING_STR);
        $this->_quoteModel->setShippingAddress($address);
    }

    /**
     * Add Items(Simple/Configurable) to quote object
     *
     * @param boolean $isConfigurableProduct
     * @param array $requestData
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addItemToQuote($isConfigurableProduct, $requestData)
    {
        if (isset($requestData[self::TAX_PROD_SKU])) {
            $this->productSKU = $requestData[self::TAX_PROD_SKU];
        }
        $product = $this->productFactory->create()->loadByAttribute("sku", $this->productSKU);

        if ($isConfigurableProduct !== null) {
            $parentProduct = $this->typeConfigurableFactory->create()->getParentIdsByChild($product->getId());
            $parentId = isset($parentProduct[0]) ? $parentProduct[0] : null;
            $options = [];

            if ($parentId !== null) {
                $item = $this->productFactory->create()->load($parentId);
                $attributeId = $this->entityAttributeFactory->create()->getIdByCode('catalog_product', 'color');

                $poductReource = $this->productResource->create();
                $attribute = $poductReource->getAttribute('color');
                if ($attribute->usesSource()) {
                    $optionId = $attribute->getSource()->getOptionId('RED');
                }
                $options[$attributeId] = $optionId;
                $buyOptions = [
                    'qty' => 1,
                    'super_attribute' => $options,
                    '_processing_params' => []
                ];
                $this->buyOptions = new \Magento\Framework\DataObject($buyOptions);
                $this->_quoteModel->addProduct($item, $this->buyOptions);
            }
        } elseif (!empty($requestData) && isset($requestData[self::CUSTOM_PRICE])) {
            $item = $this->productFactory->create()->load($product->getId());
            $buyOptions = [
                'qty' => 2,
                self::CUSTOM_PRICE => $requestData[self::CUSTOM_PRICE],
                '_processing_params' => []
            ];
            $this->buyOptions = new \Magento\Framework\DataObject($buyOptions);
            $this->_quoteModel->addProduct($item, $this->buyOptions);
        } else {
            $product->reindex();
            $productObj = $this->productFactory->create()->load($product->getId());
            $item = $this->quoteItemModel->create()->setProduct($productObj);
            $item->setBaseCost(50);
            $item->setQty(2);
            $item->setPrice(50);
            $item->setOriginalPrice(50);
            $item->getProduct()->setIsSuperMode(true);
            $this->_quoteModel->addItem($item);
        }
    }

    /**
     * Add discount to quote
     *
     * @param array $requestData
     * @return void
     */
    public function addDiscount($requestData)
    {
        $discountAmount = $requestData['discountAmount'];
        $discountCoupon = $requestData['discountCoupon'];
        $objShippingAddress = $this->_quoteModel->getShippingAddress();
        $objShippingAddress->setDiscountDescription($discountCoupon);
        $objShippingAddress->addTotal(
            ['code' => $discountCoupon, 'title' => $discountCoupon, 'value' => -$discountAmount]
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
        $total = $objShippingAddress->getSubtotal();
        foreach ($this->_quoteModel->getAllItems() as $item) {
            $rate = ($item->getPrice() * $item->getQty()) / $total;
            $ratedisc = round($discountAmount * $rate, 2);
            $itemdiscountAmount = $ratedisc;

            //We apply discount amount based on the ratio between the GrandTotal and the RowTotal
            $item->setDiscountAmount($itemdiscountAmount);
            $item->setBaseDiscountAmount($itemdiscountAmount)->save();
        }

        $this->_quoteModel->save();
    }

    /**
     * Add comment to order
     *
     * @param string $comment
     * @return void
     */
    public function addComment($comment)
    {
        $commentData = $this->orderStatusHistoryFactory->create();
        $commentData->setComment($comment)->getIsVisibleOnFront();
        $this->orderManagement->addComment($this->orderId, $commentData);
    }

    /**
     * saving targetOrder details in customTable
     *
     * @param $targetOrderId
     * @throws \Exception
     * @author Arushi Bansal
     * @updatedBy Divya Koona. Removed setting gp_orderprocess_flag;
     */
    public function saveCustomOrderInformation($targetOrderId)
    {
        $customOrderModel = $this->customSalesOrder->create();
        $loadCustomOrder = $this->customSalesOrder->create()
            ->load($this->order->getIncrementId(), 'source_order_id');
        if ($loadCustomOrder->getId()) {
            $customOrderModel->setId($loadCustomOrder->getId());
        }
        $customOrderModel->setOrigin('NAV');
        $customOrderModel->setAdditionalInfo('');
        $customOrderModel->setCreatedAt(date(self::DATE_FORMATE));
        $customOrderModel->setUpdatedAt(date(self::DATE_FORMATE));
        $customOrderModel->setUpdateBy('ERP');
        $customOrderModel->setSourceOrderId($this->order->getIncrementId());
        $customOrderModel->setTargetOrderStatus('New');
        $customOrderModel->setTargetOrderId($targetOrderId);
        $customOrderModel->setUpdatedDt(date(self::DATE_FORMATE));
        $customOrderModel->save();
    }

    /**
     * Creating Order
     * @param $targetOrderId
     * @return \Magento\Sales\Model\Order
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     * @author Sravani Polu
     */
    public function createGuestOrder($targetOrderId)
    {
        $email = "jbutt@gmail.com";
        $this->quoteModel = $this->quote->create();
        $this->quoteModel->setStoreId(1);
        $address = $this->quoteAddressInterfaceFactory->create();
        $address->setRegionId(57);
        $address->setRegionCode('TX');
        $address->setRegion('Texas');
        $address->setPostcode('77082');
        $address->setStreet('Test street');
        $address->setFirstname("James");
        $address->setLastname("Butt");
        $address->setCity('Texas');
        $address->setEmail($email);
        $address->setTelephone(self::PHONE_NUMBER);
        $address->setCountryId('US');
        $address->setAddressType("billing");
        $this->quoteModel->setBillingAddress($address);
        $address->setAddressType(self::SHIPPING_STR);
        $this->quoteModel->setShippingAddress($address);
        $product = $this->productFactory->create()->loadByAttribute("sku", $this->productSKU);
        $product->reindex();
        $productObj = $this->productFactory->create()->load($product->getId());
        $item = $this->quoteItemModel->create()->setProduct($productObj);
        $item->setBaseCost(50);
        $item->setQty(2);
        $item->setPrice(50);
        $item->setOriginalPrice(50);
        $item->getProduct()->setIsSuperMode(true);
        $this->quoteModel->addItem($item);
        $this->quoteModel->getShippingAddress()
            ->setShippingMethod(self::FLATRATE_STR);
        $this->quoteModel->getShippingAddress()->setCollectShippingRates(true);
        $this->quoteModel->setTotalsCollectedFlag(false);
        $this->quoteModel->collectTotals();
        $this->quoteModel->getShippingAddress()
            ->getShippingRateByCode(self::FLATRATE_STR);
        $this->quoteModel->setUpdatedAt(date(self::DATE_FORMATE));
        $shippingAddress = $this->quoteModel->getShippingAddress();
        $rates = $shippingAddress->collectShippingRates()
            ->getGroupedAllShippingRates();
        foreach ($rates as $carrier) {
            foreach ($carrier as $rate) {
                if ($rate->getCode() == self::FLATRATE_STR) {
                    $rate->setPrice(20);
                }
            }
        }
        $this->quoteModel->collectTotals();
        $this->quoteModel->setItemsCount(1);
        $this->quoteModel->setItemsQty(2);
        $this->quoteModel->setIsVirtual(0);
        $this->quoteModel->setIsActive(1);
        $this->quoteModel->setInventoryProcessed(false);
        $this->quoteModel->setCustomerEmail($email);
        $this->quoteModel->getPayment()->setMethod('checkmo');
        $this->quoteModel->setCustomerIsGuest(1);
        $this->quoteModel->save();
        $quoteId = $this->quoteModel->getId();
        $orderId = $this->quoteManagement->create()->placeOrder($quoteId);
        $this->chequeNumberModel->setTargetChequeNumber('789455655')
            ->setSourceOrderId($orderId)
            ->save();
        $this->order = $this->orderModel->load($orderId);
        $this->orderId = $this->order->getId();
        $this->saveCustomOrderInformation($targetOrderId);
        return $this->order;
    }

    /**
     * Read data from json file
     * @createdBy Sravani Polu
     * @param $fileName
     * @return false|string
     */
    public function readJsonFile($fileName)
    {
        $path = realpath(dirname(__FILE__)) . $fileName;
        return file_get_contents($path);
    }

    /**
     * create Test Order With Tax
     * @param type $sku
     * @param type $customerId
     * @return object
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    public function createTestOrderWithTax($sku, $customerId)
    {
        $this->customerId = $customerId;
        $requestData = [];
        $requestProduct = [self::TAX_PROD_SKU => $sku];
        $this->customerId = $customerId;
        $this->customerObj = $this->customerFactory->create()->load($this->customerId);
        $this->_quoteModel = $this->quote->create();
        $this->_quoteModel->setStoreId(1);
        $this->_quoteModel->setCustomerId($this->customerId);
        $this->setAddressById();
        $this->addAddressToQuote($requestData);
        $this->addItemToQuote(null, $requestProduct);
        $this->_quoteModel->getShippingAddress()
            ->setShippingMethod(self::FLATRATE_STR);
        $this->_quoteModel->getShippingAddress()->setCollectShippingRates(true);
        $this->_quoteModel->setTotalsCollectedFlag(false);
        $this->_quoteModel->collectTotals();
        $this->_quoteModel->getShippingAddress()
            ->getShippingRateByCode(self::FLATRATE_STR);
        $this->_quoteModel->setUpdatedAt(date(self::DATE_FORMATE));
        $shippingAddress = $this->_quoteModel->getShippingAddress();
        $rates = $shippingAddress->collectShippingRates()
            ->getGroupedAllShippingRates();
        foreach ($rates as $carrier) {
            foreach ($carrier as $rate) {
                if ($rate->getCode() == self::FLATRATE_STR) {
                    $rate->setPrice(20);
                }
            }
        }
        $this->_quoteModel->collectTotals();
        $this->_quoteModel->setItemsCount(1);
        $this->_quoteModel->setItemsQty(2);
        $this->_quoteModel->setIsVirtual(0);
        $this->_quoteModel->setIsActive(1);
        $this->_quoteModel->setInventoryProcessed(false);
        $this->_quoteModel->setCustomerEmail($this->customerObj->getEmail());
        $this->_quoteModel->getPayment()->setMethod('cashondelivery');
        $this->_quoteModel->save();
        $quoteId = $this->_quoteModel->getId();
        $orderId = $this->quoteManagement->create()->placeOrder($quoteId);
        $this->order = $this->orderModel->load($orderId);
        $this->orderId = $this->order->getId();
        $this->saveCustomOrderInformation('2225111');

        return $this->order;
    }

    /**
     * Set Address By Customer Id
     * @throws \Exception
     * @author Hrusikesh Manna
     */
    public function setAddressById()
    {
        $this->_setAddress();
    }

    /**
     * Read error message from error Id
     * @param $targetOrderId
     * @return string
     */
    public function getTargetOrderStatus($targetOrderId)
    {
        $loadCustomOrder = $this->customSalesOrder->create()->load($targetOrderId, 'target_order_id');
        return $loadCustomOrder->getTargetOrderStatus();
    }

    public function _setAddress()
    {
        $this->_setAddress();
    }
}
