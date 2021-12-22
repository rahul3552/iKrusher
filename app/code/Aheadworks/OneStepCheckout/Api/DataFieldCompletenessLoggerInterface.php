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
namespace Aheadworks\OneStepCheckout\Api;

/**
 * Interface DataFieldCompletenessLoggerInterface
 * @package Aheadworks\OneStepCheckout\Api
 */
interface DataFieldCompletenessLoggerInterface
{
    /**
     * Log field completeness data
     *
     * @param int $cartId
     * @param \Aheadworks\OneStepCheckout\Api\Data\DataFieldCompletenessInterface[] $fieldCompleteness
     * @return void
     */
    public function log($cartId, array $fieldCompleteness);
}
