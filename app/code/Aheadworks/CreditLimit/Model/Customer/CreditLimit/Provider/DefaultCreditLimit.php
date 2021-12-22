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
 * @package    CreditLimit
 * @version    1.0.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\CreditLimit\Model\Customer\CreditLimit\Provider;

use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\CreditLimit\Model\Service\CustomerGroupService;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Class DefaultCreditLimit
 *
 * @package Aheadworks\CreditLimit\Model\Customer\CreditLimit\Provider
 */
class DefaultCreditLimit implements ProviderInterface
{
    /**
     * @var CustomerGroupService
     */
    private $customerGroupService;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param CustomerGroupService $customerGroupService
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        CustomerGroupService $customerGroupService,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->customerGroupService = $customerGroupService;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @inheritdoc
     */
    public function getData($customerId, $websiteId)
    {
        try {
            $customer = $this->customerRepository->getById($customerId);
            $creditLimit = $this->customerGroupService->getCreditLimit(
                $customer->getGroupId(),
                $customer->getWebsiteId()
            );

            $data['can_use_default_credit_limit'] = $creditLimit ? true : false;
        } catch (NoSuchEntityException $exception) {
            $data['can_use_default_credit_limit'] = false;
        }

        return $data;
    }
}
