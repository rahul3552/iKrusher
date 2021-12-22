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
 * @package    OneStepCheckout
 * @version    1.7.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier\Attribute;

use Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier\ModifierInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Class Firstname
 * @package Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier\Attribute
 */
class Firstname implements ModifierInterface
{
    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param CustomerSession $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        CustomerSession $customerSession,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function modify($metadata, $addressType)
    {
        if ($this->customerSession->isLoggedIn()) {
            $metadata['default'] = $this->customerRepository
                ->getById($this->customerSession->getCustomerId())
                ->getFirstname();
        }
        return $metadata;
    }
}
