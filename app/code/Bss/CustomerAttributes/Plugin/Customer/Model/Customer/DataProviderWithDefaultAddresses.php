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
namespace Bss\CustomerAttributes\Plugin\Customer\Model\Customer;

/**
 * Class DataProviderWithDefaultAddresses
 */
class DataProviderWithDefaultAddresses
{
    const CUSTOMER_ADDRESS = 'customer_address';

    /**
     * @var \Bss\CustomerAttributes\Helper\CustomerAddress
     */
    protected $customerAddress;

    /**
     * @var \Magento\Eav\Api\AttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * @var \Bss\CustomerAttributes\Helper\Customerattribute
     */
    protected $helper;

    /**
     * DataProviderWithDefaultAddresses constructor.
     *
     * @param \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository
     * @param \Bss\CustomerAttributes\Helper\Customerattribute $helper
     */
    public function __construct(
        \Bss\CustomerAttributes\Helper\CustomerAddress $customerAddress,
        \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository,
        \Bss\CustomerAttributes\Helper\Customerattribute $helper
    ) {
        $this->customerAddress = $customerAddress;
        $this->attributeRepository = $attributeRepository;
        $this->helper = $helper;
    }

    /**
     * Convert data custom attribute address
     *
     * @param \Magento\Customer\Model\Customer\DataProviderWithDefaultAddresses $subject
     * @param array $result
     * @return array
     */
    public function afterGetData($subject, $result)
    {
        if ($this->helper->isEnable()) {
            $attributeAddress = $this->helper->converAddressCollectioin();
            foreach ($result as $key => $customer) {
                if (isset($customer["default_billing_address"])) {
                    $result[$key]["default_billing_address"]["custom_attributes_address"] =
                        $this->customerAddress->getDataCustomAddress($customer["default_billing_address"], $attributeAddress);
                }
                if (isset($customer["default_shipping_address"])) {
                    $result[$key]["default_shipping_address"]["custom_attributes_address"] =
                        $this->customerAddress->getDataCustomAddress($customer["default_shipping_address"], $attributeAddress);
                }
            }
        }
        return $result;
    }
}
