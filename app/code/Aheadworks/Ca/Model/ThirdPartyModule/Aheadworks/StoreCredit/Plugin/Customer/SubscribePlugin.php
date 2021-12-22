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
namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Plugin\Customer;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Model\StoreCreditManagement;

/**
 * Class SubscribePlugin
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Plugin\Customer
 */
class SubscribePlugin
{
    /**
     * @var StoreCreditManagement
     */
    private $storeCreditManagement;

    /**
     * @param StoreCreditManagement $storeCreditManagement
     */
    public function __construct(
        StoreCreditManagement $storeCreditManagement
    ) {
        $this->storeCreditManagement = $storeCreditManagement;
    }

    /**
     * Show block or not
     *
     * @param \Aheadworks\StoreCredit\Block\Customer\Subscribe $subject
     * @param \Closure $proceed
     * @return string
     */
    public function aroundCanShow($subject, $proceed)
    {
        $result = false;
        if ($this->storeCreditManagement->isAvailableSubscribeOptions()) {
            $result = $proceed();
        }

        return $result;
    }
}
