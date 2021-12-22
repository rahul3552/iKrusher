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

use Aheadworks\CreditLimit\Api\Data\TransactionInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface ProviderInterface
 *
 * @package Aheadworks\CreditLimit\Model\Customer\CreditLimit\Provider
 */
interface ProviderInterface
{
    /**
     * Prepare credit limit data for specified customer
     *
     * @param int $customerId
     * @param int $websiteId
     * @return array
     * @throws LocalizedException
     */
    public function getData($customerId, $websiteId);
}
