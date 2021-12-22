<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_NetTerm
 */

namespace I95DevConnect\NetTerms\Test\Integration\NetTerms;

use Magento\Customer\Model\AddressFactory;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Attribute\SetFactory;
use Magento\Eav\Setup\EavSetupFactory;

/**
 * Helper class for Net Term test cases
 */
class Helper
{

    public $eavSetupFactory;
    public $eavConfig;
    public $attributeSetFactory;
    public $customerFactory;
    public $customerAddressFactory;

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        Config $eavConfig,
        SetFactory $attributeSetFactory,
        CustomerFactory $customerFactory,
        AddressFactory $customerAddressFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->customerFactory = $customerFactory;
        $this->customerAddressFactory = $customerAddressFactory;
    }

    /**
     * function for create customer custom attribute
     * @author Hrusikesh Manna
     */
    public function createEavAttribute()
    {
        $customerSetup = $this->eavSetupFactory->create();
        $customerEntity = $customerSetup->getEntityTypeId('customer');
        $attributeSetId = $customerSetup->getDefaultAttributeSetId($customerEntity);

        /** @var $attributeSet AttributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);
        $attributeCode = 'net_terms_id';

        $customerSetup->addAttribute(Customer::ENTITY, $attributeCode, [
            'type' => 'text',
            'label' => 'NetTerms ID',
            'input' => 'text',
            'required' => false,
            'visible' => false,
            'user_defined' => false,
            'sort_order' => 30,
            'is_used_in_grid' => false,
            'is_visible_in_grid' => false,
            'is_filterable_in_grid' => false,
            'is_searchable_in_grid' => false
        ]);

        $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, $attributeCode)
                ->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
            'used_in_forms' => ['adminhtml_customer'],
                ]);

        $attribute->save();
        unset($customerSetup);
    }

    /**
     * Read data from json file
     * @param type $fils
     * @return string
     */
    public function readJsonData($fils)
    {
        $path = realpath(dirname(__FILE__)) . "/Json/" . $fils;
        return(file_get_contents($path));
    }

    /**
     * create dummy customer for test cases
     *
     * @author Arushi Bansal.Updated by Debashis, Used customerFactory instead of customer.
     */
    public function createCustomer()
    {
        $Fnames = "Hrusikesh";
        $Lnames = "Manna";
        $_customerEmail = 'hrusikesh.manna11122@jiva.com';
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
            ->setTargetCustomerId('C00011')
            ->setNetTermsId('ntr-test1');
        $this->customer->save();
        $this->customerId = $this->customer->getId();
         file_put_contents('zzzxc.txt', $this->customer->getNetTermsId());
        $this->addCustomerAddress();
    }

    /**
     * Add address to customer
     *
     * @return void
     */
    public function addCustomerAddress()
    {
        $Fnames = "Hrusikesh";
        $Lnames = "Manna";
        $this->customerAddress = $this->customerAddressFactory->create();
        $this->customerAddress->setCustomerId($this->customerId)
            ->setTargetAddressId(2)
            ->setRefName(60001)
            ->setFirstname($Fnames)
            ->setLastname($Lnames)
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
}
