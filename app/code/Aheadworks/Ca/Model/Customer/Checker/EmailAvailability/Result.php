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
namespace Aheadworks\Ca\Model\Customer\Checker\EmailAvailability;

use Aheadworks\Ca\Api\Data\EmailAvailabilityResultInterface;

/**
 * Class Result
 *
 * @package Aheadworks\Ca\Model\Customer\Checker\EmailAvailability
 */
class Result implements EmailAvailabilityResultInterface
{
    /**
     * @var bool
     */
    private $isAvailableForCompany;

    /**
     * @var bool
     */
    private $isAvailableForCustomer;

    /**
     * @param bool $isAvailableForCompany
     * @param bool $isAvailableForCustomer
     */
    public function __construct(
        $isAvailableForCompany,
        $isAvailableForCustomer
    ) {
        $this->isAvailableForCompany = $isAvailableForCompany;
        $this->isAvailableForCustomer = $isAvailableForCustomer;
    }

    /**
     * @inheritdoc
     */
    public function isAvailableForCompany()
    {
        return $this->isAvailableForCompany;
    }

    /**
     * @inheritdoc
     */
    public function isAvailableForCustomer()
    {
        return $this->isAvailableForCustomer;
    }
}
