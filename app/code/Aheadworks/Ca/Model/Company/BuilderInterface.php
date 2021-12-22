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
namespace Aheadworks\Ca\Model\Company;

use Aheadworks\Ca\Api\Data\CompanyInterface;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Interface BuilderInterface
 * @package Aheadworks\Ca\Model\Company
 */
interface BuilderInterface
{
    /**
     * @param CompanyInterface $company
     * @param CustomerInterface $customer
     * @return void
     */
    public function create($company, $customer);
}
