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
 * @package    Ctq
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\Model\Quote\Permission;

/**
 * Class Checker
 * @package Aheadworks\Ctq\Model\Quote\Permission
 */
class Checker
{
    /**
     * @var array
     */
    private $checkers;

    /**
     * @param array $checkers
     */
    public function __construct(
        $checkers = []
    ) {
        $this->checkers = $checkers;
    }

    /**
     * Check allow customer to quote
     *
     * @param int $customerId
     * @param int $storeId
     * @return bool
     */
    public function check($customerId, $storeId)
    {
        $result = true;
        foreach ($this->checkers as $checker) {
            $result = $result && $checker->check($customerId, $storeId);
        }
        return $result;
    }
}
