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
 * @copyright  Copyright (c) 2020 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\CustomerAttributes\Plugin\Integration\Model\Metadata\Form;

/**
 * Class ValidateValue
 *
 * @package Bss\CustomerAttributes\Plugin\Model\Metadata\Form
 */
class ValidateValue extends \Bss\CustomerAttributes\Plugin\Model\Metadata\Form\ValidateValue
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $area;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Eav\Model\ConfigFactory
     */
    protected $eavAttribute;

    /**
     * @var \Bss\CustomerAttributes\Helper\B2BRegistrationIntegrationHelper
     */
    private $b2BRegistrationIntegration;

    /**
     * ValidateValue constructor.
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Bss\CustomerAttributes\Helper\Customerattribute $customerAttribute
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\State $area
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Eav\Model\ConfigFactory $eavAttributeFactory
     * @param \Bss\CustomerAttributes\Helper\B2BRegistrationIntegrationHelper $b2BRegistrationIntegration
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Bss\CustomerAttributes\Helper\Customerattribute $customerAttribute,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\State $area,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Eav\Model\ConfigFactory $eavAttributeFactory,
        \Bss\CustomerAttributes\Helper\B2BRegistrationIntegrationHelper $b2BRegistrationIntegration
    ) {
        parent::__construct(
            $request,
            $customerAttribute,
            $eavAttributeFactory
        );
        $this->eavAttribute = $eavAttributeFactory;
        $this->scopeConfig = $scopeConfig;
        $this->area = $area;
        $this->registry = $registry;
        $this->customerRepository = $customerRepository;
        $this->b2BRegistrationIntegration = $b2BRegistrationIntegration;
    }

    /**
     * Don't validate the required address attributes when creating B2B account...
     *
     * @param mixed $subject
     * @param array|bool $result
     * @return array|bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterValidateValue(
        $subject,
        $result
    ) {
        if ($this->customerAttribute->isDisableAttributeAddress($subject->getAttribute())) {
            return true;
        }

        if ($this->b2BRegistrationIntegration->isB2BRegistrationModuleEnabled()) {
            $attributeCode = $subject->getAttribute()->getAttributeCode();
            $page = $this->request->getFullActionName();
            if ($this->DontValidateRequireAddress($attributeCode)) {
                return true;
            }
            $attribute = $this->eavAttribute->create()
                ->getAttribute('customer', $attributeCode);
            if (isset($attribute)) {
                $usedInForms = $attribute->getUsedInForms();
                $enableCustomerAttribute = $this->scopeConfig->getValue(
                    'bss_customer_attribute/general/enable',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );

                if (in_array('is_customer_attribute', $usedInForms) && $attribute->getIsRequired()) {
                    $newB2bValue = "";
                    /* Backend Validate */
                    if ($this->area->getAreaCode() == "adminhtml") {
                        $params = $this->request->getParams();
                        $customer = $this->registry->registry('bss_customer');
                        if ($customer->getId()) {
                            $customerId = $customer->getId();
                            $oldData = $this->customerRepository->getById($customerId);
                            $oldB2b = $oldData->getCustomAttribute('b2b_activasion_status');
                            $oldB2bValue = $oldB2b ? $oldB2b->getValue() : "";
                            $newB2bValue = $customer->getCustomAttribute('b2b_activasion_status')->getValue();
                            if ((!$oldB2bValue || !$newB2bValue) && ($oldB2bValue != $newB2bValue)) {
                                return true;
                            }
                        }
                        if ($newB2bValue) {
                            /* B2b account */
                            if (!in_array('b2b_account_create', $usedInForms)) {
                                return true;
                            }
                        } else {
                            /* Normal account */
                            if (!in_array('customer_account_create_frontend', $usedInForms)) {
                                return true;
                            }
                        }
                    }


                    if ((!in_array('b2b_account_create', $usedInForms)
                            || !$enableCustomerAttribute) && $page == 'btwob_account_createpost') {
                        return true;
                    }
                    if ((!in_array('b2b_account_edit', $usedInForms)
                            || !$enableCustomerAttribute) && $page == 'customer_account_editPost') {
                        return true;
                    }
                    if (!in_array('b2b_account_create', $usedInForms)
                        || !in_array('customer_account_create_frontend', $usedInForms)) {
                        return true;
                    }
                }
            }

            return $result;
        }

        return parent::afterValidateValue($subject, $result);
    }

    /**
     * Don't validate the required address attributes when creating B2B account.
     *
     * @param string $attributeCode
     * @return bool
     */
    public function DontValidateRequireAddress($attributeCode): bool
    {
        if ($this->request->getFullActionName() == "btwob_account_createpost") {
            $addressCollection = $this->customerAttribute->getAddressCollection();
            if($addressCollection->getSize()) {
                foreach ($addressCollection as $address) {
                    if($address->getAttributeCode() == $attributeCode) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
}
