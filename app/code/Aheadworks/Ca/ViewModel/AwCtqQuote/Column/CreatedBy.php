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
namespace Aheadworks\Ca\ViewModel\AwCtqQuote\Column;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Class CreatedBy
 *
 * @package Aheadworks\Ca\ViewModel\AwCtqQuote\Column
 */
class CreatedBy implements ArgumentInterface
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
     * Retrieve customer name from quote
     *
     * @param \Aheadworks\Ctq\Api\Data\QuoteInterface $quote
     * @throws LocalizedException
     * @return string
     */
    public function getCreatedBy($quote)
    {
        try {
            $customer = $this->customerRepository->getById($quote->getCustomerId());
            $createdBy = $customer->getFirstname() . ' ' . $customer->getLastname();
        } catch (NoSuchEntityException $exception) {
            $createdBy = '';
        }

        return $createdBy;
    }
}
