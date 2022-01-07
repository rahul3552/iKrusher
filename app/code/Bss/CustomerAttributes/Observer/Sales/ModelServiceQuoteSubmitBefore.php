<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_CustomerAttributes
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2021 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\CustomerAttributes\Observer\Sales;

use Magento\Framework\Event\Observer;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class ModelServiceQuoteSubmitBefore
 */
class ModelServiceQuoteSubmitBefore implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Bss\CustomerAttributes\Helper\Customerattribute
     */
    private $helper;
    /**
     * @var \Magento\Eav\Api\AttributeRepositoryInterface
     */
    private $attributeRepository;
    /**
     * @var Json
     */
    private $json;

    /**
     * PaymentInformationManagement constructor.
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Bss\CustomerAttributes\Helper\Customerattribute $helper
     * @param \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository
     * @param Json $json
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Bss\CustomerAttributes\Helper\Customerattribute $helper,
        \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository,
        Json $json
    ) {
        $this->request = $request;
        $this->helper = $helper;
        $this->attributeRepository = $attributeRepository;
        $this->json = $json;
    }
    /**
     * Execute observer
     *
     * @param Observer $observer
     * @return ModelServiceQuoteSubmitBefore
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        if ($this->helper->isEnable()) {
            $order = $observer->getEvent()->getOrder();
            $quote = $observer->getEvent()->getQuote();

            try {
                if ($quote->getBillingAddress()) {
                    $billingAddressAttributes = $quote->getBillingAddress()->getDataModel()->getCustomAttributes();
                    $addressAttributesArrays = [];
                    if (!empty($billingAddressAttributes)) {
                        foreach ($billingAddressAttributes as $item) {
                            if ($item->getValue() !== '' &&
                                $this->helper->isAddressShowInOrderDetail($item->getAttributeCode())) {
                                $addressAttribute = $this->attributeRepository
                                    ->get('customer_address', $item->getAttributeCode());
                                $addressAttributesArrays = $this->addDataAddress('billing', $addressAttribute, $item->getValue(), $addressAttributesArrays);
                            }
                        }
                    } else {
                        $customerBillingAddressAttributes = $quote->getBillingAddress()->getCustomerAddressAttribute();
                        if (!empty($customerBillingAddressAttributes)) {
                            foreach ($this->json->unserialize($customerBillingAddressAttributes) as $item) {
                                try {
                                    $attributeCode = trim($item['attribute_code'], '[]');
                                    if ($item['value'] !== '' &&
                                        $this->helper->isAddressShowInOrderDetail($attributeCode)) {
                                        $addressAttribute = $this->attributeRepository
                                            ->get('customer_address', $attributeCode);
                                        $addressAttributesArrays = $this->addDataAddress('billing', $addressAttribute, $item['value'], $addressAttributesArrays);
                                    }
                                } catch (\Exception $e) {
                                    continue;
                                }
                            }
                        }
                    }
                    if (!empty($addressAttributesArrays['billing'])) {
                        $order->getBillingAddress()
                            ->setCustomerAddressAttribute($this->json->serialize($addressAttributesArrays['billing']));
                    }
                }
                if (!$quote->isVirtual()) {
                    /* @var \Magento\Quote\Model\Quote $quote */
                    $shippingAddressAttributes = $quote->getShippingAddress()->getDataModel()->getCustomAttributes();
                    if (!empty($shippingAddressAttributes)) {
                        foreach ($shippingAddressAttributes as $item) {
                            if ($item->getValue() !== '' &&
                                $this->helper->isAddressShowInOrderDetail($item->getAttributeCode())) {
                                $addressAttribute = $this->attributeRepository
                                    ->get('customer_address', $item->getAttributeCode());
                                if ($addressAttribute->getFrontendInput() == "file" && $this->request->getParam("shipping_same_as_billing") == "on") {
                                    $addressAttributesArrays = $this->addDataAddressFile("shipping", $addressAttribute, $item->getValue(), $addressAttributesArrays);
                                } else {
                                    $addressAttributesArrays = $this->addDataAddress("shipping", $addressAttribute, $item->getValue(), $addressAttributesArrays);
                                }

                            }
                        }
                    } else {
                        $customerShippingAddressAttribute = $quote->getShippingAddress()->getCustomerAddressAttribute();
                        if (!empty($customerShippingAddressAttribute)) {
                            $addressAttributesArrays = [];
                            foreach ($this->json->unserialize($customerShippingAddressAttribute) as $item) {
                                $attributeCode = trim($item['attribute_code'], '[]');
                                try {
                                    if ($item['value'] !== '' &&
                                        $this->helper->isAddressShowInOrderDetail($attributeCode)) {
                                        $addressAttribute = $this->attributeRepository
                                            ->get('customer_address', $attributeCode);
                                        if ($addressAttribute->getFrontendInput() == "file" && $this->request->getParam("shipping_same_as_billing") == "on") {
                                            $addressAttributesArrays = $this->addDataAddressFile("shipping", $addressAttribute, $item["value"], $addressAttributesArrays);
                                        } else {
                                            $addressAttributesArrays = $this->addDataAddress("shipping", $addressAttribute, $item["value"], $addressAttributesArrays);
                                        }
                                    }
                                } catch (\Exception $e) {
                                    continue;
                                }
                            }
                        }
                    }
                    if (!empty($addressAttributesArrays['shipping'])) {
                        $order->getShippingAddress()->setCustomerAddressAttribute($this->json->serialize($addressAttributesArrays['shipping']));
                    }
                }
            } catch (\Exception $e) {
                throw $e;
            }
        }
        return $this;
    }

    /**
     * Add data address custom attributes, handle type file
     *
     * @param string $type
     * @param \Magento\Eav\Api\Data\AttributeInterface $addressAttribute
     * @param string $value
     * @param array $addressAttributesArrays
     * @return array
     */
    public function addDataAddressFile($type , $addressAttribute, $value, $addressAttributesArrays)
    {
        $attributeCode = $addressAttribute->getAttributeCode();
        if (isset($addressAttributesArrays["billing"]) && isset($addressAttributesArrays["billing"][$attributeCode])) {
            $addressAttributesArrays[$type][$attributeCode] = $addressAttributesArrays["billing"][$attributeCode];
        } else {
            $addressAttributesArrays = $this->AddDataAddress($type , $addressAttribute, $value, $addressAttributesArrays);
        }
        return $addressAttributesArrays;
    }

    /**
     * Add data address custom attributes, not handle type file
     *
     * @param \Magento\Eav\Api\Data\AttributeInterface $addressAttribute
     * @param string $valueAttribute
     * @param array $addressAttributesArrays
     * @return array
     */
    public function addDataAddress($type, $addressAttribute, $valueAttribute, $addressAttributesArrays)
    {
        $addressValue = $this->helper->getValueAddressAttributeForOrder(
            $addressAttribute,
            $valueAttribute
        );
        $value = [
            'value' => $addressValue,
            'label' => $addressAttribute->getFrontendLabel()
        ];
        if ($addressAttribute->getFrontendInput() == "file") {
            $value["valueOld"] = $valueAttribute;
        }
        $addressAttributesArrays[$type][$addressAttribute->getAttributeCode()] = $value;
        return $addressAttributesArrays;
    }

}
