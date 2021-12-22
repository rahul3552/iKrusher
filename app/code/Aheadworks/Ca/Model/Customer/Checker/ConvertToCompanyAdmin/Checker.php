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
namespace Aheadworks\Ca\Model\Customer\Checker\ConvertToCompanyAdmin;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Checker
 * @package Aheadworks\Ca\Model\Customer\Checker\ConvertToCompanyAdmin
 */
class Checker
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->customerRepository = $customerRepository;
    }

    /**
     * Check if customer exists and available to convert
     *
     * @param string $email
     * @param int|null $website
     * @throws LocalizedException
     * @return boolean
     */
    public function check($email, $website = null)
    {
        $result = false;

        try {
            $customer = $this->customerRepository->get($email, $website);
            if (!$customer->getExtensionAttributes()->getAwCaCompanyUser()) {
                $result = true;
            }
        } catch (NoSuchEntityException $e) {
            $result = false;
        }

        return $result;
    }
}
