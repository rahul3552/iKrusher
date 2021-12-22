<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Ca
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ca\Model\Customer\CompanyUser;

use Aheadworks\Ca\Api\Data\CompanyUserInterfaceFactory;
use Magento\Customer\Api\Data\CustomerExtensionFactory;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Class ExtensionAttributesBuilder
 * @package Aheadworks\Ca\Model\Customer\CompanyUser
 */
class ExtensionAttributesBuilder
{
    /**
     * @var CustomerExtensionFactory
     */
    private $customerExtensionFactory;

    /**
     * @var CompanyUserInterfaceFactory
     */
    private $companyUserFactory;

    /**
     * @var array
     */
    private $builders;

    /**
     * @param CustomerExtensionFactory $customerExtensionFactory
     * @param CompanyUserInterfaceFactory $companyUserFactory
     * @param array builders
     */
    public function __construct(
        CustomerExtensionFactory $customerExtensionFactory,
        CompanyUserInterfaceFactory $companyUserFactory,
        $builders = []
    ) {
        $this->customerExtensionFactory = $customerExtensionFactory;
        $this->companyUserFactory = $companyUserFactory;
        $this->builders = $builders;
    }

    /**
     * Set extension attributes if not isset
     *
     * @param CustomerInterface $customer
     */
    public function setExtensionAttributesIfNotIsset($customer)
    {
        if (!$customer->getExtensionAttributes()) {
            $extensionAttributes = $this->customerExtensionFactory->create();
            $customer->setExtensionAttributes($extensionAttributes);
        }
    }

    /**
     * Set AW CompanyUser attributes if not isset
     *
     * @param CustomerInterface $customer
     */
    public function setAwCompanyUserIfNotIsset($customer)
    {
        $this->setExtensionAttributesIfNotIsset($customer);
        if (!$customer->getExtensionAttributes()->getAwCaCompanyUser()) {
            $awCompanyUser = $this->companyUserFactory->create();
            $customer->getExtensionAttributes()->setAwCaCompanyUser($awCompanyUser);
        }
    }

    /**
     * Set additional attributes
     *
     * @param CustomerInterface $customer
     */
    public function setAdditionalAttributes($customer)
    {
        foreach ($this->builders as $builder) {
            $customer = $builder->set($customer);
        }
    }
}
